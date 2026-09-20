<?php

namespace App\Http\Controllers\API\AiAstrologer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiAstrologerModel\AiAstrologer;
use App\Models\AiAstrologerModel\AiChatHistory;
use App\Models\UserModel\UserWallet;
use App\Models\Skill;
use App\Models\AstrologerCategory;
use Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Auth;
use DB;
use Symfony\Component\HttpFoundation\Session\Session;

class ApiAiAstrologerController extends Controller
{
    public function aiAstrologerList(){
        try {
            $userId='';
            if(authcheck()){
                $userId=authcheck()['id'];
            }
            
            $session = new Session();
            $token = $session->get('token');
            $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag',[
                'token' => $token,
                ])->json();
                $getsystemflag  = collect($getsystemflag['recordList']);
                $currency       = $getsystemflag->where('name', 'currencySymbol')->first();
                
                $aiAstrologers                      = AiAstrologer::whereNull('type')
                ->orderBy('id','DESC')
                ->get()
                ->map(function($astro) {
                    $astro->primary_skills_names    = $astro->primarySkills()->pluck('name'); 
                    $astro->all_skills_names        = $astro->allSkills()->pluck('name'); 
                    $astro->categories_names        = $astro->astrologerCategories()->pluck('name'); 
                    return $astro;
                });

                $isFreeChat = DB::table('systemflag')->where('name', 'FirstFreeChat')->select('value')->first(); // 1
                $isFreeAvailable=true;
                if ($isFreeChat->value == 1) {
                    if ($userId) {
                        $isChatRequest = DB::table('ai_chat_histories')->where('user_id', $userId)->first();
                        if ($isChatRequest) {
                            $isFreeAvailable = false;
                        } else {
                            $isFreeAvailable = true;
                        }
                    }
                } else {
                    $isFreeAvailable = false;
                }
                $wallet_amount = DB::table('user_wallets')->where('userId', $userId)->value('amount');
                
                return response()->json([
                    'recordList'        => $aiAstrologers,
                    'currency'          =>$currency,
                    'isFreeAvailable'   =>$isFreeAvailable,
                    'wallet_amount'     =>$wallet_amount,
                    'status' => 200,
                ], 200);
                
            } catch (Exception $e) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage(),
                    'status' => 500,
                ], 500);
            }
        }
        
        public function aiChatButton(Request $request){
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            
            $astrologerId               = $request->astrologerId;
            $charge                     = $request->charge;
            $chat_duration              = $request->chat_duration;
            
            $userName                   = authcheck()['name'];
            $userId                     = authcheck()['id'];
            
            $firstFreerecharge          = DB::table('systemflag')->where('name', 'FirstFreeChatRecharge')->select('value')->first();
            $minAmount                  = DB::table('systemflag')->where('name', 'MinAmountFreeChatCall')->select('value')->first();
            $wallets                    = DB::table('user_wallets')->where('userId', $userId)->first();
            $walletAmount               = $wallets ? (float) $wallets->amount : 0; 
            
            $isChatRequest              = DB::table('ai_chat_histories')->where('user_id', $userId)->first();
            
            if($isChatRequest == null){
                return response()->json([
                    'message'           => 'Please wait for a second!',
                    'userId'            =>$userId,
                    'astrologerId'      =>$astrologerId,
                    'charge'            =>$charge,
                    'chat_duration'     =>$chat_duration,
                    'userName'          =>$userName,
                    'status'            => 200,
                ], 200);
            }else{
                $charge                 = $request->charge;
                $chatDuration           = $request->chat_duration/60;
                $needAmount             =$charge * $chatDuration;
                
                if ($needAmount > $walletAmount) {
                    return response()->json([
                        'message'         => 'Insufficient balance. Please recharge your wallet.',
                        'needAmount'    =>(int)$needAmount,
                        'userBalance'   =>$walletAmount,
                        'status'        => 400,
                    ], 400);
                }else{
                    return response()->json([
                        'message'       => 'Please wait for a second!',
                        'userId'        =>$userId,
                        'astrologerId'  =>$astrologerId,
                        'charge'        =>$charge,
                        'chat_duration' =>$chat_duration,
                        'userName'      =>$userName,
                        'status'        => 200,
                    ], 200);
                }
            }
            return response()->json([
                'message' => 'Something went wrong',
                'status'  => 500,
                ], 500);
        }
        
        
        public function aiChattingPage(Request $request)
        {       
            $astrologerId               = $request->query('astrologerId');
            $charge                     = $request->query('charge');
            $chatDuration               = $request->query('chat_duration')/60;
            $astrologer                 = AiAstrologer::where('id', $astrologerId)->first();
            return response()->json([
                'astrologerId'          =>$astrologerId,
                'charge'                =>$charge,
                'chatDuration'          =>$chatDuration,
                'recordList'            =>$astrologer,
                'status'                => 200,
            ], 200);
        }
        
        public function storeAiChatHistory(Request $request){
            
            $aiAstrologer               = AiAstrologer::find($request->astrologer_id);
            
            // $user_country=User::where('id',authcheck()['id'])->where('country','India')->first();   // commented
            $user_country=User::where('id',authcheck()['id'])->where('countryCode','+91')->first();   // added
            $inr_usd_conv_rate = DB::table('systemflag')->where('name','UsdtoInr')->select('value')->first();
            $actualDurationInSeconds    = $request->actualDuration;
            // if($user_country){
            //     $aiAstrologer->chat_charge=convertinrtousd($aiAstrologer->chat_charge);
            // }
            $aiAstrologer->chat_charge = $user_country ? $aiAstrologer->chat_charge : convertusdtoinr($aiAstrologer->chat_charge);
            
            $actualDurationInSeconds    = $request->actualDuration;
            $deduction                  = ceil($actualDurationInSeconds / 60); 
            $deductionAmount            = $deduction * $aiAstrologer->chat_charge; 
            $isFree                     = DB::table('ai_chat_histories')->where('user_id', authcheck()['id'])->first();
            
            if(!empty($isFree)){
                $aiChatHistory = AiChatHistory::create([
                    'user_id'           => authcheck()['id'],
                    'ai_astrologer_id'  => $request->astrologer_id,
                    'chat_duration'     => $request->actualDuration,
                    'chat_min'          => $request->chatDuration,
                    'chat_rate'         => $aiAstrologer->chat_charge,
                    'deduction'         => $deductionAmount,
                    'is_free'           => 0,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                    'inr_usd_conversion_rate'=>$inr_usd_conv_rate->value,
                    
                ]);
                $wallet_deduction = UserWallet::where('userId', authcheck()['id'])->first();
                if ($wallet_deduction) {
                    // $wallet_deduction->amount -= ($user_country) ? ($deductionAmount * $inr_usd_conv_rate->value) : $deductionAmount;
                    $wallet_deduction->amount -= $deductionAmount;
                    $wallet_deduction->save();
                } else {
                    return response()->json([
                        'message'       => 'Wallet not found.',
                        'status'        => 404,
                        ], 404);
                }
                
                // $wallet_deduction = UserWallet::where('userId', authcheck()['id'])->first();
                // if ($wallet_deduction) {
                //     $wallet_deduction->amount -= $deductionAmount;
                //     $wallet_deduction->save(); 
                // } else {
                //     return response()->json(['error' => 'Wallet not found.'], 404);
                // }
            }else{
                $aiChatHistory = AiChatHistory::create([
                    'user_id'           => authcheck()['id'],
                    'ai_astrologer_id'  => $request->astrologer_id,
                    'chat_duration'     => $request->actualDuration,
                    // 'chat_min'       => $request->chatMin,
                    'chat_rate'         => $aiAstrologer->chat_charge,
                    'is_free'           => 0,
                    // 'deduction'      => $deduction,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
                
                $wallet_deduction = UserWallet::where('userId', authcheck()['id'])->first();
                if ($wallet_deduction) {
                    // $wallet_deduction->amount -= ($user_country) ? ($deductionAmount * $inr_usd_conv_rate->value) : $deductionAmount;
                    $wallet_deduction->amount -= $deductionAmount; 
                    $wallet_deduction->save(); 
                } else {
                    return response()->json([
                        'message'       => 'Wallet not found.',
                        'status'        => 404,
                        ], 404);
                }
            }
            
            return response()->json([
                'message'               => 'Chat ended successfully.',
                'data'                  => $aiChatHistory,
                'walletAmount'          => $wallet_deduction->amount,
                'status' => 200,
            ], 200);
        }
    }
    