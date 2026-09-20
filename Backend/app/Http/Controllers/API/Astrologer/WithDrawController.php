<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrologer;
use App\Models\AstrologerModel\WithdrawRequest;
use App\Models\UserModel\UserWallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class WithDrawController extends Controller
{
    public function sendWithdrawRequest(Request $req)
    {
        try {
            $user = Auth::guard('api')->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $userId = $user->id;
            $wallet = DB::table('user_wallets')->where('userId', $userId)->first();

            if (!$wallet) {
                return response()->json([
                    'message' => 'Wallet not found',
                    'status' => 404,
                ], 404);
            }

            // Validate minimum withdraw amount
            if ($req->withdrawAmount < 100) {
                return response()->json([
                    'message' => 'Minimum withdraw amount should be 100',
                    'status' => 400,
                ], 400);
            }

            if ($req->withdrawAmount > $wallet->amount) {
                return response()->json([
                    'message' => 'Withdrawal amount exceeded your wallet balance',
                    'status' => 400,
                ], 400);
            }

            // Validation
            $validator = Validator::make($req->all(), [
                'astrologerId' => 'required|exists:astrologers,id',
                'withdrawAmount' => 'required|numeric',
                'paymentMethod' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors(),
                    'status' => 400,
                ], 400);
            }

            // Fetch TDS Commission Percentage
            $commissionFlag = DB::table('systemflag')
                ->where('name', 'TDSCommission')
                ->where('isActive', 1)
                ->where('isDelete', 0)
                ->first();

            $commissionPercent = $commissionFlag ? floatval($commissionFlag->value) : 0;

            $tdsAmount = ($req->withdrawAmount * $commissionPercent) / 100;

            $payAmount = $req->withdrawAmount - $tdsAmount;

            $astrologer = DB::table('astrologers')->where('id', $req->astrologerId)->first();
            $panCard = $astrologer->pan_card ?? null;

            $withdrawRequest = [
                'astrologerId' => $req->astrologerId,
                'withdrawAmount' => $req->withdrawAmount,
                'pan_card' => $panCard,
                'status' => 'Pending',
                'paymentMethod' => $req->paymentMethod,
                'upiId' => $req->upiId ?? null,
                'accountNumber' => $req->accountNumber ?? null,
                'ifscCode' => $req->ifscCode ?? null,
                'accountHolderName' => $req->accountHolderName ?? null,
                'tds_pay_amount' => $tdsAmount,
                'pay_amount' => $payAmount,
                'createdBy' => $userId,
                'modifiedBy' => $userId,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];

            DB::table('withdrawrequest')->insert($withdrawRequest);
            DB::table('user_wallets')
                ->where('id', $wallet->id)
                ->update([
                    'amount' => $wallet->amount - $req->withdrawAmount,
                ]);

            return response()->json([
                'message' => 'Withdrawal request sent successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function updateWithdrawRequest(Request $req)
    {
        // DB::beginTransaction();
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'id',
                'astrologerId',
                'withdrawAmount'
            );
            $validator = Validator::make($data, [
                'id' => 'required',
                'astrologerId' => 'required',
                'withdrawAmount' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $withdrawRequest = array(
                'astrologerId' => $req->astrologerId,
                'withdrawAmount' => $req->withdrawAmount,
                'status' => 'Pending',
                'paymentMethod' => $req->paymentMethod,
                'upiId' => $req->upiId,
                'accountNumber' => $req->accountNumber,
                'ifscCode' => $req->ifscCode,
                'accountHolderName' => $req->accountHolderName,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            );
            $withDrawAmount = DB::table('withdrawrequest')
                ->where('id', $req->id)
                ->get();

            $amount = DB::table('user_wallets')
                ->join('astrologers', 'astrologers.userId', '=', 'user_wallets.userId')
                ->where('astrologers.id', '=', $req->astrologerId)
                ->select('amount', 'user_wallets.id')
                ->get();

            if ($req->withdrawAmount > $withDrawAmount[0]->withdrawAmount + $amount[0]->amount) {
                return response()->json([
                    'message' => 'Withdrawl Amount Exceeded',
                    'status' => 400,
                ], 400);
            }
            DB::table('withdrawrequest')
                ->where('id', $req->id)
                ->update($withdrawRequest);

            $userWallet = array(
                'amount' => ($amount[0]->amount + $withDrawAmount[0]->withdrawAmount) - $req->withdrawAmount,
            );

            DB::table('user_wallets')
                ->where('id', $amount[0]->id)
                ->update($userWallet);

            return response()->json([
                'message' => 'Request update & send to admin successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            // DB::rollback();
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getWithdrawRequest(Request $req)
    {
        try {
            $withdrawRequestQuery = WithdrawRequest::join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId')
                ->select('withdrawrequest.*', 'astrologers.name', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.userId');

            $astroUserId = Astrologer::where('id', $req->astrologerId)->pluck('userId')->first();
            $mergedData = collect();  // Initialize as an empty collection

            if ($req->astrologerId) {
                $withdrawRequestQuery->where('astrologers.id', $req->astrologerId);

                // Fetch wallet transactions
                $walletTransaction = WalletTransaction::join('astrologers', 'astrologers.userId', '=', 'wallettransaction.userId')
                    ->select('wallettransaction.*')
                    ->where('astrologers.id', $req->astrologerId)
                    ->get();

                // Fetch payment data
                $payment = DB::table('payment')
                    ->join('astrologers', 'astrologers.userId', '=', 'payment.userId')
                    ->select('payment.*', DB::raw("'recharge' as transactionType"))
                    ->where('astrologers.id', $req->astrologerId)
                    ->orderBy('payment.id', 'DESC')
                    ->get();

                // Merge and sort both collections
                $mergedData = collect($walletTransaction)
                    ->merge($payment)
                    ->sortByDesc('created_at')
                    ->values();
            }

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $withdrawRequestQuery->skip($req->startIndex)->take($req->fetchRecord);
            }

            $withdrawRequests = $withdrawRequestQuery->orderBy('withdrawrequest.id', 'DESC')->get();

            // Fetch wallet and transaction summaries
            $amount = 0;
            $totalPending = 0;
            $totalEarning = 0;
            $totalWithdrawn = 0;

            if ($req->astrologerId) {
                $amount = UserWallet::where('userId', $astroUserId)->pluck('amount')->first();

                $totalWithdrawn = WithdrawRequest::where('astrologerId', $req->astrologerId)
                    ->where('status', 'Released')
                    ->sum('withdrawAmount');

                $totalPending = WithdrawRequest::where('status', 'Pending')
                    ->where('astrologerId', $req->astrologerId)
                    ->sum('withdrawAmount');

                $totalEarning = WithdrawRequest::where('astrologerId', $req->astrologerId)
                    ->where('status', '!=', 'Cancelled')
                    ->sum('withdrawAmount');
            }

            $withdrawRequestCount = WithdrawRequest::join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId')
                ->when($req->astrologerId, function ($query) use ($req) {
                    $query->where('astrologers.id', $req->astrologerId);
                })
                ->count();
            $paymentSum = DB::table('payment')->where('userId', $astroUserId)->where('paymentStatus', 'success')->sum('amount');

            $response = [
                'withdrawl' => $withdrawRequests,
                'walletTransaction' => $mergedData,
                'walletAmount' => $amount,
                'totalPending' => (string) $totalPending,
                'totalEarning' => $totalEarning + $amount - $paymentSum,
                'withdrawAmount' => $totalWithdrawn,
            ];

            return response()->json([
                'message' => 'Get Withdrawal request successfully',
                'status' => 200,
                'recordList' => $response,
                'totalRecords' => $withdrawRequestCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ]);
        }
    }

    public function releaseAmount(Request $req)
    {
        try {
            $data = $req->only(
                'id',
            );
            $validator = Validator::make($data, [
                'id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $withdrawRequest = array(
                'status' => 'Released',
            );
            DB::table('withdrawrequest')
                ->where('id', $req->id)
                ->update($withdrawRequest);
            return response()->json([
                'message' => 'Request update & send to admin successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getWithdrawMethod(Request $req)
    {
        try {
            $withdrawMethod = DB::table('withdrawmethods')
                ->get();

            return response()->json([
                'message' => 'Get Withdrawl method successfully',
                'status' => 200,
                'recordList' => $withdrawMethod,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    // public function getWithdrawMethod(Request $req)
    // {
    //     try {
    //         $astrologerId = $req->astrologerId;

    //         $withdrawMethods = DB::table('withdrawmethods')
    //             ->get();

    //         if ($withdrawMethods->isEmpty()) {
    //             return response()->json([
    //                 'error' => true,
    //                 'message' => 'No withdrawal methods found.',
    //                 'status' => 404,
    //             ], 404);
    //         }

    //         // Find the method with method_id 1 (Bank Account)
    //         $bankMethod = $withdrawMethods->where('method_id', 1)->first();

    //         if (!$bankMethod) {
    //             return response()->json([
    //                 'error' => true,
    //                 'message' => 'Bank Account withdrawal method not found.',
    //                 'status' => 404,
    //             ], 404);
    //         }

    //         // Get bank details for the specified astrologer ID
    //         $bankDetails = DB::table('withdrawrequest')
    //         ->where('astrologerId', $astrologerId)
    //         ->where('paymentMethod', 1)
    //         ->latest('created_at')
    //         ->select('accountNumber', 'ifscCode', 'accountHolderName')
    //         ->first();

    //         // Add bank details to the first method if it's the bank account method
    //         if ($bankDetails) {
    //             $bankMethod->bankDetails = $bankDetails;
    //         }

    //         return response()->json([
    //             'message' => 'Get Withdrawal methods successfully',
    //             'status' => 200,
    //             'recordList' => $withdrawMethods,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => true,
    //             'message' => $e->getMessage(),
    //             'status' => 500,
    //         ], 500);
    //     }
    // }
}
