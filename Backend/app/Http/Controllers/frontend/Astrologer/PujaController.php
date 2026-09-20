<?php

namespace App\Http\Controllers\frontend\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\SystemFlag;
use Illuminate\Http\Request;
use App\Models\Puja;
use App\Models\PujaCategory;
use App\Models\PujaSubCategory;
use App\Models\Pujapackage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class PujaController extends Controller
{
    public function createPuja()
    {
        if(!astroauthcheck())
            return redirect()->route('front.astrologerlogin');

        $currency = DB::table('systemflag')
        ->where('name', 'CurrencySymbol')
        ->select('value')
        ->first();

        return view('frontend.astrologers.pages.create-puja',compact('currency'));
    }

    public function PujaList()
    {
        if(!astroauthcheck())
        return redirect()->route('front.astrologerlogin');
       
        $currency = SystemFlag::where('name', 'CurrencySymbol')
        ->select('value')
        ->first();

        $pujas = Puja::where('astrologerId', astroauthcheck()['astrologerId'])
        ->where('created_by','astrologer')->where('puja_start_datetime', '>', Carbon::now())->orderBy('id','DESC')->get();

        return view('frontend.astrologers.pages.my-puja-list',compact('currency','pujas'));
    }

    public function editPuja($id)
    {

        if(!astroauthcheck())
        return redirect()->route('front.astrologerlogin');
       
        $puja = Puja::where('id', $id)
        ->where('astrologerId', astroauthcheck()['astrologerId'])
        ->where('created_by','astrologer')
        ->firstOrFail();

        return view('frontend.astrologers.pages.create-puja',compact('puja'));
    }



    public function storePuja(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'puja_title' => 'required|string|max:200',
            'long_description' => 'required|string|max:250',
            'puja_start_datetime' => 'required|date',
            'puja_duration' => 'required',
            'puja_place' => 'required|string|max:255',
            'puja_price' => 'required|numeric',
            'puja_images.*' => 'image|mimes:jpeg,png,jpg,gif,avif,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
                'status' => 422
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Generate slug
            $slug = Str::slug($request->puja_title, '-');
            $originalSlug = $slug;
            $counter = 1;
            
            // Check for unique slug (excluding current puja if updating)
            $query = Puja::where('slug', $slug);
            if ($request->has('puja_id')) {
                $query->where('id', '!=', $request->puja_id);
            }
            
            while ($query->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
                $query = Puja::where('slug', $slug);
                if ($request->has('puja_id')) {
                    $query->where('id', '!=', $request->puja_id);
                }
            }

            $pujaStartDatetime = Carbon::parse($request->puja_start_datetime);
            $pujaEndDatetime = $pujaStartDatetime->copy()->addMinutes($request->puja_duration);

            // Prepare base puja data
            $pujaData = [
                'astrologerId' => astroauthcheck()['astrologerId'],
                'puja_title' => $request->puja_title,
                'slug' => $slug,
                'puja_price' => $request->puja_price,
                'long_description' => $request->long_description,
                'puja_start_datetime' => $request->puja_start_datetime,
                'puja_end_datetime' => $pujaEndDatetime,
                'puja_duration' => $request->puja_duration,
                'puja_place' => $request->puja_place,
                'created_by' => 'astrologer',
            ];

            // Handle image uploads (for both create and update)
            $imagePaths = [];
            
            // For updates, keep existing images unless deleted
            if ($request->has('puja_id')) {
                $puja = Puja::where('id', $request->puja_id)
                        ->where('astrologerId', astroauthcheck()['astrologerId'])
                        ->firstOrFail();
                        
                $imagePaths = $puja->puja_images ?? [];
                
                // Remove images marked for deletion
                if ($request->has('images_to_delete')) {
                    foreach ($request->images_to_delete as $imageToDelete) {
                        // Remove from storage
                        if (file_exists(public_path($imageToDelete))) {
                            unlink(public_path($imageToDelete));
                        }
                        // Remove from array
                        $imagePaths = array_filter($imagePaths, function($path) use ($imageToDelete) {
                            return $path !== $imageToDelete;
                        });
                    }
                    $imagePaths = array_values($imagePaths); // Reindex array
                }
            }
            
            // Add new images using your preferred method
            if ($request->hasFile('puja_images')) {
                foreach ($request->file('puja_images') as $file) {
                    $name = time().rand().'.'.$file->getClientOriginalExtension();
                    $path = $file->move('public/storage/images/puja_images', $name);
                    $imagePaths[] = 'public/storage/images/puja_images/'.$name;
                }
            }
            
            // Add images to puja data
            $pujaData['puja_images'] = $imagePaths;
            
            // Create or update the puja
            if ($request->has('puja_id')) {
                $puja->update($pujaData);
            } else {
                $puja = Puja::create($pujaData);
            }

            DB::commit();
            
            return response()->json([
                'message' => $request->has('puja_id') ? 'Puja Updated Successfully' : 'Puja Added Successfully',
                'data' => $puja,
                'status' => 200
            ], 200);
                
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Server Error: ' . $e->getMessage(),
                'status' => 500
            ], 500);
        }
    }

    public function deletePuja($id)
    {
       
        $puja = Puja::where('id', $id)
        ->where('astrologerId', astroauthcheck()['astrologerId'])
        ->where('created_by','astrologer')
        ->firstOrFail();
        
        if($puja){
            $puja->delete();
        }

        return redirect()->back();
    }
}
