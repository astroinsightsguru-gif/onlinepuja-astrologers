<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\SystemFlag;
use App\Models\ReferralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class AccountController extends Controller
{
    public function getMyAccount(Request $request)
    {

        if(!authcheck())
            return redirect()->route('front.home');

        $session = new Session();
        $token = $session->get('token');

        $getuserdetails = Http::withoutVerifying()->post(url('/') . '/api/getUserdetails', [
            'token' => $token,
        ])->json();

        $referral_settings=ReferralSetting::first();
        $currency=SystemFlag::where('name','currencySymbol')->first();


        return view('frontend.pages.my-account', [
            'getuserdetails'=>$getuserdetails,
            'referral_settings'=>$referral_settings,
            'currency'=>$currency,

        ]);
    }

    public function deleteAccount(Request $req)
    {
        try {

            $userId = authcheck()['id'];
            DB::table('users')->where('id', $userId)->delete();

            return redirect()->route('front.home');
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }


    public function getMyFollowing(Request $request)
    {


        if(!authcheck())
            return redirect()->route('front.home');

            Artisan::call('cache:clear');
        $session = new Session();
        $token = $session->get('token');

        $getfollowing = Http::withoutVerifying()->post(url('/') . '/api/getFollower', [
            'token' => $token,
        ])->json();
            // dd($getfollowing);
        $getsystemflag = SystemFlag::all();
        $Chatsection = $getsystemflag->where('name', 'Chatsection')->first();

        $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag',[
            'token' => $token,
        ])->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        // dd($getfollowing);


        return view('frontend.pages.my-following', [
            'getfollowing'=>$getfollowing,
            'currency' => $currency,
            'Chatsection'=>$Chatsection,

        ]);
    }

    #------------------------blocked astrologer--------------------------------
    public function getblockAstrologer(Request $request)
    {


        if(!authcheck())
            return redirect()->route('front.home');

            Artisan::call('cache:clear');
        $session = new Session();
        $token = $session->get('token');

        $getblockastro = Http::withoutVerifying()->post(url('/') . '/api/getBlockAstrologer', [
            'token' => $token,
        ])->json();

        // dd($getblockastro);


        $getsystemflag = Http::withoutVerifying()->post(url('/') . '/api/getSystemFlag')->json();
        $getsystemflag = collect($getsystemflag['recordList']);
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        // dd($getfollowing);


        return view('frontend.pages.blocked-astrologer', [
            'getblockastro'=>$getblockastro,
            'currency' => $currency,

        ]);
    }

}
