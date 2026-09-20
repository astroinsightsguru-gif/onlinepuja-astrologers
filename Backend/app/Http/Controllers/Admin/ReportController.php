<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\UserModel\ReportType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class ReportController extends Controller
{
    // Add Gift API
    public $path;
    public $limit = 8;
    public $paginationStart;

    public function addReport()
    {
        return view('pages.report');
    }

    public function addReportApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'title' => 'required|unique:report_types',
                'reportImage' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $imagePath = null;

            // Handle report image upload
            if ($req->hasFile('reportImage')) {
                $imageContent = file_get_contents($req->file('reportImage')->getRealPath());
                $extension = $req->file('reportImage')->getClientOriginalExtension() ?? 'png';
                $imageName = 'reportType_' . time() . '.' . $extension;

                try {
                    $imagePath = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'report');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            } elseif ($req->reportImage) {
                // Handle base64 image
                $imageContent = base64_decode(base64_encode(file_get_contents($req->file('reportImage'))));
                $imageName = 'reportType_' . time() . '.png';
                try {
                    $imagePath = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'report');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            // Create report type
            $reportType = ReportType::create([
                'title' => $req->title,
                'reportImage' => $imagePath,
                'description' => $req->description,
            ]);

            return redirect()->route('reportTypes');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getReport(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $reportType = ReportType::query();
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $reportType->whereRaw(sql: "title LIKE '%" . $request->searchString . "%' ");
                }
                $reportType = $reportType->orderBy('id', 'DESC');
                $reportTypeCount = $reportType->count();
                $reportType->skip($paginationStart);
                $reportType->take($this->limit);
                $reports = $reportType->get();
                $totalPages = (int) ceil($reportTypeCount / $this->limit);
                $totalRecords = $reportTypeCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.report', compact('reports', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editGift()
    {
        return view('pages.gift-list');
    }

    public function editReportApi(Request $req)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $reportType = ReportType::find($req->editId);
            if (!$reportType) {
                return redirect()->route('reportTypes')->with('error', 'Report not found.');
            }

            $imagePath = $reportType->reportImage;  // default to existing image

            // Handle new image upload
            if ($req->hasFile('reportImage')) {
                $imageContent = file_get_contents($req->file('reportImage')->getRealPath());
                $extension = $req->file('reportImage')->getClientOriginalExtension() ?? 'png';
                $imageName = 'reportType_' . $req->editId . '_' . time() . '.' . $extension;

                try {
                    $imagePath = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'report');

                    // Delete old image if exists
                    if ($reportType->reportImage && Str::contains($reportType->reportImage, 'storage')) {
                        @unlink($reportType->reportImage);
                    }
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            } elseif ($req->reportImage) {
                // Handle base64 image
                $imageContent = base64_decode(base64_encode(file_get_contents($req->file('reportImage'))));
                $imageName = 'reportType_' . $req->editId . '_' . time() . '.png';

                try {
                    $imagePath = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'report');

                    if ($reportType->reportImage && Str::contains($reportType->reportImage, 'storage')) {
                        @unlink($reportType->reportImage);
                    }
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            // Update report type
            $reportType->title = $req->title;
            $reportType->description = $req->editdescription;
            $reportType->reportImage = $imagePath;
            $reportType->update();

            return redirect()->route('reportTypes');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function giftStatus(Request $request)
    {
        return view('pages.gift-list');
    }

    public function reportTypeStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $reportType = ReportType::find($request->status_id);
                if ($reportType) {
                    $reportType->isActive = !$reportType->isActive;
                    $reportType->update();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Exotel Report

    public function exotelReport(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $exotel = DB::table('exotel_reports as exotel')
                    ->join('astrologers as astr', 'astr.id', '=', 'exotel.astrologerId')
                    ->join('users as u', 'u.id', '=', 'exotel.userId')
                    ->select('u.name as userName', 'astr.name as astrologerName', 'exotel.*')
                    ->orderBy('exotel.id', 'DESC');

                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $exotel->where(function ($q) use ($searchString) {
                        $q
                            ->where('u.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('u.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astr.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astr.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }

                $totalRecords = $exotel->count();
                $totalPages = ceil($totalRecords / $this->limit);

                // Adjust page number if it exceeds total pages
                $page = min($page, $totalPages);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                $exotelHistory = $exotel->skip($paginationStart)->take($this->limit)->get();

                return view('pages.exotel-reports', compact('exotelHistory', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
