<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\SystemFlag;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Session\Session;

class ProductController extends Controller
{
    public function getproducts(Request $request)
    {
        Artisan::call('cache:clear');
        $getproductCategory = Http::withoutVerifying()->post(url('/') . '/api/getproductCategory')->json();

        // Determine the current page from the request
        $productCategoryId = (int) $request->productCategoryId;
        $searchTerm = $request->input('s');

        $getsystemflag = SystemFlag::all();
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        $productlist = Product::query();
        if ($request->productCategoryId) {
            $productlist->where('productCategoryId', '=', $request->productCategoryId);
        }
        $productlist = $productlist
            ->where('isActive', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('frontend.pages.products', [
            'getproductCategory' => $getproductCategory,
            'productCategoryId' => $productCategoryId,
            'searchTerm' => $searchTerm,
            'currency' => $currency,
            'productlist' => $productlist,
        ]);
    }

    // -----------------------------------------------------------------------------------------------------------------------------------
    public function getproductDetails(Request $request)
    {
        Artisan::call('cache:clear');
        $getAstromallProduct = Http::withoutVerifying()->post(url('/') . '/api/getAstromallProduct')->json();

        // if(isset($request->ref))
        //     $cookie = cookie('productref_'.$request->id, $request->ref, 1440);
        $productdet = Product::where('slug', $request->slug)->first();
        if (isset($request->ref)) {
            setcookie('productref_' . $productdet->id, $request->ref, time() + (1440 * 60), '/');
        }

        // $getproductdetails = Http::withoutVerifying()->post(url('/') . '/api/getAstromallProductById', [
        //     'id' => $request->id,])->json();
        $getproductdetails = Product::join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')
            ->where('astromall_products.slug', '=', $request->slug)
            ->select('astromall_products.*', 'product_categories.name as productCategory')
            ->first();
        // dd($getproductdetails);
        $productfaq = DB::table('product_details')->where('astromallProductId', $getproductdetails->id)->get();
        $getsystemflag = SystemFlag::all();
        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        $productlist = Product::query()->where('isActive', 1)->where('id', '!=', $request->id)->orderBy('created_at', 'desc')->take(4)->get();

        return view('frontend.pages.product-details', [
            'getproductdetails' => $getproductdetails,
            'getAstromallProduct' => $getAstromallProduct,
            'currency' => $currency,
            'productlist' => $productlist,
            'productfaq' => $productfaq,
        ]);
    }

    public function checkout(Request $request)
    {
        Artisan::call('cache:clear');
        if (!authcheck())
            return redirect()->route('front.home');

        $userId = authcheck()['id'];

        $getAstromallProduct = Http::withoutVerifying()->post(url('/') . '/api/getAstromallProduct')->json();
        $getOrderAddress = Http::withoutVerifying()->post(url('/') . '/api/getOrderAddress', [
            'userId' => $userId,
        ])->json();

        $getproductdetails = Product::join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')
            ->where('astromall_products.id', '=', $request->id)
            ->select('astromall_products.*', 'product_categories.name as productCategory')
            ->first();

        $getsystemflag = SystemFlag::all();
        $gstvalue = $getsystemflag->where('name', 'Gst')->first();

        $currency = $getsystemflag->where('name', 'currencySymbol')->first();

        return view('frontend.pages.checkout', [
            'getproductdetails' => $getproductdetails,
            'getAstromallProduct' => $getAstromallProduct,
            'getOrderAddress' => $getOrderAddress,
            'gstvalue' => $gstvalue,
            'currency' => $currency,
        ]);
    }

    public function myOrders()
    {
        Artisan::call('cache:clear');

        if (!authcheck())
            return redirect()->route('front.home');

        $session = new Session();
        $token = $session->get('token');

        $getUserById = Http::withoutVerifying()->post(url('/') . '/api/getUserById', [
            'userId' => authcheck()['id'],
            'token' => $token,
        ])->json();

        $currency = SystemFlag::where('name', 'currencySymbol')->first();

        return view('frontend.pages.my-orders', [
            'getUserById' => $getUserById,
            'currency' => $currency
        ]);
    }
}
