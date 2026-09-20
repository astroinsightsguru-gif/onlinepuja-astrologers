<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\CallRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;

class CallHistoryReportController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getCallHistory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $call = CallRequest::join('astrologers as astr', 'astr.id', '=', 'callrequest.astrologerId')
                    ->join('users as u', 'u.id', '=', 'callrequest.userId')
                    ->select('u.name as userName', 'astr.name as astrologerName', 'callrequest.*')
                    ->orderBy('callrequest.id', 'DESC');

                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $call->where(function ($q) use ($searchString) {
                        $q
                            ->where('u.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('u.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astr.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astr.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }

                // Clone query for counting records
                $countQuery = clone $call;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $call->whereBetween('callrequest.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('callrequest.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $call->where('callrequest.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('callrequest.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $call->where('callrequest.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('callrequest.created_at', '<=', $to_date . ' 23:59:59');
                }

                $totalRecords = $call->count();
                $totalPages = ceil($totalRecords / $this->limit);

                // Adjust page number if it exceeds total pages
                $page = min($page, $totalPages);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                $callHistory = $call->skip($paginationStart)->take($this->limit)->get();

                return view('pages.call-history-report', compact('callHistory', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function setCallHistoryPage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $call = DB::table('callrequest as cal')
                    ->join('astrologers as ast', 'ast.id', '=', 'cal.astrologerId')
                    ->join('users as us', 'us.id', '=', 'cal.userId')
                    ->select('us.name as userName', 'ast.name as astrologerName', 'cal.*')
                    ->orderBy('cal.id', 'DESC');
                $call = $call->skip($paginationStart);
                $call = $call->take($this->limit);
                $callHistory = $call->get();
                $callCount = DB::table('callrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                    ->join('users', 'users.id', '=', 'callrequest.userId')
                    ->count();
                $totalPages = ceil($callCount / $this->limit);
                $totalRecords = $callCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view('pages.call-history-report', compact('callHistory', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function printPdf(Request $request)
    {
        try {
            $callHistory = CallRequest::join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                ->join('users', 'users.id', '=', 'callrequest.userId')
                ->select('users.name as userName', 'astrologers.name as astrologerName', 'callrequest.*')
                ->orderBy('callrequest.id', 'DESC');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $callHistory = $callHistory->where(function ($q) use ($searchString) {
                    $q
                        ->where('users.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $callHistory = $callHistory->get();
            $data = [
                'title' => 'Call History Report',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'callHistory' => $callHistory,
            ];
            $pdf = PDF::loadView('pages.myPdf', $data);
            return $pdf->download('callHistory.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function exportCSV(Request $request)
    {
        $this->path = env('API_URL');
        $callHistory = CallRequest::join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
            ->join('users', 'users.id', '=', 'callrequest.userId')
            ->select('users.name as userName', 'astrologers.name as astrologerName', 'callrequest.*')
            ->orderBy('callrequest.id', 'DESC');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $callHistory = $callHistory->where(function ($q) use ($searchString) {
                $q
                    ->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $callHistory = $callHistory->get();
        // $callHistory =

        $headers = array(
            'Content-type' => 'text/csv',
        );
        $filename = public_path('callHistory.csv');
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'ID',
            'User',
            'Astrologer',
            'Call Rate',
            'Call Time',
            'Total Min',
            'Deduction',
        ]);
        for ($i = 0; $i < count($callHistory); $i++) {
            fputcsv($handle, [
                $i + 1,
                $callHistory[$i]->userName,
                $callHistory[$i]->astrologerName,
                number_format($callHistory[$i]->callRate, 2),
                date('d-m-Y h:i', strtotime($callHistory[$i]->updated_at)),
                $callHistory[$i]->totalMin,
                number_format($callHistory[$i]->deduction, 2),
            ]);
        }
        fclose($handle);
        return Response::download($filename, 'callHistory.csv', $headers);
    }
}
