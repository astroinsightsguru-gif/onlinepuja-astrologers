<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Commission;
use App\Models\AdminModel\CommissionType;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class CommissionController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function addCommission()
    {
        return view('pages.commission-list');
    }

    public function addCommissionApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'commissionTypeId' => 'required',
                'commission' => 'required|numeric|max:100',
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            $commission = Commission::query()->where('astrologerId', $req->astrologerId)->where('commissionTypeId', $req->commissionTypeId)->first();
            if ($commission) {
                return response()->json([
                    'error' => ['Commission Already Set'],
                ]);
            }
            if (Auth::guard('web')->check()) {
                Commission::create([
                    'commissionTypeId' => $req->commissionTypeId,
                    'commission' => $req->commission,
                    'astrologerId' => $req->astrologerId,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                return redirect()->route('commissions');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    //Get Skill Api

    public function getCommission(Request $request)
    {

        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $commission = DB::table('commissions')
                    ->join('astrologers', 'astrologers.id', '=', 'commissions.astrologerId')
                    ->join('commission_types', 'commission_types.id', '=', 'commissions.commissionTypeId')
                    ->select('astrologers.name as astrologerName', 'astrologers.contactNo', 'commissions.*', 'commission_types.name as commssionType');

                $commissionCount = DB::table('commissions')
                    ->join('astrologers', 'astrologers.id', '=', 'commissions.astrologerId')
                    ->join('commission_types', 'commission_types.id', '=', 'commissions.commissionTypeId');

                if ($request->astrologerId) {
                    $commission->where('commissions.astrologerId', '=', $request->astrologerId);
                    $commissionCount->where('commissions.astrologerId', '=', $request->astrologerId);
                }
                $commissionCount = $commissionCount->count();
                $commission->skip($paginationStart);
                $commission->take($this->limit);
                $commission = $commission->get();
                $totalPages = ceil($commissionCount / $this->limit);
                $commissionType = CommissionType::query()->where('isActive', '=', 1)->get();
                $astrologer = DB::table('astrologers')->where('isActive', '=', 1)->where('isDelete', '=', 0)->where('isVerified', '=', 1)->get();
                $totalRecords = $commissionCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.commission-list', compact('commission', 'commissionType', 'astrologer', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function editCommissionApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'commissionTypeId' => 'required',
                'commission' => 'required|numeric|max:100',
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            $commission = Commission::query()->where('astrologerId', $req->astrologerId)->where('commissionTypeId', $req->commissionTypeId)->where('id','!=',$req->filed_id)->first();
            if ($commission) {
                return response()->json([
                    'error' => ['Commission Already Set'],
                ]);
            }
            if (Auth::guard('web')->check()) {
                $commission = Commission::find($req->filed_id);
                if ($commission) {
                    $currenttimestamp = Carbon::now()->timestamp;
                    $commission->commissionTypeId = $req->commissionTypeId;
                    $commission->commission = $req->commission;
                    $commission->astrologerId = $req->astrologerId;
                    $commission->updated_at = $currenttimestamp;
                    $commission->update();
                    return redirect()->route('commissions');
                }
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }


    public function deleteCommission(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $comm = Commission::find($request->del_id);
                if ($comm) {
                    // $user->isDelete = true;
                    $comm->delete();
                } else {
                    return redirect(LOGINPATH);
                }
                return redirect()->route('commissions');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
