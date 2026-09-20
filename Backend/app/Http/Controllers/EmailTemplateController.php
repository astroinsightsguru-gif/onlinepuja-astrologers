<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
if (!defined('DESTINATIONPATH')) define('DESTINATIONPATH', 'public/storage/images/');
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class EmailTemplateController extends Controller
{
    public $path;
    public $limit = 6;
    public $paginationStart;

    public function getEmailTemplate(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $email = EmailTemplate::query();
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $email->whereRaw(sql: "title LIKE '%" . $request->searchString . "%' ");
                }
                $email->orderBy('id', 'DESC');
                $emailCount = $email->count();
                $email->skip($paginationStart);
                $email->take($this->limit);
                $emails = $email->get();
                $totalPages = ceil($emailCount / $this->limit);
                $totalRecords = $emailCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.emailtemplate.emailtemplate', compact('emails', 'totalPages', 'searchString', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', '', $e->getMessage());
        }
    }

    public function addEmailTemplate(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $email = EmailTemplate::create([
                    'name' => $req->name,
                    'subject' => $req->subject,
                    'description' => $req->description,
                ]);

                return response()->json([
                    'success' => 'Email Added',
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function editEmailTemplate(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $email = EmailTemplate::find($request->filed_id);
                if ($email) {
                    $email->name = $request->name;
                    $email->subject = $request->subject;
                    $email->description = $request->editdescription;
                    $email->update();

                    return response()->json([
                        'success' => 'Email Update',
                    ]);
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', '', $e->getMessage());
        }
    }
}
