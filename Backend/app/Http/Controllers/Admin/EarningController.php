<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\AdminGetCommission;
use App\Models\AstrologerModel\WithdrawRequest;
use App\Models\UserModel\UserOrder;
use App\Models\UserModel\UserWallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use PDF;
use Response;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class EarningController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getEarning(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $astrologerEarning = UserOrder::join('users as us', 'us.id', '=', 'order_request.userId')
                    ->where('astrologerId', '=', $request->id)
                    ->where('orderType', '!=', 'astromall')
                    ->select('us.name as userName', 'order_request.*')
                    ->orderby('id', 'DESC');

                $astrologerEarningCount = DB::table('order_request')
                    ->join('users', 'users.id', '=', 'order_request.userId')
                    ->where('astrologerId', '=', $request->id)
                    ->where('orderType', '!=', 'astromall')
                    ->count();
                $astrologerEarning = $astrologerEarning->skip($paginationStart);
                $astrologerEarning = $astrologerEarning->take($this->limit);
                $astrologerName = DB::Table('astrologers')
                    ->where('id', '=', $request->id)
                    ->select('name')
                    ->get();

                // Clone query for counting records
                $countQuery = clone $astrologerEarning;

                // Remove groupBy from count query
                $countQuery->selectRaw('COUNT(DISTINCT admin_get_commissions.id) as totalRecords');
                $searchString = $request->searchString ?? null;
                if ($searchString) {
                    $astrologerEarning->where(function ($q) use ($searchString) {
                        $q->where('us.name', 'LIKE', '%' . $searchString . '%');
                        // ->orWhere('us.contactNo', 'LIKE', '%' . $searchString . '%')
                        // ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                        // ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%');
                    });

                    // Apply search filters to count query
                    $countQuery->where(function ($q) use ($searchString) {
                        $q->where('us.name', 'LIKE', '%' . $searchString . '%');
                        // ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                        // ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                        // ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%');
                    });
                }

                $orderType = $request->orderType ?? null;
                if ($orderType) {
                    $astrologerEarning->where('order_request.orderType', $orderType);
                    $countQuery->where('order_request.orderType', $orderType);
                }

                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $astrologerEarning->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $astrologerEarning->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $astrologerEarning->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
                }

                $astrologerEarning = $astrologerEarning->get();

                if ($astrologerEarning && count($astrologerEarning) > 0) {
                    foreach ($astrologerEarning as $earning) {
                        $earning->charge = $earning->totalMin > 0 ? $earning->totalPayable / $earning->totalMin : 0;
                        $earning->astrologerName = $astrologerName[0]->name;
                    }
                }
                $totalPages = ceil($astrologerEarningCount / $this->limit);
                $astrologerId = $request->id;
                $totalRecords = $astrologerEarningCount;
                if ($astrologerEarning && count($astrologerEarning) > 0) {
                    foreach ($astrologerEarning as $earning) {
                        if ($earning->totalMin > 0) {
                            $earning->charge = $earning->totalPayable / $earning->totalMin;
                        }
                    }
                }
                // Get currency symbol
                $currency = DB::table('systemflag')->where('name', 'currencySymbol')->select('value')->first();
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.earning', compact('astrologerEarning', 'astrologerId', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'currency', 'from_date', 'to_date'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function printPdf(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astrologerEarning = DB::table('order_request')
                    ->join('users', 'users.id', '=', 'order_request.userId')
                    ->where('astrologerId', '=', $req->id)
                    ->where('orderType', '!=', 'astromall')
                    ->select('users.name as userName', 'order_request.*')
                    ->orderby('id', 'DESC')
                    ->get();
                if ($astrologerEarning && count($astrologerEarning) > 0) {
                    $astrologerName = DB::Table('astrologers')
                        ->where('id', '=', $req->id)
                        ->select('name')
                        ->get();
                    foreach ($astrologerEarning as $earning) {
                        $earning->charge = $earning->totalMin > 0 ? $earning->totalPayable / $earning->totalMin : 0;
                        $earning->astrologerName = $astrologerName[0]->name;
                    }
                }
                $data = [
                    'title' => 'Earning Report',
                    'date' => Carbon::now()->format('d-m-Y h:i'),
                    'astrologerEarning' => $astrologerEarning,
                    'astrologerName' => $astrologerEarning[0]->astrologerName,
                ];
                $pdf = PDF::loadView('pages.astrologer-earning-report', $data);
                return $pdf->download('astrologerEarning.pdf');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function exportAstrologerEarningCSV(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astrologerEarning = DB::table('order_request')
                    ->join('users', 'users.id', '=', 'order_request.userId')
                    ->where('astrologerId', '=', $request->id)
                    ->where('orderType', '!=', 'astromall')
                    ->select('users.name as userName', 'order_request.*')
                    ->orderby('id', 'DESC')
                    ->get();
                if ($astrologerEarning && count($astrologerEarning) > 0) {
                    $astrologerName = DB::Table('astrologers')
                        ->where('id', '=', $request->id)
                        ->select('name')
                        ->get();
                    foreach ($astrologerEarning as $earning) {
                        $earning->charge = $earning->totalMin > 0 ? $earning->totalPayable / $earning->totalMin : 0;
                        $earning->astrologerName = $astrologerName[0]->name;
                    }
                }
                $headers = array(
                    'Content-type' => 'text/csv',
                );
                $filename = public_path('astrologerEarning.csv');
                $handle = fopen($filename, 'w');
                fputcsv($handle, [
                    'ID',
                    'User',
                    'OrderType',
                    'OrderAmount',
                    'TotalMin',
                    'Charge',
                    'OrderDate',
                ]);

                for ($i = 0; $i < count($astrologerEarning); $i++) {
                    fputcsv($handle, [
                        $i + 1,
                        $astrologerEarning[$i]->userName,
                        $astrologerEarning[$i]->orderType,
                        number_format($astrologerEarning[$i]->totalPayable, 2),
                        $astrologerEarning[$i]->totalMin,
                        number_format($astrologerEarning[$i]->charge, 2),
                        date('d-m-Y h:i', strtotime($astrologerEarning[$i]->created_at)),
                    ]);
                }
                fclose($handle);
                return Response::download($filename, 'astrologerEarning.csv', $headers);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Admin Earning

    public function adminEarnings(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                // Base query
                $adminearnings = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->leftJoin('users', 'users.id', 'order_request.userId')
                    ->leftJoin('astrologers', 'astrologers.id', 'order_request.astrologerId')
                    ->select(
                        'order_request.*',
                        'admin_get_commissions.amount as adminearningAmount',
                        'users.name as UserName',
                        'users.profile as userProfile',
                        'astrologers.name as astrologerName',
                        'astrologers.profileImage as astrologerProfile'
                    )
                    ->orderBy('admin_get_commissions.id', 'DESC');

                // Clone query for counting records
                $countQuery = clone $adminearnings;

                // Remove groupBy from count query
                $countQuery->selectRaw('COUNT(DISTINCT admin_get_commissions.id) as totalRecords');

                $searchString = $request->searchString ?? null;
                if ($searchString) {
                    $adminearnings->where(function ($q) use ($searchString) {
                        $q
                            ->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%');
                    });

                    // Apply search filters to count query
                    $countQuery->where(function ($q) use ($searchString) {
                        $q
                            ->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%');
                    });
                }

                $orderType = $request->orderType ?? null;
                if ($orderType) {
                    $adminearnings->where('order_request.orderType', $orderType);
                    $countQuery->where('order_request.orderType', $orderType);
                }

                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $adminearnings->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $adminearnings->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $adminearnings->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
                }

                // Get paginated records
                $earnings = $adminearnings->skip($paginationStart)->take($this->limit)->get();

                // Get correct total count
                $counts = $countQuery->first()->totalRecords ?? 0;

                // Calculate pagination
                $totalPages = ceil($counts / $this->limit);
                $totalRecords = $counts;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                // Get currency symbol
                $currency = DB::table('systemflag')->where('name', 'currencySymbol')->select('value')->first();

                return view('pages.admin-earnings', compact('earnings', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'currency', 'from_date', 'to_date'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function printAdminEarnings(Request $request)
    {
        try {
            $adminearnings = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                ->leftJoin('users', 'users.id', 'order_request.userId')
                ->leftJoin('astrologers', 'astrologers.id', 'order_request.astrologerId')
                ->select(
                    'order_request.*',
                    'admin_get_commissions.amount as adminearningAmount',
                    'users.name as UserName',
                    'users.profile as userProfile',
                    'astrologers.name as astrologerName',
                    'astrologers.profileImage as astrologerProfile'
                )
                ->orderBy('admin_get_commissions.id', 'DESC');

            // Clone query for counting records
            $countQuery = clone $adminearnings;

            // Remove groupBy from count query
            $countQuery->selectRaw('COUNT(DISTINCT admin_get_commissions.id) as totalRecords');

            $searchString = $request->searchString ?? null;
            if ($searchString) {
                $adminearnings->where(function ($q) use ($searchString) {
                    $q
                        ->where('users.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%');
                });

                // Apply search filters to count query
                $countQuery->where(function ($q) use ($searchString) {
                    $q
                        ->where('users.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%');
                });
            }

            $orderType = $request->orderType ?? null;
            if ($orderType) {
                $adminearnings->where('order_request.orderType', $orderType);
                $countQuery->where('order_request.orderType', $orderType);
            }

            // Date filter
            $from_date = $request->from_date ?? null;
            $to_date = $request->to_date ?? null;

            if ($from_date && $to_date) {
                $adminearnings->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                $countQuery->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
            } elseif ($from_date) {
                $adminearnings->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
                $countQuery->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
            } elseif ($to_date) {
                $adminearnings->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
                $countQuery->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
            }

            // Get paginated records
            $earnings = $adminearnings->get();

            // Get correct total count
            $counts = $countQuery->first()->totalRecords ?? 0;

            // Get currency symbol
            $currency = DB::table('systemflag')->where('name', 'currencySymbol')->select('value')->first();
            $data = [
                'title' => 'AdminEarnings',
                'date' => Carbon::now()->format('d-m-Y h:i a'),
                'earnings' => $earnings,
                'currency' => $currency
            ];
            $pdf = PDF::loadView('pages.admin-earningsPdf', $data);
            return $pdf->download('CustomerList.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function exportAdminEarningCSV(Request $request)
    {
        $authuser = Auth::guard('web')->user();
        $adminearnings = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
            ->leftJoin('users', 'users.id', 'order_request.userId')
            ->leftJoin('astrologers', 'astrologers.id', 'order_request.astrologerId')
            ->select(
                'order_request.*',
                'admin_get_commissions.amount as adminearningAmount',
                'users.name as UserName',
                'users.profile as userProfile',
                'astrologers.name as astrologerName',
                'astrologers.profileImage as astrologerProfile'
            )
            ->orderBy('admin_get_commissions.id', 'DESC');

        // Clone query for counting records
        $countQuery = clone $adminearnings;

        // Remove groupBy from count query
        $countQuery->selectRaw('COUNT(DISTINCT admin_get_commissions.id) as totalRecords');

        $searchString = $request->searchString ?? null;
        if ($searchString) {
            $adminearnings->where(function ($q) use ($searchString) {
                $q
                    ->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%');
            });

            // Apply search filters to count query
            $countQuery->where(function ($q) use ($searchString) {
                $q
                    ->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%');
            });
        }

        $orderType = $request->orderType ?? null;
        if ($orderType) {
            $adminearnings->where('order_request.orderType', $orderType);
            $countQuery->where('order_request.orderType', $orderType);
        }

        // Date filter
        $from_date = $request->from_date ?? null;
        $to_date = $request->to_date ?? null;

        if ($from_date && $to_date) {
            $adminearnings->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
            $countQuery->whereBetween('order_request.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
        } elseif ($from_date) {
            $adminearnings->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
            $countQuery->where('order_request.created_at', '>=', $from_date . ' 00:00:00');
        } elseif ($to_date) {
            $adminearnings->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
            $countQuery->where('order_request.created_at', '<=', $to_date . ' 23:59:59');
        }

        // Get paginated records
        $earnings = $adminearnings->get();

        // Get correct total count
        $counts = $countQuery->first()->totalRecords ?? 0;

        $headers = array(
            'Content-type' => 'text/csv',
        );
        $filename = public_path('CustomerList.csv');
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'SN',
            'User Name',
            'Exper Name',
            'Order Type',
            'Duration',
            'Total Amount',
            'Expert Earning',
            'Admin Earning',
            'Date',
        ]);
        for ($i = 0; $i < count($earnings); $i++) {
            fputcsv($handle, [
                $i + 1,
                $earnings[$i]->UserName ?? '--',
                $earnings[$i]->astrologerName,
                $earnings[$i]->orderType ?? '--',
                $earnings[$i]->totalMin ?? '--',
                $earnings[$i]->totalPayable ?? '--',
                ($earnings[$i]->totalPayable - $earnings[$i]->adminearningAmount) ?? '--',
                $earnings[$i]->adminearningAmount ?? '--',
                date('d-m-Y h:i a', strtotime($earnings[$i]->updated_at)),
            ]);
        }
        fclose($handle);
        return Response::download($filename, 'customerList.csv', $headers);
    }

    // partner earning
    public function astrologerEarning(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                // Fetch Admin Commission Data
                $adminCommissionQuery = DB::table('order_request')
                    ->join('admin_get_commissions', 'admin_get_commissions.orderId', '=', 'order_request.id')
                    ->whereNotNull('order_request.astrologerId')
                    ->select('order_request.astrologerId', 'order_request.inr_usd_conversion_rate')
                    ->groupBy('order_request.astrologerId')
                    ->selectRaw('sum(admin_get_commissions.amount) as totalEarning');

                if ($request->astrologerId) {
                    $adminCommissionQuery->where('order_request.astrologerId', $request->astrologerId);
                }

                $adminCommission = $adminCommissionQuery->get();
                $astrologers = DB::table('astrologers')->get();

                // Count the number of astrologers who have commissions
                $adminCommissionCount = DB::table('admin_get_commissions')
                    ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->whereNotNull('order_request.astrologerId')
                    ->select('order_request.astrologerId')
                    ->groupBy('order_request.astrologerId')
                    ->distinct()
                    ->get();

                if ($adminCommission->isNotEmpty()) {
                    foreach ($adminCommission as $commission) {
                        $astrologer = DB::table('astrologers')->where('id', $commission->astrologerId)->first();
                        $commission->astrologerName = $astrologer->name ?? null;

                        // Fetch earnings by commission type
                        $commissionTypes = [1 => 'chatEarning', 2 => 'callEarning', 3 => 'reportEarning', 5 => 'giftEarning', 6 => 'pujaEarning'];
                        foreach ($commissionTypes as $typeId => $field) {
                            $earning = DB::table('order_request')
                                ->join('admin_get_commissions', 'admin_get_commissions.orderId', '=', 'order_request.id')
                                ->where('order_request.astrologerId', $commission->astrologerId)
                                ->where('admin_get_commissions.commissionTypeId', $typeId)
                                ->sum(DB::raw('order_request.totalPayable - admin_get_commissions.amount'));
                            $commission->$field = $earning ?: null;
                        }

                        // Fetch total withdrawal
                        $totalWithdrawal = WithdrawRequest::where('astrologerId', $commission->astrologerId)->sum('withdrawAmount');
                        $commission->totalWithdrawal = $totalWithdrawal ?: null;

                        // Fetch user wallet balance (Check for null astrologer)
                        if ($astrologer) {
                            $totalBalance = UserWallet::where('userId', $astrologer->userId)->value('amount');
                            $commission->totalbalance = $totalBalance ?? null;
                        } else {
                            $commission->totalbalance = null;
                        }
                    }

                    // Paginate results
                    $adminCommission = array_slice(array($adminCommission), $paginationStart, $this->limit);
                    $totalPages = ceil(count($adminCommissionCount) / $this->limit);
                    $totalRecords = count($adminCommissionCount);
                    $partnerWiseEarning = !empty($adminCommission) ? $adminCommission[0] : [];
                    $start = ($this->limit * ($page - 1)) + 1;
                    $end = min($this->limit * $page, $totalRecords);

                    return view('pages.astrologer-earning', compact('partnerWiseEarning', 'astrologers', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
                } else {
                    return view('pages.astrologer-earning', [
                        'partnerWiseEarning' => [],
                        'astrologers' => $astrologers,
                        'totalPages' => 0,
                        'totalRecords' => 0,
                        'start' => 0,
                        'end' => 0,
                        'page' => $page
                    ]);
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
