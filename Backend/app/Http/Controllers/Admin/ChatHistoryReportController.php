<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\ChatRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;

class ChatHistoryReportController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getChatHistory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $chat = ChatRequest::join('astrologers as astro', 'astro.id', '=', 'chatrequest.astrologerId')
                    ->join('users as ur', 'ur.id', '=', 'chatrequest.userId')
                    ->select('ur.name as userName', 'ur.contactNo as contactNo', 'astro.name as astrologerName', 'chatrequest.*')
                    ->orderBy('chatrequest.id', 'DESC');

                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $chat->where(function ($q) use ($searchString) {
                        $q
                            ->where('ur.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('ur.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astro.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astro.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }

                // Clone query for counting records
                $countQuery = clone $chat;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $chat->whereBetween('chatrequest.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('chatrequest.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $chat->where('chatrequest.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('chatrequest.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $chat->where('chatrequest.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('chatrequest.created_at', '<=', $to_date . ' 23:59:59');
                }

                // Count the total records
                $totalRecords = $chat->count();

                // Calculate total pages
                $totalPages = ceil($totalRecords / $this->limit);

                // Adjust page number if it exceeds total pages
                $page = min($page, $totalPages);

                // Retrieve chat history for the current page
                $chatHistory = $chat->skip($paginationStart)->take($this->limit)->get();

                // Calculate start and end records for the current page
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.chat-history-report', compact('chatHistory', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function setChatHistoryPage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $chat = DB::table('chatrequest as chat')
                    ->join('astrologers as astr', 'astr.id', '=', 'chat.astrologerId')
                    ->join('users as ur', 'ur.id', '=', 'chat.userId')
                    ->select('ur.name as userName', 'ur.contactNo as contactNo', 'astr.name as astrologerName', 'chat.*');
                $chat->skip($paginationStart);
                $chat->take($this->limit);
                $chat = $chat->orderBy('chat.id', 'DESC');
                $chatHistory = $chat->get();
                $chatCount = DB::table('chatrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
                    ->join('users', 'users.id', '=', 'chatrequest.userId')
                    ->count();
                $totalPages = ceil($chatCount / $this->limit);
                $totalRecords = $chatCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.chat-history-report', compact('chatHistory', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
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
            $chatHistory = ChatRequest::join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
                ->join('users', 'users.id', '=', 'chatrequest.userId')
                ->select('users.name as userName', 'users.contactNo as contactNo', 'astrologers.name as astrologerName', 'chatrequest.*');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $chatHistory = $chatHistory->where(function ($q) use ($searchString) {
                    $q
                        ->where('users.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $chatHistory = $chatHistory->orderBy('id', 'DESC')->get();
            $data = [
                'title' => 'Chat History Report',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'chatHistory' => $chatHistory,
            ];
            $pdf = PDF::loadView('pages.chatHistoryPdf', $data);
            return $pdf->download('chatHistory.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function exportChatCSV(Request $request)
    {
        $chatHistory = ChatRequest::join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
            ->join('users', 'users.id', '=', 'chatrequest.userId')
            ->select('users.name as userName', 'astrologers.name as astrologerName', 'chatrequest.*');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $chatHistory = $chatHistory->where(function ($q) use ($searchString) {
                $q
                    ->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $chatHistory = $chatHistory->orderBy('id', 'DESC')->get();
        $headers = array(
            'Content-type' => 'text/csv',
        );
        $filename = public_path('chatHistory.csv');
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'ID',
            'User',
            'Astrologer',
            'Chat Rate',
            'Chat Time',
            'Total Min',
            'Deduction',
        ]);
        for ($i = 0; $i < count($chatHistory); $i++) {
            fputcsv($handle, [
                $i + 1,
                $chatHistory[$i]->userName,
                $chatHistory[$i]->astrologerName,
                number_format($chatHistory[$i]->chatRate, 2),
                date('d-m-Y h:i', strtotime($chatHistory[$i]->updated_at)),
                $chatHistory[$i]->totalMin,
                number_format($chatHistory[$i]->deduction, 2),
            ]);
        }
        fclose($handle);
        return Response::download($filename, 'chatHistory.csv', $headers);
    }
}
