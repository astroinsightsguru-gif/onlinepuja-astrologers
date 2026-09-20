<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\AppReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Contactus;
use Exception;

class AppFeedbackController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getAppFeedback(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $feedback = AppReview::select('app_reviews.*', 'users.name', 'users.contactNo', 'users.profile')
                ->join('users', 'users.id', '=', 'app_reviews.userId')
                ->orderBy('app_reviews.id', 'DESC');

                $totalRecords = $feedback->count();

                // Adjust page number if it exceeds total pages
                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);

                // Retrieve feedback for the current page
                $feedback = $feedback->skip($paginationStart)
                    ->take($this->limit)
                    ->orderBy('app_reviews.id', 'DESC')
                    ->get();

                // Calculate start and end records for the current page
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.app-feedback', compact('feedback', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    #-------------------------------------------------------------------------------------------------------------------------------------------------------
    public function contactList(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $contacts = Contactus::orderBy('id', 'DESC')
                ->skip($paginationStart)
                ->take($this->limit)
                ->get();

                $totalRecords = $contacts->count();

                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.contactlist', compact('contacts', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function details(Request $request)
{
    $contact = ContactUs::find($request->id);
    if($contact){
        return response()->json([
            'success' => true,
            'data' => $contact
        ]);
    }
    return response()->json(['success' => false]);
}


}
