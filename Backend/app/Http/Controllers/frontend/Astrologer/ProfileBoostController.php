<?php

namespace App\Http\Controllers\frontend\Astrologer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class ProfileBoostController extends Controller
{
    public function history(Request $request)
    {
        try {
            if(!astroauthcheck()){
                return back()->with('error','Unauthenticated');
            }
            $session = new Session();
            $token = $session->get('astrotoken');
            $profileBoostHistory = Http::withoutVerifying()->post(url('/') . '/api/Profileboosthistory', [
                'token' => $token,
                'astrologer_id' => astroauthcheck()['astrologerId']
            ])->json();
            return view('frontend.astrologers.pages.profile-boost', compact('profileBoostHistory'));
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

     public function profileBoostStore(Request $request)
    {
        try {
            if(!astroauthcheck()){
                return back()->with('error','Unauthenticated');
            }
            $session = new Session();
            $token = $session->get('astrotoken');
            $response = Http::withoutVerifying()->post(url('/') . '/api/boostProfile', [
                'token' => $token,
                'astrologer_id' => astroauthcheck()['astrologerId']
            ])->json();
            if($response['status'] == 200){
                return response()->json([
                    'success' => true,
                    'message' => 'Profile boosted successfully!',
                ]);
            }else{
                return response()->json([
                    'success' => false,
                    'message' => $response['error'],
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
