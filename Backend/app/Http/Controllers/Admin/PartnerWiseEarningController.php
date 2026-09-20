<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\AdminGetCommission;
use App\Models\UserModel\UserOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;

class PartnerWiseEarningController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getPartnerWiseEarning(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $adminCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->select('order_request.astrologerId')
                    ->selectRaw('sum(admin_get_commissions.amount) as totalEarning')
                    ->groupBy('order_request.astrologerId');

                if ($request->astrologerId) {
                    $adminCommission->where('order_request.astrologerId', $request->astrologerId);
                }

                $adminCommission = $adminCommission->get();

                $astrologers = DB::table('astrologers')->get();

                $adminCommissionCount = DB::table('admin_get_commissions')
                    ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->select('order_request.astrologerId')
                    ->groupBy('order_request.astrologerId')
                    ->distinct()
                    ->get();

                if ($adminCommission && count($adminCommission) > 0) {
                    foreach ($adminCommission as $commission) {
                        $astrologerName = DB::table('astrologers')->where('id', '=', $commission->astrologerId)->select('name')->get();

                        if (count($astrologerName) > 0) {
                            $commission->astrologerName = $astrologerName[0]->name;
                        } else {
                            $commission->astrologerName = null;  // or any default value you want to set
                        }

                        $chatCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->where('order_request.astrologerId', '!=', 'NULL')
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->where('admin_get_commissions.commissionTypeId', '=', '1')
                            ->get()
                            ->sum(function ($commission) {
                                return $commission->amount;
                            });

                        $callCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->where('order_request.astrologerId', '!=', 'NULL')
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->where('admin_get_commissions.commissionTypeId', '=', '2')
                            ->get()
                            ->sum(function ($commission) {
                                return $commission->amount;
                            });

                        $reportCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->whereNotNull('order_request.astrologerId')
                            ->where('admin_get_commissions.commissionTypeId', 3)
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->get()
                            ->sum(function ($commission) {
                                return $commission->amount;
                            });

                        $userOrder = UserOrder::where('orderType', 'aiChat')->get();
                        $aichatearning = $userOrder->sum('totalPayable');

                        $giftCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->where('order_request.astrologerId', '!=', 'NULL')
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->where('admin_get_commissions.commissionTypeId', '=', '5')
                            ->get()  // Fetch all matching records
                            ->sum(function ($commission) {
                                return $commission->amount;  // Accessor logic is applied here
                            });

                        $pujaCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->where('order_request.astrologerId', '!=', 'NULL')
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->where('admin_get_commissions.commissionTypeId', '=', '6')
                            ->get()  // Fetch all matching records
                            ->sum(function ($commission) {
                                return $commission->amount;  // Accessor logic is applied here
                            });

                        $courseCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->where('order_request.astrologerId', '!=', 'NULL')
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->where('admin_get_commissions.commissionTypeId', '=', '8')
                            ->get()  // Fetch all matching records
                            ->sum(function ($commission) {
                                return $commission->amount;  // Accessor logic is applied here
                            });

                        $commission->chatEarning = $chatCommission ? $chatCommission : null;
                        $commission->callEarning = $callCommission ? $callCommission : null;
                        $commission->reportEarning = $reportCommission ? $reportCommission : null;
                        $commission->giftEarning = $giftCommission ? $giftCommission : null;
                        $commission->pujaEarning = $pujaCommission ? $pujaCommission : null;
                        $commission->courseEarning = $courseCommission ? $courseCommission : null;
                        $commission->aichatearning = $aichatearning ? $aichatearning : null;
                    }

                    $adminCommission = array_slice(array($adminCommission), $paginationStart, $this->limit);

                    $totalPages = ceil(count($adminCommissionCount) / $this->limit);
                    $totalRecords = count($adminCommissionCount);
                    $partnerWiseEarning = ($adminCommission && count($adminCommission) > 0) ? $adminCommission[0] : [];
                    $start = ($this->limit * ($page - 1)) + 1;
                    $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                    // dd($partnerWiseEarning);
                    return view('pages.partnerwise-earning', compact('partnerWiseEarning', 'astrologers', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
                } else {
                    // No data available, pass an empty array to the view
                    $partnerWiseEarning = [];
                    $totalPages = 0;
                    $totalRecords = 0;
                    $start = 0;
                    $end = 0;

                    return view('pages.partnerwise-earning', compact('partnerWiseEarning', 'astrologers', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function printPdf(Request $req)
    {
        try {
            $adminCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                ->where('order_request.astrologerId', '!=', 'NULL')
                ->select('order_request.astrologerId')
                ->selectRaw('sum(admin_get_commissions.amount) as totalEarning')
                ->groupBy('order_request.astrologerId')
                ->get();
            if ($adminCommission && count($adminCommission) > 0) {
                foreach ($adminCommission as $commission) {
                    $astrologerName = DB::table('astrologers')->where('id', '=', $commission->astrologerId)->select('name')->get();
                    $commission->astrologerName = $astrologerName[0]->name;

                    $totalCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->where('order_request.astrologerId', '!=', 'NULL')
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        // ->where('admin_get_commissions.commissionTypeId', '=', '2')
                        ->get()
                        ->sum(function ($commission) {
                            return $commission->amount;
                        });

                    $chatCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->where('order_request.astrologerId', '!=', 'NULL')
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->where('admin_get_commissions.commissionTypeId', '=', '1')
                        ->get()
                        ->sum(function ($commission) {
                            return $commission->amount;
                        });
                    $callCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->where('order_request.astrologerId', '!=', 'NULL')
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->where('admin_get_commissions.commissionTypeId', '=', '2')
                        ->get()
                        ->sum(function ($commission) {
                            return $commission->amount;
                        });
                    $reportCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->whereNotNull('order_request.astrologerId')
                        ->where('admin_get_commissions.commissionTypeId', 3)
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->get()
                        ->sum(function ($commission) {
                            return $commission->amount;
                        });

                    $pujaCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->whereNotNull('order_request.astrologerId')
                        ->where('admin_get_commissions.commissionTypeId', 6)
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->get()
                        ->sum(function ($commission) {
                            return $commission->amount;
                        });
                    $courseCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->whereNotNull('order_request.astrologerId')
                        ->where('admin_get_commissions.commissionTypeId', 8)
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->get()
                        ->sum(function ($commission) {
                            return $commission->amount;
                        });
                    $giftCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->whereNotNull('order_request.astrologerId')
                        ->where('admin_get_commissions.commissionTypeId', 5)
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->get()
                        ->sum(function ($commission) {
                            return $commission->amount;
                        });

                    $commission->chatEarning = $chatCommission ? $chatCommission : null;
                    $commission->callEarning = $callCommission ? $callCommission : null;
                    $commission->reportEarning = $reportCommission ? $reportCommission : null;
                    $commission->pujaEarning = $pujaCommission ? $pujaCommission : null;
                    $commission->courseEarning = $courseCommission ? $courseCommission : null;
                    $commission->giftEarning = $giftCommission ? $giftCommission : null;
                    $commission->totalNewEarning = $totalCommission ? $totalCommission : null;
                }
            }
            $partnerWiseEarning = ($adminCommission && count($adminCommission) > 0) ? $adminCommission : [];
            $data = [
                'title' => 'PartnerWise Earning',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'partnerWiseEarning' => $partnerWiseEarning,
            ];
            $pdf = PDF::loadView('pages.partnerwise-earning-report', $data);
            return $pdf->download('partnerWiseEarning.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function exportPartnerWiseCSV(Request $request)
    {
        $adminCommission = DB::table('admin_get_commissions')
            ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
            ->where('order_request.astrologerId', '!=', 'NULL')
            ->select('order_request.astrologerId')
            ->selectRaw('sum(admin_get_commissions.amount) as totalEarning')
            ->groupBy('order_request.astrologerId')
            ->get();
        if ($adminCommission && count($adminCommission) > 0) {
            foreach ($adminCommission as $commission) {
                $astrologerName = DB::table('astrologers')->where('id', '=', $commission->astrologerId)->select('name')->get();
                $commission->astrologerName = $astrologerName[0]->name;

                $totalCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    // ->where('admin_get_commissions.commissionTypeId', '=', '2')
                    ->get()
                    ->sum(function ($commission) {
                        return $commission->amount;
                    });

                $chatCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->where('admin_get_commissions.commissionTypeId', '=', '1')
                    ->get()
                    ->sum(function ($commission) {
                        return $commission->amount;
                    });

                $callCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->where('admin_get_commissions.commissionTypeId', '=', '2')
                    ->get()
                    ->sum(function ($commission) {
                        return $commission->amount;
                    });
                $reportCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->whereNotNull('order_request.astrologerId')
                    ->where('admin_get_commissions.commissionTypeId', 3)
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->get()
                    ->sum(function ($commission) {
                        return $commission->amount;
                    });

                $pujaCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->whereNotNull('order_request.astrologerId')
                    ->where('admin_get_commissions.commissionTypeId', 6)
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->get()
                    ->sum(function ($commission) {
                        return $commission->amount;
                    });
                $courseCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->whereNotNull('order_request.astrologerId')
                    ->where('admin_get_commissions.commissionTypeId', 8)
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->get()
                    ->sum(function ($commission) {
                        return $commission->amount;
                    });
                $giftCommission = AdminGetCommission::join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->whereNotNull('order_request.astrologerId')
                    ->where('admin_get_commissions.commissionTypeId', 5)
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->get()
                    ->sum(function ($commission) {
                        return $commission->amount;
                    });
                $commission->chatEarning = $chatCommission ? $chatCommission : null;
                $commission->callEarning = $callCommission ? $callCommission : null;
                $commission->reportEarning = $reportCommission ? $reportCommission : null;
                $commission->pujaEarning = $pujaCommission ? $pujaCommission : null;
                $commission->giftEarning = $giftCommission ? $giftCommission : null;
                $commission->courseEarning = $courseCommission ? $courseCommission : null;
                $commission->totalNewEarning = $totalCommission ? $totalCommission : null;
            }
        }
        $partnerWiseEarning = ($adminCommission && count($adminCommission) > 0) ? $adminCommission : [];
        $headers = array(
            'Content-type' => 'text/csv',
        );
        $filename = public_path('partnerWiseEarning.csv');
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'ID',
            'Astrologer',
            'Total Earning',
            'Chat Earning',
            'Call Earning',
            'Report Earning',
            'Puja Earning',
            'Gift Earning',
        ]);
        for ($i = 0; $i < count($partnerWiseEarning); $i++) {
            fputcsv($handle, [
                $i + 1,
                $partnerWiseEarning[$i]->astrologerName,
                number_format($partnerWiseEarning[$i]->totalNewEarning, 2),
                number_format($partnerWiseEarning[$i]->chatEarning, 2),
                number_format($partnerWiseEarning[$i]->callEarning, 2),
                number_format($partnerWiseEarning[$i]->reportEarning, 2),
                number_format($partnerWiseEarning[$i]->pujaEarning, 2),
                number_format($partnerWiseEarning[$i]->giftEarning, 2),
            ]);
        }
        fclose($handle);
        return Response::download($filename, 'partnerWiseEarning.csv', $headers);
    }
}
