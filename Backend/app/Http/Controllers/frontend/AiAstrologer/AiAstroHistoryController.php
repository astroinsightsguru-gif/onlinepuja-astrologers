<?php

namespace App\Http\Controllers\Frontend\AiAstrologer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;
use Carbon\Carbon;
use App\Models\AiAstrologerModel\AiAstrologer;
use App\Models\AiAstrologerModel\AiChatHistory;

class AiAstroHistoryController extends Controller
{
    //
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getAiChatHistory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $chat = DB::table('ai_chat_histories as chat')
                ->join('aiastrologers as astro', 'astro.id', '=', 'chat.ai_astrologer_id')
                ->join('users as ur', 'ur.id', '=', 'chat.user_id')
                ->select('ur.name as userName', 'ur.contactNo as contactNo', 'astro.name as astrologerName', 'chat.*')
                ->orderBy('chat.id', 'DESC');

                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $chat->where(function ($q) use ($searchString) {
                        $q->where('ur.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('ur.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astro.name', 'LIKE', '%' . $searchString . '%');
                    });
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

                return view('pages.ai-astrologer.ai-chat-history-report', compact('chatHistory', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }


    public function setAiChatHistoryPage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $chat = AiChatHistory::join('aiastrologers as astr', 'astr.id', '=', 'ai_chat_histories.ai_astrologer_id')
                ->join('users as ur', 'ur.id', '=', 'ai_chat_histories.user_id')
                ->select('ur.name as userName', 'ur.contactNo as contactNo', 'astr.name as astrologerName', 'ai_chat_histories.*');
                $chat->skip($paginationStart);
                $chat->take($this->limit);
                $chat = $chat->orderBy('ai_chat_histories.id', 'DESC');


                 // Clone query for counting records
                 $countQuery = clone $chat;
                 // Date filter
                 $from_date = $request->from_date ?? null;
                 $to_date = $request->to_date ?? null;

                 if ($from_date && $to_date) {
                     $chat->whereBetween('ai_chat_histories.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                     $countQuery->whereBetween('ai_chat_histories.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                 } elseif ($from_date) {
                     $chat->where('ai_chat_histories.created_at', '>=', $from_date . ' 00:00:00');
                     $countQuery->where('ai_chat_histories.created_at', '>=', $from_date . ' 00:00:00');
                 } elseif ($to_date) {
                     $chat->where('ai_chat_histories.created_at', '<=', $to_date . ' 23:59:59');
                     $countQuery->where('ai_chat_histories.created_at', '<=', $to_date . ' 23:59:59');
                 }


                $chatHistory = $chat->get();
                // dd($chatHistory);
                $chatCount = DB::table('ai_chat_histories')
                ->join('aiastrologers', 'aiastrologers.id', '=', 'ai_chat_histories.ai_astrologer_id')
                ->join('users', 'users.id', '=', 'ai_chat_histories.user_id')
                ->count();
                $totalPages = ceil($chatCount / $this->limit);
                $totalRecords = $chatCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.ai-astrologer.ai-chat-history-report', compact('chatHistory', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function printAiPdf(Request $request)
    {
        try {
            $chatHistory = AiChatHistory::join('aiastrologers', 'aiastrologers.id', '=', 'ai_chat_histories.ai_astrologer_id')
            ->join('users', 'users.id', '=', 'ai_chat_histories.user_id')
            ->select('users.name as userName', 'users.contactNo as contactNo', 'aiastrologers.name as astrologerName', 'ai_chat_histories.*');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $chatHistory = $chatHistory->where(function ($q) use ($searchString) {
                    $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('aiastrologers.name', 'LIKE', '%' . $searchString . '%');
                });
            }
            $chatHistory = $chatHistory->orderBy('id', 'DESC')->get();
            $data = [
                'title' => 'AI Chat History Report',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'chatHistory' => $chatHistory,
            ];
            $pdf = PDF::loadView('pages.ai-astrologer.ai-chat-history-pdf', $data);
            return $pdf->download('ai-chat-history.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function exportAiChatCSV(Request $request)
    {
        $chatHistory = AiChatHistory::join('aiastrologers', 'aiastrologers.id', '=', 'ai_chat_histories.ai_astrologer_id')
        ->join('users', 'users.id', '=', 'ai_chat_histories.user_id')
        ->select('users.name as userName', 'aiastrologers.name as astrologerName', 'ai_chat_histories.*');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $chatHistory = $chatHistory->where(function ($q) use ($searchString) {
                $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                ->orWhere('aiastrologers.name', 'LIKE', '%' . $searchString . '%');
            });
        }
        $chatHistory = $chatHistory->orderBy('id', 'DESC')->get();
        $headers = array(
            "Content-type" => "text/csv",
        );
        $filename = public_path("chatHistory.csv");
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            "ID",
            "User",
            "Astrologer",
            "Chat Rate",
            "Chat Time",
            "Total Min",
            "Deduction",
        ]);
        for ($i = 0; $i < count($chatHistory); $i++) {
            $durationInSeconds = $chatHistory[$i]->chat_duration;
            $minutes = floor($durationInSeconds / 60);
            $seconds = $durationInSeconds % 60;

            $formattedDuration = $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);

            fputcsv($handle, [
                $i + 1,
                $chatHistory[$i]->userName,
                $chatHistory[$i]->astrologerName,
                $chatHistory[$i]->chat_rate,
                date('d-m-Y h:i', strtotime($chatHistory[$i]->updated_at)),
                $formattedDuration,
                $chatHistory[$i]->deduction,
            ]);
        }
        fclose($handle);
        return Response::download($filename, "chatHistory.csv", $headers);
    }

}

