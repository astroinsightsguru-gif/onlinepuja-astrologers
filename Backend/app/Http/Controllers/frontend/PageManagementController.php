<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Contactus;
use App\Models\Page;
class PageManagementController extends Controller
{

    public function privacyPolicy(Request $request)
	{

        try {

            $privacy=DB::table('pages')->where('type','privacy')->first();
            return view('frontend.pages.privacy-policy',compact('privacy'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}

    public function termscondition(Request $request)
	{

        try {

            $terms=DB::table('pages')->where('type','terms')->first();
            return view('frontend.pages.terms-condition',compact('terms'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}

    #--------------------------------------------------------------------------------------------------------------------------------------

    public function astrologerPrivacyPolicy(Request $request)
	{

        try {

            $privacy=DB::table('pages')->where('type','astrologerPrivacy')->first();
            return view('frontend.pages.astrologer-privacy-policy',compact('privacy'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}



    #---------------------------------------------------------------------------------------------------------------------------------

    public function astrologerTermsCondition(Request $request)
	{

        try {

            $terms=DB::table('pages')->where('type','astrologerTerms')->first();
            return view('frontend.pages.astrologer-terms-condition',compact('terms'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}


    #------------------------------------------------------------------------------------------------------------------------------------------

     public function privacyPolicyApp(Request $request)
	{

        try {

            $privacy=DB::table('pages')->where('type','privacy')->first();
            return view('frontend.pages.privacy-policy-app',compact('privacy'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}

	 #------------------------------------------------------------------------------------------------------------------------------------------
     public function refundPolicyApp(Request $request)
     {

         try {

             $refundpolicy=DB::table('pages')->where('type','refundpolicy')->first();
             return view('frontend.pages.refund-policy-app',compact('refundpolicy'));
         } catch (\Exception$e) {
             return response()->json([
                 'error' => false,
                 'message' => $e->getMessage(),
                 'status' => 500,
             ], 500);
         }
     }

      #------------------------------------------------------------------------------------------------------------------------------------------

	 public function termsconditionforapp(Request $request)
	{

        try {

            $terms=DB::table('pages')->where('type','terms')->first();
            return view('frontend.pages.terms-condition-app',compact('terms'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}

	  #------------------------------------------------------------------------------------------------------------------------------------------

    public function aboutus(Request $request)
	{

        try {

            $aboutus=DB::table('pages')->where('type','aboutus')->first();
            return view('frontend.pages.aboutus',compact('aboutus'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}


    #-------------------------------------------------------------------------------------------------------------------------

    public function refundPolicy(Request $request)
	{

        try {

            $refundpolicy=DB::table('pages')->where('type','refundpolicy')->first();
            return view('frontend.pages.refund-policy',compact('refundpolicy'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}


    #-------------------------------------------------------------------------------------------------------------------------
    public function contactUS(Request $request)
    {
        try {
            $sitenumber = DB::table('systemflag')
                ->where('name', 'sitenumber')
                ->value('value');

            $siteemail = DB::table('systemflag')
                ->where('name', 'siteemail')
                ->value('value');

            $siteaddress = DB::table('systemflag')
                ->where('name', 'siteaddress')
                ->value('value');

                $appName = DB::table('systemflag')
            ->where('name', 'AppName')
            ->select('value')
            ->first();


            return view('frontend.pages.contact', compact('sitenumber', 'siteemail', 'siteaddress','appName'));
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    #-------------------------------------------------------------------------------------------------------------------------
    public function SavecontactUS(Request $request)
    {
        $request->validate([
            'contact_name' => 'required',
            'contact_email' => 'required|email',
            'contact_message' => 'required',
        ]);

        ContactUs::create([
            'contact_name' => $request->contact_name,
            'contact_email' => $request->contact_email,
            'contact_message' => $request->contact_message,
        ]);

        return response()->json(['success' => 'Form submitted successfully!'], 200);
    }



    #-------------------------------------------------------------------------------------------------------------------------

    public function astrologerprivacyPolicyApp(Request $request)
	{

        try {

            $privacy=DB::table('pages')->where('type','astrologerPrivacy')->first();
            return view('frontend.astrologers.pages.privacy-policy-app',compact('privacy'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}

    public function astrologertermsconditionforapp(Request $request)
	{

        try {

            $terms=DB::table('pages')->where('type','astrologerTerms')->first();
            return view('frontend.astrologers.pages.terms-condition-app',compact('terms'));
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
	}

    public function show($slug)
    {
        $page = Page::where('type', $slug)->where('isActive', 1)->first();

        if (!$page) {
            abort(404, 'Page not found.');
        }

        return view('frontend.pages.allpagesdata', compact('page'));
    }

}
