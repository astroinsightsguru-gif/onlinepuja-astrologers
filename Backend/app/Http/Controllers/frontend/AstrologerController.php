<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Models\UserModel\UserWallet;

class AstrologerController extends Controller
{
    public function astrologerDetails(Request $request,$slug)
    {
        Artisan::call('cache:clear');
        // $astrologerId = $request->input('id');

        $session = new Session();
        $token = $session->get('token');

    	$userId='';
		if(authcheck()){
		$userId=authcheck()['id'];
		}

        $getAstrologerId= DB::table('astrologers')->where('slug', $slug)->first();
        // dd($slug);
        $astrologerId=$getAstrologerId->id;

        $getAstrologer = Http::withoutVerifying()->post(url('/') . '/api/getAstrologerById', [
            'astrologerId' => $getAstrologerId->id,'userId'=>$userId,'token' => $token,
        ])->json();



        $getIntakeForm = Http::withoutVerifying()->post(url('/') . '/api/chatRequest/getIntakeForm', [
            'token' => $token,
        ])->json();

        $getfollower = DB::table('astrologer_followers')
                ->where('userId', '=', $userId)
                ->where('astrologerId', '=', $astrologerId)
                ->first();


        $session = DB::table('callrequest')
        ->where('userId', '=', $userId)
        ->where('astrologerId', '=', $astrologerId)
        ->where('callStatus', '=', 'Pending')
        ->get();
        $isCallSessionavailable = true;
        if ($session && count($session) > 0) {
            $isCallSessionavailable = false;
        }

        $session = DB::table('chatrequest')
        ->where('userId', '=', $userId)
        ->where('astrologerId', '=', $astrologerId)
        ->where('chatStatus', '=', 'Pending')
        ->get();
            $isChatSessionavailable = true;
            if ($session && count($session) > 0) {
                $isChatSessionavailable = false;
            }


        $getGift = Http::withoutVerifying()->post(url('/') . '/api/getGift',[
            'token' => $token,
        ])->json();

        $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag',[
            'token' => $token,
        ])->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();
        $chatsection = $getsystemflag->where('name', 'Chatsection')->first();
        $callsection = $getsystemflag->where('name', 'Callsection')->first();


         $userWalletAmount = 0;
        if (authcheck()) {
            $userWalletAmount = UserWallet::where('userId', authcheck()->id)->value('amount') ?? 0;
        }
        return view('frontend.pages.astrologer-details', [
            'getAstrologer' => $getAstrologer,
            'getGift' => $getGift,
            'getIntakeForm' => $getIntakeForm,
            'isCallSessionavailable' => $isCallSessionavailable,
            'isChatSessionavailable' => $isChatSessionavailable,
            'currency' => $currency,
            'chatsection' => $chatsection,
            'callsection' => $callsection,
            'getfollower'=> $getfollower,
            'walletAmount' => $userWalletAmount

        ]);
    }

    public function getLiveAstro(Request $request)
    {
        Artisan::call('cache:clear');
        $liveastro = Http::withoutVerifying()->post(url('/') . '/api/liveAstrologer/get')->json();

        return view('frontend.pages.live-astrologers', [
            'liveastro' => $liveastro,

        ]);
    }
    public function LiveAstroDetails(Request $request)
    {
        Artisan::call('cache:clear');


            $liveAstrologer = DB::table('liveastro')
                ->join('astrologers', 'astrologers.id', '=', 'liveastro.astrologerId')
                ->where('liveastro.isActive', '=', true)
                ->select('astrologers.name', 'astrologers.profileImage', 'liveastro.*', 'astrologers.charge', 'astrologers.videoCallRate')
                ->orderBy('id', 'DESC')
                ->where('liveastro.astrologerId',$request->astrologerId)
                ->first();

                if(!$liveAstrologer)
                    return redirect()->route('front.home');

                $wallet_amount ='';
                if(authcheck())
                    $userWalletAmount = UserWallet::where('userId', authcheck()->id)->value('amount') ?? 0;
                    $wallet_amount = $userWalletAmount;


                $getGift = Http::withoutVerifying()->post(url('/') . '/api/getGift')->json();

                $getSystemFlag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag');
                $recordList = $getSystemFlag['recordList'];

                $agoraAppIdValue = $agorcertificateValue = $agorsecretValue = null;

                foreach ($recordList as $item) {
                    switch ($item['name']) {
                        case 'AgoraAppId':
                            $agoraAppIdValue = $item['value'];
                            break;
                        case 'AgoraAppCertificate':
                            $agorcertificateValue = $item['value'];
                            break;
                        case 'AgoraSecret':
                            $agorsecretValue = $item['value'];
                            break;
                    }
                }

                // dd($agoraAppIdValue, $agorcertificateValue, $agorsecretValue);



        $getLiveUser = Http::withoutVerifying()->post(url('/') . '/api/getLiveUser', [
            'channelName' => $liveAstrologer->channelName,
        ])->json();

        $id='';
        if(authcheck()){
            $id=authcheck()['id'];
        }


        $RtmToken = Http::withoutVerifying()->post(url('/') . '/api/generateToken', [
            'appID' => $agoraAppIdValue,
            'appCertificate' => $agorcertificateValue,
            'user' => 'liveAstrologer_' . $id,
            'channelName' =>$liveAstrologer->channelName
        ])->json();

        $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();


        // dd($generateRtmToken);



        return view('frontend.pages.live-astrologer-details', [
            'liveAstrologer' => $liveAstrologer,
            'wallet_amount' => $wallet_amount,
            'getGift' => $getGift,
            'agoraAppIdValue' => $agoraAppIdValue,
            'getLiveUser' => $getLiveUser,
            'RtmToken' => $RtmToken,
            'currency' => $currency,

        ]);
    }
}
