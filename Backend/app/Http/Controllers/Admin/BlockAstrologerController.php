<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BlockAstrologerController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getBlockAstrologer(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $searchString = $request->searchString ? $request->searchString : null;
                $reviews = DB::table('blockastrologer')
                    ->join('users', 'users.id', '=', 'blockastrologer.userId')
                    ->join('astrologers', 'astrologers.id', '=', 'blockastrologer.astrologerId')
                    ->select('blockastrologer.*', 'users.name as userName', 'users.profile', 'users.contactNo', 'astrologers.name as astrologerName', 'astrologers.contactNo as astrologerContactNo')
                    ->whereNotNull('blockastrologer.astrologerId');
                if ($searchString) {
                    $reviews = $reviews->where(function ($q) use ($searchString) {
                        $q
                            ->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $reviewsCount = $reviews->count();
                $reviews->skip($paginationStart);
                $reviews->take($this->limit);
                $reportBlocks = $reviews->get();
                $totalPages = ceil($reviewsCount / $this->limit);
                $totalRecords = $reviewsCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.block-astrologer', compact('reportBlocks', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
