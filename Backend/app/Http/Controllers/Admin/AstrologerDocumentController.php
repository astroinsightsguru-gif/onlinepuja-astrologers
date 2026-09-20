<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class AstrologerDocumentController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function addDocument(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:astrologer_documents',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                AstrologerDocument::create([
                    'name' => $req->name,
                ]);
                return redirect()->route('document');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function document(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $document = AstrologerDocument::query();
                $document->orderBy('id', 'DESC');
                $document->skip($paginationStart);
                $document->take($this->limit);
                $document = $document->get();
                $documentCount = AstrologerDocument::query();
                $documentCount = $documentCount->count();
                $totalPages = ceil($documentCount / $this->limit);
                $totalRecords = $documentCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.astrologer-document-list', compact('document', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Delete Skill Api

    public function deleteDocument(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $document = AstrologerDocument::find($request->del_id);
                if ($document) {
                    $document->delete();
                }
                return redirect()->route('document');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editDocument(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $document = AstrologerDocument::find($request->filed_id);
                if ($document) {
                    $document->name = $request->name;
                    $document->update();
                    return redirect()->route('document');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
