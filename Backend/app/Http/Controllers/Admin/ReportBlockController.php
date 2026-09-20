<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class ReportBlockController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getReportBlock(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $reviews = DB::table('user_reviews')
                    ->leftjoin('users', 'users.id', '=', 'user_reviews.userId')
                    ->join('astrologers', 'astrologers.id', '=', 'user_reviews.astrologerId')
                    ->select('user_reviews.*', 'users.name as userName', 'users.profile', 'users.contactNo', 'astrologers.name as astrologerName', 'astrologers.contactNo as astrologerContactNo')
                    ->whereNotNull('user_reviews.astrologerId');
                $reviewsCount = $reviews->count();
                $astrologer = DB::table('astrologers')->where('isActive', '=', 1)->where('isDelete', '=', 0)->where('isVerified', '=', 1)->get();
                $userdatas = DB::table('users as u')
                    ->join('user_roles', 'user_roles.userId', '=', 'u.id')
                    ->where('u.isDelete', '=', false)
                    ->where('user_roles.roleId', '=', 3)
                    ->select('u.name as userName', 'u.id as userId', 'u.contactNo as usercontactNo', 'u.email')
                    ->orderBy('u.id', 'DESC')
                    ->get();

                $reviews->skip($paginationStart);
                $reviews->take($this->limit);
                $reportBlocks = $reviews->get();
                $totalPages = ceil($reviewsCount / $this->limit);
                $totalRecords = $reviewsCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.reportBlock', compact('reportBlocks', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'astrologer', 'userdatas'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteReview(request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                DB::table('user_reviews')->where('id', $request->del_id)->delete();
                return redirect()->back();
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addReviewfromAdmin(Request $req)
    {
        DB::table('user_reviews')->insert([
            'astrologerId' => $req->astrologerId,
            'user_name' => $req->user_name,
            'review' => $req->review,
            'rating' => $req->rating,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'createdBy' => Auth::id(),
            'modifiedBy' => Auth::id()
        ]);

        return redirect()->back();
    }
}
