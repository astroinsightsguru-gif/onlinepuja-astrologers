<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class RechargeController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function addRechargeAmount(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $amount = DB::table('rechargeamount')->where('amount', $req->amount)->first();
                if (!$amount) {
                    $data = array(
                        'amount' => $req->amount,
                        'amount_usd' => $req->amount_usd,
                        'cashback' => $req->cashback,
                    );
                    DB::table('rechargeamount')->insert($data);
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editRechargeAmount(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $amount = DB::table('rechargeamount')->find($req['filed_id']);

                if ($amount) {
                    DB::table('rechargeamount')
                        ->where('id', $req['filed_id'])
                        ->update([
                            'amount' => $req['amount'],
                            'amount_usd' => $req['amount_usd'],
                            'cashback' => $req['cashback']
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

    // Get Skill Api

    public function getRechargeAmount(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $rechargeAmount = DB::table('rechargeamount');
                $rechargeAmount->orderBy('id', 'DESC');
                $rechargeAmount->skip($paginationStart);
                $rechargeAmount->take($this->limit);
                $rechargeAmountCount = $rechargeAmount->count();
                $rechargeAmount = $rechargeAmount->get();

                $totalPages = ceil($rechargeAmountCount / $this->limit);
                $totalRecords = $rechargeAmountCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.recharge-amount', compact('rechargeAmount', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteRechargeAmount(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                DB::table('rechargeamount')->where('id', $request->del_id)->delete();
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
