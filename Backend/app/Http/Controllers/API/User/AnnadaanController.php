<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Payment;
use App\Models\UserModel\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AnnadaanController extends Controller
{
    /**
     * Handle Annadaan / Gau Seva / Temple Charity donation.
     */
    public function donate(Request $req)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user && $req->userId) {
                $user = User::find($req->userId);
            }

            $raw = json_decode($req->getContent(), true);
            if (!is_array($raw)) $raw = [];

            $causeTitle = $req->input('causeTitle') 
                ?: ($raw['causeTitle'] ?? ($req->input('cause_title') ?: ($raw['cause_title'] ?? null)));
            $devoteeName = $req->input('devoteeName') 
                ?: ($raw['devoteeName'] ?? ($req->input('devotee_name') ?: ($raw['devotee_name'] ?? null)));
            $amount = (float)($req->input('amount') ?: ($raw['amount'] ?? 0));
            $gotra = $req->input('gotra') ?: ($raw['gotra'] ?? 'Kashyap');

            if (empty($causeTitle) || empty($devoteeName) || $amount <= 0) {
                return response()->json([
                    'error' => true,
                    'message' => 'Please provide causeTitle, devoteeName, and a valid donation amount.',
                    'status' => 400
                ], 400);
            }

            $userId = $user ? $user->id : ($req->userId ? (int)$req->userId : null);
            $amount = (float)$req->amount;
            $devoteeName = $req->devoteeName;
            $gotra = $req->gotra ?: 'Kashyap';
            $causeTitle = $req->causeTitle;

            // Check wallet if user exists
            $wallet = $userId ? DB::table('user_wallets')->where('userId', $userId)->first() : null;

            if ($wallet && $wallet->amount >= $amount) {
                // Deduct from wallet
                DB::table('user_wallets')->where('id', $wallet->id)->decrement('amount', $amount);

                // Create order_request record
                $orderId = DB::table('order_request')->insertGetId([
                    'userId' => $userId,
                    'orderType' => 'charity',
                    'payableAmount' => $amount,
                    'walletBalanceDeducted' => $amount,
                    'totalPayable' => $amount,
                    'paymentMethod' => 'wallet',
                    'orderStatus' => 'Complete',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                // Create wallet transaction
                DB::table('wallettransaction')->insert([
                    'userId' => $userId,
                    'orderId' => $orderId,
                    'amount' => $amount,
                    'isCredit' => false,
                    'transactionType' => 'charity',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

                return response()->json([
                    'status' => 200,
                    'message' => "Sacred offering of ₹{$amount} for {$causeTitle} completed successfully.",
                    'orderId' => 'OP-SEVA-' . $orderId,
                    'deductedFromWallet' => true,
                ], 200);
            }

            // Wallet insufficient or guest user: Create pending payment intent
            $inrUsdRate = DB::table('systemflag')->where('name', 'UsdtoInr')->value('value') ?: 83;
            $payment = Payment::create([
                'amount' => $amount,
                'cashback_amount' => 0,
                'gst_amount' => 0,
                'inr_usd_conversion_rate' => $inrUsdRate,
                'userId' => $userId ?: 1,
                'paymentStatus' => 'pending',
                'createdBy' => $userId ?: 1,
                'modifiedBy' => $userId ?: 1,
                'payment_for' => 'charity',
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Online payment required',
                'orderId' => 'OP-SEVA-' . $payment->id,
                'redirect' => url('/') . "/payment?payid={$payment->id}",
                'deductedFromWallet' => false,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
