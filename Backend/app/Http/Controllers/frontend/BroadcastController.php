<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\AgoraToken\RtcTokenBuilder;
use App\Models\AstrologerModel\Astrologer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Puja;
use App\Models\PujaOrder;
use App\Models\UserModel\User;

class BroadcastController extends Controller
{
    public function create(Request $request, $puja_id = 0)
    {
        // Assuming a view route exists

        $puja = Puja::findOrFail($puja_id);

        $roomId = "puja_" . encrypt_to($puja_id);

        return route('broadcast.view', ['roomId' => $roomId]);

        return view('frontend.pages.puja-broadcast', ['roomId' => $roomId, 'broadcastLink' => $broadcastLink, 'puja' => $puja]);

        // return response()->json([
        //     'roomId' => $roomId,
        //     'broadcastLink' => $broadcastLink,
        // ]);
    }

    private function generateAgoraToken($roomId)
    {

        $privilegeExpiredTs = Carbon::now()->timestamp + 600;
        $rtcTokenController = new RtcTokenBuilder;
        $rtcToken = $rtcTokenController->buildTokenWithUid('7136c501bc034eb08d062a00d9a31719', 'e94d1ab535494daca1687e76f166c00b', $roomId, 111, 1, $privilegeExpiredTs);
        return $rtcToken;
    }


    public function view($roomId, $userid)
    {
        $OrdId = decrypt_to(str_replace('puja_', '', $roomId));
        $userid = decrypt_to($userid);

        $pujaOrder = PujaOrder::where('id', $OrdId)->where(function ($q) use ($userid) {
            $q->orWhere('user_id', $userid);
            $q->orWhere('astrologer_id', $userid);
        })->first();

        if (!$pujaOrder || !$pujaOrder->exists())
            abort(404);

        $puja_id = $pujaOrder->puja_id;
        $currentDatetime = Carbon::now();

        $puja = Puja::findOrFail($puja_id);

        // --- NEW LOGIC ---
        $pujaEndTime = Carbon::parse($puja->puja_end_datetime);
        if (!$puja->isPujaEnded) {
            // If NOT ended manually, add 2 extra hours
            $pujaEndTime = $pujaEndTime->addHours(2);
        }

        if ($currentDatetime->greaterThan($pujaEndTime)) {
            // Puja broadcast is considered ended
            return view('frontend.pages.puja-broadcast', ['puja' => $puja]);
        }
        // --- END NEW LOGIC ---

        // Puja is live
        $roomuid = $userid;
        if ($pujaOrder->astrologer_id == $userid) {
            $roomuid = 'astrologer';
            $pujaOrder->update(['astrologer_joined_at' => now()]);
        }

        $agoraAppIdValue = DB::table('systemflag')->where('name', 'AgoraAppIdForPuja')->first();
        $token = null;

        return view('frontend.pages.broadcast', [
            'roomId' => $roomId,
            'token' => $token,
            'agoraAppIdValue' => $agoraAppIdValue,
            'puja' => $puja,
            'roomuid' => $roomuid
        ]);
    }

    #-------------------------------------------------------------------------------------------------------------------------
    // End Puja By Astrologer
    public function endPujabyAstrologer(Request $request)
    {
        $request->validate([
            'puja_id' => 'required|exists:pujas,id',
        ]);

        $puja = Puja::findOrFail($request->puja_id);
        $puja->isPujaEnded = true;
        $puja->actual_puja_endtime = Carbon::now();
        $puja->save();

        return response()->json([
            'status' => true,
            'message' => 'Puja broadcast ended successfully.',
        ]);
    }


    #--------------------------------------------------------------------------------------------------------------------------------------------

    public function pujaUpdateCommission()
    {
        $currentDatetime = Carbon::now();
        $puja = PujaOrder::where('astrologer_joined_at', '!=', null)
            ->where('puja_order_status', '!=', 'completed')
            ->where('is_puja_approved', '!=', 'approved')
            ->where('puja_end_datetime', '<=', $currentDatetime)
            ->get();

        if ($puja) {
            foreach ($puja as $pujacommissions) {

                /*
                $user_country = User::where('id', $pujacommissions->user_id)->where('country', 'India')->first();
                if ($user_country) {
                    $order_price = convertusdtoinr($pujacommissions->order_price);
                } else {
                    $order_price = $pujacommissions->order_price;
                }
                */

                // new added by bhushan bose on 03 june 2025 start -----
                $user_country = User::where('id', $pujacommissions->user_id)->where('countryCode', '+91')->first();
                $order_price = $user_country ? $pujacommissions->order_price : convertusdtoinr($pujacommissions->order_price);
                // new added by bhushan bose on 03 june 2025 close -----

                $deduction = $order_price;

                $commission = DB::table(table: 'commissions')
                    ->where('commissionTypeId', '=', '6')
                    ->where('astrologerId', '=', $pujacommissions->astrologer_id)
                    ->get();

                if ($commission && count($commission) > 0) {
                    $adminCommission = ($commission[0]->commission * $deduction) / 100;
                } else {
                    $syscommission = DB::table('systemflag')->where('name', 'PujaCommission')->select('value')->get();

                    $adminCommission = ($syscommission[0]->value * $deduction) / 100;
                }
                $astrologerCommission = $deduction - $adminCommission;

                $astrologerId = $pujacommissions->astrologer_id;
                $inr_usd_conv_rate = DB::table('systemflag')->where('name', 'UsdtoInr')->select('value')->first();
                // $astrologercountry = Astrologer::where('id', $astrologerId)->where('country', 'India')->first();    // commented
                $astrologercountry = Astrologer::where('id', $astrologerId)->where('countryCode', '+91')->first();    // added
                $astrologerUserId = DB::table('astrologers')
                    ->where('id', '=', $astrologerId)
                    ->selectRaw('userId,name')
                    ->get();
                $astrologerWallet = DB::table('user_wallets')
                    ->where('userId', '=', $astrologerUserId[0]->userId)
                    ->get();

                $astrologerWalletData = array(
                    // 'amount' => $astrologerWallet && count($astrologerWallet) > 0
                    //     ? $astrologerWallet[0]->amount + ($astrologercountry ? ($astrologerCommission * $inr_usd_conv_rate->value) : $astrologerCommission)
                    //     : ($astrologercountry ? ($astrologerCommission * $inr_usd_conv_rate->value) : $astrologerCommission),
                    'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $astrologerCommission : $astrologerCommission,
                    'userId' => $astrologerUserId[0]->userId,
                    'createdBy' => $astrologerUserId[0]->userId,
                    'modifiedBy' => $astrologerUserId[0]->userId,
                );

                // dd($astrologerWalletData);
                if ($astrologerWallet && count($astrologerWallet) > 0) {
                    DB::Table('user_wallets')
                        ->where('userId', '=', $astrologerUserId[0]->userId)
                        ->update($astrologerWalletData);
                } else {
                    DB::Table('user_wallets')->insert($astrologerWalletData);
                }

                $astrologerWalletTransaction = array(
                    'amount' => $astrologerCommission,
                    'userId' => $astrologerUserId[0]->userId,
                    'createdBy' => $astrologerUserId[0]->userId,
                    'modifiedBy' => $astrologerUserId[0]->userId,
                    // 'orderId' => $pujacommissions->id,
                    'isCredit' => true,
                    'transactionType' => 'pujaOrder',
                    "astrologerId" => $astrologerId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'inr_usd_conversion_rate' => $inr_usd_conv_rate->value,
                );
                DB::table('wallettransaction')->insert($astrologerWalletTransaction);

                $gst_percent = DB::table('systemflag')->where('name', 'Gst')->first();
                $orderRequest = array(
                    'userId' => $pujacommissions->user_id,
                    'astrologerId' => $astrologerId,
                    'orderType' => 'puja',
                    'puja_id' => $pujacommissions->puja_id,
                    'package_id' => $pujacommissions->package_id,
                    'orderAddressId' => $pujacommissions->address_id,
                    'payableAmount' => $pujacommissions->order_price,
                    'gstPercent' => $gst_percent->value,
                    'totalPayable' => $pujacommissions->order_total_price,
                    'orderStatus' => 'Complete',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'inr_usd_conversion_rate' => $inr_usd_conv_rate->value,

                );
                DB::Table('order_request')->insert($orderRequest);
                $Orderid = DB::getPdo()->lastInsertId();

                // Commission
                if ($commission && count($commission) > 0) {
                    $adminGetCommission = array(
                        'commissionTypeId' => 6,
                        "amount" => $adminCommission,
                        "commissionId" => $commission && count($commission) > 0 ? $commission[0]->id : null,
                        "orderId" => $Orderid,
                        "createdBy" =>   $pujacommissions->user_id,
                        "modifiedBy" =>  $pujacommissions->user_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'inr_usd_conversion_rate' => $inr_usd_conv_rate->value,

                    );
                    DB::table('admin_get_commissions')->insert($adminGetCommission);
                } elseif ($syscommission && count($syscommission) > 0) {
                    $adminGetCommission = array(
                        'commissionTypeId' => 6,
                        "amount" => $adminCommission,
                        "commissionId" => null,
                        "orderId" => $Orderid,
                        "createdBy" =>  $pujacommissions->user_id,
                        "modifiedBy" =>  $pujacommissions->user_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'inr_usd_conversion_rate' => $inr_usd_conv_rate->value,
                    );
                    DB::table('admin_get_commissions')->insert($adminGetCommission);
                }

                $pujacommissions->update(['puja_order_status' => 'completed', 'is_puja_approved' => 'approved','isPujaEnded'=>true,'actual_puja_endtime'=>Carbon::now()]);
            }
        }
        return response()->json([
            'message' => 'Puja Money Added Successfully',
            'status' => 200,
        ], 200);
    }
}
