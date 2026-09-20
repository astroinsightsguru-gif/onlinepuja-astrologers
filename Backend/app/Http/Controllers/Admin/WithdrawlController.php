<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\services\OneSignalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use PDF;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class WithdrawlController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function setWithdrawlPage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $withdrawRequest = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                $withdrawRequest = $withdrawRequest->select('withdrawrequest.*', 'astrologers.name', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.userId');

                $withdrawRequest = $withdrawRequest->orderBy('id', 'DESC');
                $withdrawRequest->skip($paginationStart);
                $withdrawRequest->take($this->limit);
                $withdrawlRequest = $withdrawRequest->get();

                $withdrawRequestCount = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                $withdrawRequestCount = $withdrawRequestCount->count();
                $totalPages = ceil($withdrawRequestCount / $this->limit);
                $totalRecords = $withdrawRequestCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.withdrawl', compact('withdrawlRequest', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function downloadTDSReportCSV(Request $request)
    {
        $searchString = $request->searchString ?? null;
        $from_date = $request->from_date ?? null;
        $to_date = $request->to_date ?? null;

        // Withdraw request query
        $withdrawQuery = DB::table('withdrawrequest')
            ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId')
            ->select(
                'astrologers.name as Astrologer',
                'astrologers.contactNo as Contact',
                'withdrawrequest.withdrawAmount',
                'withdrawrequest.tds_pay_amount',
                'withdrawrequest.pay_amount',
                'withdrawrequest.status'
            )
            ->whereIn('withdrawrequest.status', ['Pending', 'Released']);

        if ($searchString) {
            $withdrawQuery->where(function ($q) use ($searchString) {
                $q
                    ->where('astrologers.name', 'LIKE', "%{$searchString}%")
                    ->orWhere('astrologers.contactNo', 'LIKE', "%{$searchString}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::parse($from_date)->startOfDay();
            $to = Carbon::parse($to_date)->endOfDay();
            $withdrawQuery->whereBetween('withdrawrequest.created_at', [$from, $to]);
        }

        $withdrawData = $withdrawQuery->get();

        // Wallet amounts
        $walletQuery = DB::table('user_wallets')
            ->join('astrologers', 'astrologers.id', '=', 'user_wallets.userId')
            ->select(
                'astrologers.name as Astrologer',
                DB::raw('SUM(user_wallets.amount) as wallet_amount')
            )
            ->where('user_wallets.isActive', 1)
            ->groupBy('astrologers.name');

        if ($searchString) {
            $walletQuery->where(function ($q) use ($searchString) {
                $q
                    ->where('astrologers.name', 'LIKE', "%{$searchString}%")
                    ->orWhere('astrologers.contactNo', 'LIKE', "%{$searchString}%");
            });
        }

        $walletData = $walletQuery->get()->keyBy('Astrologer');

        // Prepare CSV
        $filename = 'tds_report_' . now()->format('Ymd_His') . '.csv';
        $columns = ['Astrologer', 'Contact', 'Total Withdraw', 'TDS Deducted', 'Payable Amount', 'Wallet Amount', 'Total Earned'];

        $callback = function () use ($withdrawData, $walletData, $columns) {
            $file = fopen('php://output', 'w');

            // Calculate summary
            $totalAstrologers = $withdrawData->groupBy('Astrologer')->count();
            $totalWithdraw = $withdrawData->sum('withdrawAmount');
            $totalTDS = $withdrawData->sum('tds_pay_amount');
            $totalPayable = $withdrawData->sum('pay_amount');

            // Write summary row first
            fputcsv($file, ['TOTAL ASTROLOGERS', $totalAstrologers, $totalWithdraw, $totalTDS, $totalPayable, '', '']);

            // Write column headers
            fputcsv($file, $columns);

            $reportGrouped = $withdrawData->groupBy('Astrologer');

            foreach ($reportGrouped as $astrologer => $records) {
                $contact = $records->first()->Contact;
                $totalWithdrawAstro = $records->sum('withdrawAmount');
                $totalTDSAstro = $records->sum('tds_pay_amount');
                $totalPayableAstro = $records->sum('pay_amount');
                $walletAmount = $walletData[$astrologer]->wallet_amount ?? 0;
                $totalEarned = $totalWithdrawAstro + $walletAmount;

                fputcsv($file, [
                    $astrologer,
                    $contact,
                    $totalWithdrawAstro,
                    $totalTDSAstro,
                    $totalPayableAstro,
                    $walletAmount,
                    $totalEarned
                ]);
            }

            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}"
        ];

        return response()->stream($callback, 200, $headers);
    }

    public function downloadTDSReportPDF(Request $request)
    {
        $searchString = $request->searchString ?? null;
        $from_date = $request->from_date ?? null;
        $to_date = $request->to_date ?? null;

        $withdrawQuery = DB::table('withdrawrequest')
            ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId')
            ->select(
                'astrologers.name as Astrologer',
                'astrologers.contactNo as Contact',
                'withdrawrequest.withdrawAmount',
                'withdrawrequest.tds_pay_amount',
                'withdrawrequest.pay_amount',
                'withdrawrequest.status'
            )
            ->whereIn('withdrawrequest.status', ['Pending', 'Released']);

        if ($searchString) {
            $withdrawQuery->where(function ($q) use ($searchString) {
                $q
                    ->where('astrologers.name', 'LIKE', "%{$searchString}%")
                    ->orWhere('astrologers.contactNo', 'LIKE', "%{$searchString}%");
            });
        }

        if ($from_date && $to_date) {
            $from = Carbon::parse($from_date)->startOfDay();
            $to = Carbon::parse($to_date)->endOfDay();
            $withdrawQuery->whereBetween('withdrawrequest.created_at', [$from, $to]);
        }

        $withdrawData = $withdrawQuery->get();

        $walletQuery = DB::table('user_wallets')
            ->join('astrologers', 'astrologers.id', '=', 'user_wallets.userId')
            ->select(
                'astrologers.name as Astrologer',
                DB::raw('SUM(user_wallets.amount) as wallet_amount')
            )
            ->where('user_wallets.isActive', 1)
            ->groupBy('astrologers.name');

        if ($searchString) {
            $walletQuery->where(function ($q) use ($searchString) {
                $q
                    ->where('astrologers.name', 'LIKE', "%{$searchString}%")
                    ->orWhere('astrologers.contactNo', 'LIKE', "%{$searchString}%");
            });
        }

        $walletData = $walletQuery->get()->keyBy('Astrologer');

        // Summary totals
        $totalAstrologers = $withdrawData->groupBy('Astrologer')->count();
        $totalWithdraw = $withdrawData->sum('withdrawAmount');
        $totalTDS = $withdrawData->sum('tds_pay_amount');
        $totalPayable = $withdrawData->sum('pay_amount');
        $totalWallet = $walletData->sum('wallet_amount');

        // Prepare data for view
        $reportGrouped = $withdrawData->groupBy('Astrologer');

        $pdf = PDF::loadView('reports.tds_pdf', [
            'reportGrouped' => $reportGrouped,
            'walletData' => $walletData,
            'totalAstrologers' => $totalAstrologers,
            'totalWithdraw' => $totalWithdraw,
            'totalTDS' => $totalTDS,
            'totalPayable' => $totalPayable,
            'totalWallet' => $totalWallet,
            'searchString' => $searchString,
            'from_date' => $from_date,
            'to_date' => $to_date
        ]);

        $pdf->setPaper('A4', 'portrait');
        return $pdf->download('TDS_Report_' . now()->format('Ymd_His') . '.pdf');
    }

    public function getWithDrawlRequest(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $page = $request->page ?? 1;
            $paginationStart = ($page - 1) * $this->limit;
            $searchString = $request->searchString ?? null;
            $orderType = $request->orderType ?? null;

            // Base withdraw request query
            $withdrawRequestQuery = DB::table('withdrawrequest')
                ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId')
                ->join('withdrawmethods', 'withdrawmethods.id', '=', 'withdrawrequest.paymentMethod')
                ->select(
                    'withdrawrequest.*',
                    'astrologers.name',
                    'astrologers.contactNo',
                    'astrologers.profileImage',
                    'astrologers.userId',
                    'astrologers.country',
                    'withdrawmethods.id as withdrawmethodid',
                    'withdrawmethods.method_name',
                    'withdrawmethods.method_id'
                );

            // Search filter
            if ($searchString) {
                $withdrawRequestQuery->where(function ($q) use ($searchString) {
                    $q
                        ->where('astrologers.name', 'LIKE', "%{$searchString}%")
                        ->orWhere('astrologers.contactNo', 'LIKE', "%{$searchString}%");
                });
            }

            // Status filter
            if ($orderType) {
                if ($orderType == 'pending') {
                    $withdrawRequestQuery->where('withdrawrequest.status', 'Pending');
                } elseif ($orderType == 'released' || $orderType == 'approve') {
                    $withdrawRequestQuery->where('withdrawrequest.status', 'Released');
                } elseif ($orderType == 'cancelled' || $orderType == 'reject') {
                    $withdrawRequestQuery->where('withdrawrequest.status', 'Cancelled');
                }
            }

            // Date filter
            if ($request->filled('from_date') && $request->filled('to_date')) {
                $from = Carbon::parse($request->from_date)->startOfDay();
                $to = Carbon::parse($request->to_date)->endOfDay();
                $withdrawRequestQuery->whereBetween('withdrawrequest.created_at', [$from, $to]);
            }

            // Count total records
            $totalRecords = $withdrawRequestQuery->count();

            // Apply pagination
            $withdrawlRequest = $withdrawRequestQuery
                ->orderBy('withdrawrequest.id', 'DESC')
                ->skip($paginationStart)
                ->take($this->limit)
                ->get();

            $totalPages = ceil($totalRecords / $this->limit);
            $start = ($this->limit * ($page - 1)) + 1;
            $end = ($paginationStart + $this->limit) < $totalRecords ? ($paginationStart + $this->limit) : $totalRecords;

            // TDS & Earnings report
            $tdsReportQuery = DB::table('withdrawrequest')
                ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId')
                ->select(
                    DB::raw('SUM(withdrawrequest.withdrawAmount) as total_withdraw'),
                    DB::raw('SUM(withdrawrequest.tds_pay_amount) as total_tds'),
                    DB::raw('SUM(withdrawrequest.pay_amount) as total_payable')
                )
                ->whereIn('withdrawrequest.status', ['Pending', 'Released']);

            if ($searchString) {
                $tdsReportQuery->where(function ($q) use ($searchString) {
                    $q
                        ->where('astrologers.name', 'LIKE', "%{$searchString}%")
                        ->orWhere('astrologers.contactNo', 'LIKE', "%{$searchString}%");
                });
            }

            $tdsReport = $tdsReportQuery->first();

            // Remaining Amount: sum of wallet amounts
            $walletQuery = DB::table('user_wallets')->select(DB::raw('SUM(amount) as remaining_amount'));
            if ($searchString) {
                $walletQuery = $walletQuery
                    ->join('astrologers', 'astrologers.id', '=', 'user_wallets.userId')
                    ->where(function ($q) use ($searchString) {
                        $q
                            ->where('astrologers.name', 'LIKE', "%{$searchString}%")
                            ->orWhere('astrologers.contactNo', 'LIKE', "%{$searchString}%");
                    });
            }
            $walletReport = $walletQuery->first();

            return view('pages.withdrawl', compact(
                'withdrawlRequest',
                'searchString',
                'totalPages',
                'totalRecords',
                'start',
                'end',
                'page',
                'orderType',
                'tdsReport',
                'walletReport'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function releaseAmount(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $withdrawRequest = array('status' => 'Released', 'note' => $request->note);
                DB::table('withdrawrequest')
                    ->where('id', $request->del_id)
                    ->update($withdrawRequest);

                $userDeviceDetail = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', 'withdrawrequest.astrologerId')
                    ->join('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                    ->where('withdrawrequest.id', '=', $request->del_id)
                    ->select('user_device_details.*', 'withdrawrequest.withdrawAmount')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    // One signal FOr notification send
                    $oneSignalService = new OneSignalService();
                    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                    $notification = [
                        'title' => $userDeviceDetail[0]->withdrawAmount . ' Receive from onlinepuja admin',
                        'body' => ['description' => 'Payment release from admin successfully', 'notificationType' => 7],
                    ];
                    // Send the push notification using the OneSignalService
                    $response = $oneSignalService->sendNotification($userPlayerIds, $notification);

                    $notification = array(
                        'userId' => $userDeviceDetail[0]->userId,
                        'title' => $userDeviceDetail[0]->withdrawAmount . ' Receive from onlinepuja admin',
                        // 'description' => 'It seems like you have missed/rejected your chat from ' . $astrologer[0]->name . ' .You may initiate it again from the app.',
                        'description' => 'Payment release from admin successfully',
                        'notificationId' => null,
                        'createdBy' => $userDeviceDetail[0]->userId,
                        'modifiedBy' => $userDeviceDetail[0]->userId,
                        'notification_type' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    );
                    DB::table('user_notifications')->insert($notification);
                }
                return redirect()->route('withdrawalRequests');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function cancelWithdrawAmount(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $withdrawRequest = array(
                    'status' => 'Cancelled',
                );
                DB::table('withdrawrequest')
                    ->where('id', $request->del_id)
                    ->update($withdrawRequest);

                $withdrawreq = DB::table('withdrawrequest')
                    ->where('id', $request->del_id)
                    ->first();

                $amount = DB::table('user_wallets')
                    ->join('astrologers', 'astrologers.userId', '=', 'user_wallets.userId')
                    ->where('astrologers.id', '=', $withdrawreq->astrologerId)
                    ->select('amount', 'user_wallets.id')
                    ->get();

                $userWallet = array(
                    'amount' => $amount[0]->amount + $withdrawreq->withdrawAmount,
                );

                DB::table('user_wallets')
                    ->where('id', $amount[0]->id)
                    ->update($userWallet);

                $userDeviceDetail = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', 'withdrawrequest.astrologerId')
                    ->join('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                    ->where('withdrawrequest.id', '=', $request->del_id)
                    ->select('user_device_details.*', 'withdrawrequest.withdrawAmount')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    // One signal FOr notification send
                    $oneSignalService = new OneSignalService();
                    //    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->all();
                    $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                    $notification = [
                        'title' => $userDeviceDetail[0]->withdrawAmount . ' Cancelled from onlinepuja admin',
                        'body' => ['description' => 'Payment cancelled from admin', 'notificationType' => 7],
                    ];
                    // Send the push notification using the OneSignalService
                    $response = $oneSignalService->sendNotification($userPlayerIds, $notification);

                    $notification = array(
                        'userId' => $userDeviceDetail[0]->userId,
                        'title' => $userDeviceDetail[0]->withdrawAmount . ' Cancelled from onlinepuja admin',
                        // 'description' => 'It seems like you have missed/rejected your chat from ' . $astrologer[0]->name . ' .You may initiate it again from the app.',
                        'description' => 'Payment cancelled from admin',
                        'notificationId' => null,
                        'createdBy' => $userDeviceDetail[0]->userId,
                        'modifiedBy' => $userDeviceDetail[0]->userId,
                        'notification_type' => 0,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    );
                    DB::table('user_notifications')->insert($notification);
                }
                return redirect()->route('withdrawalRequests');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getTdsGst(Request $request)
    {
        $query = DB::table('withdrawrequest');

        if ($request->filled('searchString')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->searchString . '%');
            });
        }

        if ($request->filled('orderType')) {
            if ($request->orderType == 'pending') {
                $query->where('status', 'pending');
            } elseif ($request->orderType == 'released') {
                $query->where('status', 'released');
            } elseif ($request->orderType == 'rancelled') {
                $query->where('status', 'cancelled');
            }
        }

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        }

        $AdminGetTDScomm = $query->orderBy('id', 'desc')->get();
        return view('pages.withdrawl', compact('AdminGetTDScomm'));
    }

    public function getWalletHistory(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $page = $request->page ?? 1;
            $paginationStart = ($page - 1) * $this->limit;

            // Currency symbol
            $currency = DB::table('systemflag')
                ->where('name', 'currencySymbol')
                ->select('value')
                ->first();

            // GST percentage
            $gstValue = DB::table('systemflag')
                ->where('name', 'gst')
                ->select('value')
                ->first();

            // Base Query
            $wallet = DB::table('payment')
                ->join('users', 'users.id', '=', 'payment.userId')
                ->select('payment.*', 'users.name as userName', 'users.profile as userProfile', 'users.contactNo as userContact', 'users.email as userEmail')
                ->whereIn('payment.paymentStatus', ['success', 'failed']);

            // 🔹 Filters
            if ($request->filled('searchString')) {
                $search = $request->searchString;
                $wallet->where(function ($q) use ($search) {
                    $q
                        ->where('users.name', 'LIKE', "%{$search}%")
                        ->orWhere('users.contactNo', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('paymentMethod')) {
                $wallet->where('payment.paymentMode', strtolower($request->paymentMethod));
            }

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $from = Carbon::parse($request->from_date)->startOfDay();
                $to = Carbon::parse($request->to_date)->endOfDay();
                $wallet->whereBetween('payment.created_at', [$from, $to]);
            }

            $walletData = (clone $wallet)
                ->orderBy('payment.id', 'DESC')
                ->skip($paginationStart)
                ->take($this->limit)
                ->get();

            $walletCount = (clone $wallet)->count();

            $totalPages = ceil($walletCount / $this->limit);
            $totalRecords = $walletCount;
            $start = ($this->limit * ($page - 1)) + 1;
            $end = min($start + $this->limit - 1, $totalRecords);

            return view('pages.wallet-history', [
                'wallet' => $walletData,
                'searchString' => $request->searchString,
                'totalPages' => $totalPages,
                'totalRecords' => $totalRecords,
                'start' => $start,
                'end' => $end,
                'page' => $page,
                'currency' => $currency,
                'gst' => $gstValue ? $gstValue->value : 0,
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function downloadWalletHistoryCSV(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Currency symbol
            $currency = DB::table('systemflag')
                ->where('name', 'currencySymbol')
                ->select('value')
                ->first();

            // GST percentage
            $gstValue = DB::table('systemflag')
                ->where('name', 'gst')
                ->select('value')
                ->first();

            $gst = $gstValue ? $gstValue->value : 0;

            // Base query
            $wallet = DB::table('payment')
                ->join('users', 'users.id', '=', 'payment.userId')
                ->select('payment.*', 'users.name as userName', 'users.contactNo as userContact')
                ->whereIn('payment.paymentStatus', ['success', 'failed']);

            // Filters
            if ($request->filled('searchString')) {
                $search = $request->searchString;
                $wallet->where(function ($q) use ($search) {
                    $q
                        ->where('users.name', 'LIKE', "%{$search}%")
                        ->orWhere('users.contactNo', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('paymentMethod')) {
                $wallet->where('payment.paymentMode', strtolower($request->paymentMethod));
            }

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $from = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                $to = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                $wallet->whereBetween('payment.created_at', [$from, $to]);
            }

            $walletData = $wallet->orderBy('payment.id', 'DESC')->get();

            if ($walletData->isEmpty()) {
                return back()->with('error', 'No records found to export.');
            }

            // Prepare CSV filename
            $fileName = 'Wallet_History_Report_' . now()->format('Y_m_d_H_i_s') . '.csv';

            // Open output stream
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename={$fileName}",
            ];

            $columns = [
                'ID',
                'User Name',
                'Contact',
                'Payment Mode',
                'Payment For',
                'Reference',
                'Amount (' . ($currency->value ?? '₹') . ')',
                'GST (' . $gst . '%)',
                'Total Amount (' . ($currency->value ?? '₹') . ')',
                'Status',
                'Date'
            ];

            $callback = function () use ($walletData, $columns, $gst) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($walletData as $row) {
                    $normalAmount = $row->amount;
                    $gstAmount = ($normalAmount * $gst) / 100;
                    $totalAmount = $normalAmount + $gstAmount;

                    fputcsv($file, [
                        $row->id,
                        $row->userName,
                        $row->userContact,
                        ucfirst($row->paymentMode),
                        $row->payment_for,
                        $row->paymentReference,
                        number_format($normalAmount, 2),
                        number_format($gstAmount, 2),
                        number_format($totalAmount, 2),
                        ucfirst($row->paymentStatus),
                        \Carbon\Carbon::parse($row->created_at)->format('d-m-Y h:i A')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function downloadWalletHistoryPDF(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Currency symbol
            $currency = DB::table('systemflag')
                ->where('name', 'currencySymbol')
                ->select('value')
                ->first();

            // GST value from systemflag table
            $gstValue = DB::table('systemflag')
                ->where('name', 'gst')
                ->select('value')
                ->first();

            $gst = $gstValue ? $gstValue->value : 0;

            // Wallet data query
            $wallet = DB::table('payment')
                ->join('users', 'users.id', '=', 'payment.userId')
                ->select('payment.*', 'users.name as userName', 'users.contactNo as userContact')
                ->whereIn('payment.paymentStatus', ['success', 'failed']);

            // Filters
            if ($request->filled('searchString')) {
                $search = $request->searchString;
                $wallet->where(function ($q) use ($search) {
                    $q
                        ->where('users.name', 'LIKE', "%{$search}%")
                        ->orWhere('users.contactNo', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('paymentMethod')) {
                $wallet->where('payment.paymentMode', strtolower($request->paymentMethod));
            }

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $from = \Carbon\Carbon::parse($request->from_date)->startOfDay();
                $to = \Carbon\Carbon::parse($request->to_date)->endOfDay();
                $wallet->whereBetween('payment.created_at', [$from, $to]);
            }

            $walletData = $wallet->orderBy('payment.id', 'DESC')->get();

            $generated_at = now()->format('d-m-Y h:i A');

            $pdf = \PDF::loadView('reports.wallet-history-pdf', [
                'wallet' => $walletData,
                'currency' => $currency,
                'gst' => $gst,
                'generated_at' => $generated_at
            ]);

            return $pdf->download('Wallet_History_Report_' . now()->format('Y_m_d_H_i') . '.pdf');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function filterWalletData($request)
    {
        $wallet = DB::table('payment')
            ->join('users', 'users.id', '=', 'payment.userId')
            ->select(
                'payment.*',
                'users.name as userName',
                'users.contactNo as userContact'
            )
            ->whereIn('payment.paymentStatus', ['success', 'failed']);
        $filtersApplied = false;
        if ($request->filled('searchString')) {
            $filtersApplied = true;
            $search = $request->searchString;
            $wallet->where(function ($q) use ($search) {
                $q
                    ->where('users.name', 'LIKE', "%{$search}%")
                    ->orWhere('users.contactNo', 'LIKE', "%{$search}%");
            });
        }
        if ($request->filled('paymentMethod')) {
            $filtersApplied = true;
            $wallet->where('payment.paymentMode', strtolower($request->paymentMethod));
        }
        if ($request->filled('from_date') && $request->filled('to_date')) {
            $filtersApplied = true;
            $from = Carbon::parse($request->from_date)->startOfDay();
            $to = Carbon::parse($request->to_date)->endOfDay();
            $wallet->whereBetween('payment.created_at', [$from, $to]);
        }
        if (!$filtersApplied) {
            $wallet->take(10000);
        }
        return $wallet->orderBy('payment.id', 'DESC')->get();
    }

    public function getwithdrawalMethods(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $methods = DB::table('withdrawmethods')->get();
                return view('pages.withdrawl-methods', compact(
                    'methods',
                ));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function withdrawStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $affected = DB::table('withdrawmethods')
                    ->where('id', $request->status_id)
                    ->update(['isActive' => DB::raw('NOT isActive')]);
                if ($affected > 0) {
                    return redirect()->route('withdrawalMethods');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editwithdrawApi(Request $req)
    {
        try {
            // if (Auth::guard('web')->check()) {
            $affected = DB::table('withdrawmethods')
                ->where('id', $req->filed_id)
                ->update(['method_name' => $req->name, 'updated_at' => Carbon::now()]);

            if ($affected > 0) {
                return redirect()->route('withdrawalMethods');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
