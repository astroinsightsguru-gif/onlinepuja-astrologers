<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\UserModel\AstromallProduct;
use App\Models\UserModel\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
if (!defined('DESTINATIONPATH')) define('DESTINATIONPATH', 'public/storage/images/');
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class AstroMallController extends Controller
{
    public $path;
    public $limit = 8;
    public $paginationStart;

    public function addAstroMall()
    {
        return view('pages.astroMall');
    }

    public function addAstroMallApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:product_categories',
                'categoryImage' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                if (request('categoryImage')) {
                    $image = base64_encode(file_get_contents($req->file('categoryImage')));
                } else {
                    $image = null;
                }
                $productCategory = ProductCategory::create([
                    'name' => $req->name,
                    'displayOrder' => null,
                    'categoryImage' => '',
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);

                // Handle categoryImage upload
                $path = null;
                $time = Carbon::now()->timestamp;

                if ($req->hasFile('categoryImage')) {
                    $imageContent = file_get_contents($req->file('categoryImage')->getRealPath());
                    $extension = $req->file('categoryImage')->getClientOriginalExtension() ?? 'png';
                    $imageName = 'catimg_' . $time . '.' . $extension;

                    try {
                        // Upload to active storage (local / external)
                        $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'categoryImage');
                    } catch (Exception $ex) {
                        return response()->json(['error' => $ex->getMessage()]);
                    }
                }

                $productCategory->categoryImage = $path;
                $productCategory->update();
                return redirect()->route('productCategories');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get AstroMall
    public function getastroMall(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $productCategory = ProductCategory::query();
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $productCategory->whereRaw(sql: "name LIKE '%" . $request->searchString . "%' ");
                }
                $productCategoryCount = $productCategory->count();
                $productCategory = $productCategory->skip($paginationStart);
                $productCategory = $productCategory->take($this->limit);
                $totalPages = ceil($productCategoryCount / $this->limit);
                $totalRecords = $productCategoryCount;
                $productCategory->orderBy('id', 'DESC');
                $astroMall = $productCategory->get();
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.astroMall', compact('astroMall', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get AstroMall Category

    public function getastroMallCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $productCategory = ProductCategory::query()->where('isActive', 1)->get();
                return view('pages.add-product')->with(['result' => $productCategory]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getCategoryById(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $productDetail = DB::table('astromall_products')
                    ->join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')
                    ->where('astromall_products.id', '=', $req->id)
                    ->select('astromall_products.*', 'product_categories.name as productCategory')
                    ->get();
                $questionAnswer = DB::Table('product_details')
                    ->where('astromallProductId', '=', $req->id)
                    ->where('isActive', '=', 1)
                    ->select('question', 'answer', 'id')
                    ->get();
                $productDetail[0]->questionAnswer = $questionAnswer;

                $productReview = DB::table('user_reviews')
                    ->join('users', 'users.id', '=', 'user_reviews.userId')
                    ->where('astromallProductId', '=', $req->id)
                    ->select('user_reviews.*', 'users.name as userName', 'users.profile')
                    ->get();
                $productDetail[0]->productReview = $productReview;
                $astroMallDetail = $productDetail;
                return view('pages.product-detail', compact('astroMallDetail'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Delete AstroMall

    // Update AstroMall

    public function editAstroMall()
    {
        return view('pages.astroMall');
    }

    public function editAstroMallApi(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            // Fetch existing category record
            $astromallProduct = DB::table('product_categories')
                ->where('id', '=', $request->filed_id)
                ->first();

            $productCategory = ProductCategory::find($request->filed_id);

            if (!$productCategory) {
                return response()->json(['error' => 'Product category not found.'], 404);
            }

            /* ------------------------------------------------
                   CATEGORY IMAGE UPLOAD LOGIC (Your Required)
            -------------------------------------------------- */

            $path = $productCategory->categoryImage;  // default old image path
            $time = Carbon::now()->timestamp;

            if ($request->hasFile('categoryImage')) {
                $imageContent = file_get_contents($request->file('categoryImage')->getRealPath());
                $extension = $request->file('categoryImage')->getClientOriginalExtension() ?? 'png';
                $imageName = 'catimg_' . $time . '.' . $extension;

                try {
                    // Upload to active storage (local/external)
                    $path = StorageHelper::uploadToActiveStorage(
                        $imageContent,
                        $imageName,
                        'categoryImage'
                    );
                } catch (\Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            /* ------------------------------------------------
                           UPDATE CATEGORY DATA
            -------------------------------------------------- */

            $productCategory->name = $request->name;
            $productCategory->displayOrder = null;
            $productCategory->categoryImage = $path;
            $productCategory->update();

            return redirect()->route('productCategories');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get Product

    public function getProduct(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $astromallProduct = AstromallProduct::query();
                $astromallProduct = $astromallProduct->join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')->select('astromall_products.*', 'product_categories.name as productCategory');
                $astromallProduct = $astromallProduct->orderBy('id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $astromallProduct->whereRaw(sql: "astromall_products.name LIKE '%" . $request->searchString . "%' ");
                }
                $astromallProductCount = $astromallProduct->count();
                $astromallProduct->skip($paginationStart);
                $astromallProduct->take($this->limit);
                $astromallProduct = $astromallProduct->get();
                // Fetch product details for each product
                foreach ($astromallProduct as $product) {
                    $product->details = DB::table('product_details')
                        ->where('astromallProductId', $product->id)
                        ->get();
                }
                // dd($astromallProduct);
                $totalPages = ceil($astromallProductCount / $this->limit);
                $totalRecords = $astromallProductCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.product', compact('astromallProduct', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Add Product

    public function addProduct()
    {
        return view('pages.add-product');
    }

    public function addProductApi(Request $req)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            /* -----------------------------------------
                  IMAGE HANDLING VARIABLES
            ----------------------------------------- */
            $path = null;
            $time = Carbon::now()->timestamp;

            /* -----------------------------------------
                   PRODUCT IMAGE UPLOAD LOGIC
            ----------------------------------------- */

            // 1️⃣ If normal file uploaded
            if ($req->hasFile('productImage')) {
                $file = $req->file('productImage');
                $imageContent = file_get_contents($file->getRealPath());
                $extension = $file->getClientOriginalExtension() ?? 'png';
                $imageName = 'product_' . $time . '.' . $extension;

                try {
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'productImage');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }
            // 2️⃣ If Base64 string uploaded
            elseif ($req->productImage) {
                $imageContent = base64_decode($req->productImage);
                $imageName = 'product_' . $time . '.png';

                try {
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'productImage');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            /* -----------------------------------------
                   AUTO SLUG CREATION
            ----------------------------------------- */
            $slug = Str::slug($req->name, '-');
            $originalSlug = $slug;
            $counter = 1;

            while (DB::table('astromall_products')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            /* -----------------------------------------
                  CREATE PRODUCT RECORD
            ----------------------------------------- */

            $astromallProduct = AstromallProduct::create([
                'name' => $req->name,
                'features' => $req->features,
                'slug' => $slug,
                'productImage' => $path,
                'productCategoryId' => $req->productCategoryId,
                'amount' => $req->amount,
                'usd_amount' => $req->usd_amount,
                'description' => $req->description,
                'createdBy' => Auth()->user()->id,
                'modifiedBy' => Auth()->user()->id,
            ]);

            return redirect()->route('products');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function astroMallStatus(Request $request)
    {
        return view('pages.astroMall');
    }

    public function astroMallStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $productCategory = ProductCategory::find($request->status_id);
                if ($productCategory) {
                    $productCategory->isActive = !$productCategory->isActive;
                    $productCategory->update();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function productStatus(Request $request)
    {
        return view('pages.product');
    }

    public function productStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astromallProduct = AstromallProduct::find($request->status_id);
                if ($astromallProduct) {
                    $astromallProduct->isActive = !$astromallProduct->isActive;
                    $astromallProduct->update();
                }
                return redirect()->route('products');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function editProduct(Request $req)
    {
        $product = AstromallProduct::find($req->id);
        $product_faqs = DB::table('product_details')->where('astromallProductId', $product->id)->get();
        $productCategory = ProductCategory::query()->where('isActive', '=', true)->get();
        return view('pages.edit-product')->with(['product' => $product, 'result' => $productCategory, 'product_faqs' => $product_faqs]);
    }

    public function editProductApi(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $product = AstromallProduct::find($request->field_id);
            if (!$product) {
                return back()->with('error', 'Product not found');
            }

            /* -----------------------------------------
                IMAGE HANDLING (Base64 + File Upload)
            ------------------------------------------ */

            $path = $product->productImage;  // default old image
            $time = Carbon::now()->timestamp;

            // 1. If new file uploaded (from form)
            if ($request->hasFile('productImage')) {
                $file = $request->file('productImage');
                $imageContent = file_get_contents($file->getRealPath());
                $extension = $file->getClientOriginalExtension() ?? 'png';
                $imageName = 'product_' . $product->id . '_' . $time . '.' . $extension;

                // Upload new image
                $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'productImage');
            }
            // 2. If Base64 image is sent
            elseif ($request->productImage && !Str::contains($request->productImage, 'storage')) {
                $imageContent = base64_decode($request->productImage);
                $imageName = 'product_' . $product->id . '_' . $time . '.png';

                // Upload new image
                $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'productImage');
            }

            // 3. Else keep old image (no change)

            /* -----------------------------
                  AUTO SLUG HANDLING
            ------------------------------ */

            $slug = Str::slug($request->name, '-');
            $originalSlug = $slug;
            $counter = 1;

            while (
                DB::table('astromall_products')
                    ->where('slug', $slug)
                    ->where('id', '!=', $product->id)  // ignore current
                    ->exists()
            ) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            /* -----------------------------
                UPDATE PRODUCT DATA
            ------------------------------ */

            $product->name = $request->name;
            $product->slug = $slug;
            $product->features = $request->features;
            $product->productImage = $path;
            $product->productCategoryId = $request->productCategoryId;
            $product->amount = $request->amount;
            $product->usd_amount = $request->usd_amount;
            $product->description = $request->description;
            $product->update();

            /* -----------------------------
                  UPDATE FAQ SECTION
            ------------------------------ */

            $existingDbFaqs = DB::table('product_details')
                ->where('astromallProductId', $product->id)
                ->pluck('id')
                ->toArray();

            $faqsToKeep = [];

            foreach ($request->faqs ?? [] as $faqData) {
                if (!empty($faqData['id'])) {
                    DB::table('product_details')
                        ->where('id', $faqData['id'])
                        ->update([
                            'question' => $faqData['question'],
                            'answer' => $faqData['answer']
                        ]);

                    $faqsToKeep[] = $faqData['id'];
                }
            }

            $faqsToDelete = array_diff($existingDbFaqs, $faqsToKeep);

            if (!empty($faqsToDelete)) {
                DB::table('product_details')
                    ->where('astromallProductId', $product->id)
                    ->whereIn('id', $faqsToDelete)
                    ->delete();
            }

            return redirect()->route('products');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addProductDetailApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $productDetail = array(
                    'astromallProductId' => $req->astromallProductId,
                    'question' => $req->question,
                    'answer' => $req->answer,
                );
                DB::Table('product_details')->insert($productDetail);
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
