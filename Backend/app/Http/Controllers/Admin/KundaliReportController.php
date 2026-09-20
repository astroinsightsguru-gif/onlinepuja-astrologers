<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

class KundaliReportController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getKundaliEarnings(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $forMatch = $request->forMatch ?? 0;
                $query = DB::table('kundalis')
                    ->join('users', 'users.id', '=', 'kundalis.createdBy')
                    ->leftjoin('astrologers', 'astrologers.userId', '=', 'users.id')
                    ->select([
                        'users.name as userName',
                        'users.contactNo as userContactNo',
                        'kundalis.name as kundaliName',
                        'kundalis.birthDate',
                        'kundalis.birthTime',
                        'kundalis.birthPlace',
                        'kundalis.pdf_type as kundaliType',
                        'kundalis.created_at',
                        'kundalis.pdf_link',
                        'kundalis.forMatch',
                        DB::raw('IF(astrologers.userId IS NOT NULL, "Astrologer", "User") as user_type')
                    ])
                    ->where('kundalis.forMatch', $forMatch)
                    ->orderBy('kundalis.id', 'DESC');

                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $query = $query->where(function ($q) use ($searchString) {
                        $q
                            ->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('kundalis.pdf_type', 'LIKE', '%' . $searchString . '%');
                    });
                }

                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $query->whereBetween('kundalis.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $query->where('kundalis.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $query->where('kundalis.created_at', '<=', $to_date . ' 23:59:59');
                }

                $kundalisCountQuery = clone $query;

                $kundaliEarnings = $query->skip($paginationStart)->take($this->limit)->get();

                $totalRecords = $kundalisCountQuery->count();
                $totalPages = ceil($totalRecords / $this->limit);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * ($page - 1)) + $this->limit, $totalRecords);

                return view('pages.kundali-earnings', compact('kundaliEarnings', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date', 'forMatch'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // -------kundali Prices--------------------------------

    public function kundaliPrices(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $kundaliAmount = DB::table('kundali_prices');
                $kundaliAmount->orderBy('id', 'DESC');
                $kundaliAmount->skip($paginationStart);
                $kundaliAmount->take($this->limit);
                $kundaliAmountCount = $kundaliAmount->count();
                $kundaliAmount = $kundaliAmount->get();

                $totalPages = ceil($kundaliAmountCount / $this->limit);
                $totalRecords = $kundaliAmountCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.kundali-prices', compact('kundaliAmount', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editkundaliAmount(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $amount = DB::table('kundali_prices')->find($req['filed_id']);

                if ($amount) {
                    DB::table('kundali_prices')
                        ->where('id', $req['filed_id'])
                        ->update([
                            'price' => $req['price'],
                            'price_usd' => $req['price_usd'],
                        ]);

                    return redirect()->back();
                } else {
                    return redirect()->back()->with('error', 'Record not found');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
