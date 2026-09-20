<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReferralController extends Controller
{
    public function editReferral()
    {
        $referral = ReferralSetting::first();
        return view('pages.referral-settings', compact('referral'));
    }

    public function updateReferral(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'amount' => 'required',
                'amount_usd' => 'required',
                'max_user_limit' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                $referral = ReferralSetting::updateOrCreate(
                    [
                        'id' => $req->id,
                    ],
                    [
                        'amount' => $req->amount,
                        'amount_usd' => $req->amount_usd,
                        'max_user_limit' => $req->max_user_limit,
                    ]
                );

                return response()->json([
                    'success' => 'Updated Successfully',
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
