<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductCategoryController extends Controller
{

    //Get a all data of product category
    public function getProductCategory(Request $req)
{
    try {
        $productCategory = ProductCategory::query();

        // Search functionality
        if ($s = $req->input('s')) {
            $productCategory->whereRaw("name LIKE '%" . $s . "%' ");
        }

        // Only active categories
        $productCategory = $productCategory->where('isActive', '=', true);

        // Total count
        $productCategoryCount = $productCategory->count();

        // Pagination
        if ($req->startIndex >= 0 && $req->fetchRecord) {
            $productCategory = $productCategory->skip($req->startIndex);
            $productCategory = $productCategory->take($req->fetchRecord);
        }

        $productCategoryList = $productCategory->get();

        // ✅ Convert categoryImage to full URL
        foreach ($productCategoryList as $category) {
            $category->categoryImage = $category->categoryImage ? asset($category->categoryImage) : null;
        }

        return response()->json([
            'recordList' => $productCategoryList,
            'status' => 200,
            'totalRecords' => $productCategoryCount,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'error' => false,
            'message' => $e->getMessage(),
            'status' => 500,
        ], 500);
    }
}


    public function gettopthreeproductcategory(Request $req)
    {
        try {
            $topCategory = DB::table('order_request')->join('product_categories', 'product_categories.id', 'order_request.productCategoryId')->whereNotNull('productCategoryId')->where('orderType', 'astromall')->groupBy('product_categories.id')->orderByRaw('count(product_categories.id) DESC')->limit(3)->select('product_categories.id','product_categories.name')->get();
            return response()->json([
                'recordList' => $topCategory,
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
