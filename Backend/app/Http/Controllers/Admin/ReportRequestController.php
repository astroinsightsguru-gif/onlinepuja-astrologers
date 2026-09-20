<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\UserReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
use PDF;
use Response;

class ReportRequestController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getReportRequest(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $report = UserReport::join('astrologers as ast', 'ast.id', '=', 'user_reports.astrologerId')
                    ->join('users as ur', 'ur.id', '=', 'user_reports.userId')
                    ->join('report_types', 'report_types.id', 'user_reports.reportType')
                    ->select('ur.name as userName', 'ur.contactNo as userContactNo', 'ast.contactNo as astrologerContactNo', 'ast.name as astrologerName', 'user_reports.*', 'report_types.title')
                    ->orderBy('user_reports.id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $report = $report->where(function ($q) use ($searchString) {
                        $q
                            ->where('ur.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('ur.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('ast.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('ast.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                // Clone query for counting records
                $countQuery = clone $report;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $report->whereBetween('user_reports.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('user_reports.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $report->where('user_reports.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('user_reports.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $report->where('user_reports.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('user_reports.created_at', '<=', $to_date . ' 23:59:59');
                }
                $report = $report->skip($paginationStart);
                $report = $report->take($this->limit);
                $reportRequest = $report->get();
                $reportCount = DB::table('user_reports as rep')
                    ->join('astrologers as astro', 'astro.id', '=', 'rep.astrologerId')
                    ->join('users as us', 'us.id', '=', 'rep.userId')
                    ->join('report_types', 'report_types.id', 'rep.reportType');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $reportCount = $reportCount->where(function ($q) use ($searchString) {
                        $q
                            ->where('us.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('us.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astro.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astro.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $reportCount = $reportCount->count();
                $totalPages = ceil($reportCount / $this->limit);
                $totalRecords = $reportCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view('pages.report-request', compact('reportRequest', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function printPdf(Request $request)
    {
        try {
            $reportHistory = UserReport::join('astrologers as astr', 'astr.id', '=', 'user_reports.astrologerId')
                ->join('users as u', 'u.id', '=', 'user_reports.userId')
                ->join('report_types', 'report_types.id', 'user_reports.reportType')
                ->select('u.name as userName', 'u.contactNo as userContactNo', 'astr.name as astrologerName', 'astr.contactNo as astrologerContactNo', 'user_reports.*', 'report_types.title')
                ->orderBy('user_reports.id', 'DESC');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $reportHistory = $reportHistory->where(function ($q) use ($searchString) {
                    $q
                        ->where('u.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('u.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astr.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astr.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $reportHistory = $reportHistory->get();
            $data = [
                'title' => 'Report History Report',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'reportHistory' => $reportHistory,
            ];
            $pdf = PDF::loadView('pages.report-history', $data);
            return $pdf->download('reportHistory.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function exportCSV(Request $request)
    {
        $this->path = env('API_URL');
        $reportHistory = UserReport::join('astrologers as astr', 'astr.id', '=', 'user_reports.astrologerId')
            ->join('users as u', 'u.id', '=', 'user_reports.userId')
            ->join('report_types', 'report_types.id', 'user_reports.reportType')
            ->select('u.name as userName', 'u.contactNo as userContactNo', 'astr.name as astrologerName', 'astr.contactNo as astrologerContactNo', 'user_reports.*', 'report_types.title')
            ->orderBy('user_reports.id', 'DESC');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $reportHistory = $reportHistory->where(function ($q) use ($searchString) {
                $q
                    ->where('u.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('u.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astr.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astr.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $reportHistory = $reportHistory->get();

        $headers = array(
            'Content-type' => 'text/csv',
        );
        $filename = public_path('reportHistory.csv');
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'ID',
            'User',
            'Astrologer',
            'Report Type',
            'Report Date',
            'Report Charge',
        ]);
        for ($i = 0; $i < count($reportHistory); $i++) {
            fputcsv($handle, [
                $i + 1,
                $reportHistory[$i]->userName,
                $reportHistory[$i]->astrologerName,
                $reportHistory[$i]->title,
                date('d-m-Y h:i', strtotime($reportHistory[$i]->created_at)),
                number_format($reportHistory[$i]->reportRate, 2),
            ]);
        }
        fclose($handle);
        return Response::download($filename, 'reportHistory.csv', $headers);
    }
}
