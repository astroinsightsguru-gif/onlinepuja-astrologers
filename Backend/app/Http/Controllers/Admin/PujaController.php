<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminModel\SystemFlag;
use App\Models\AstrologerModel\Astrologer;
use App\Models\UserModel\UserDeviceDetail;
use App\Models\UserModel\UserNotification;
use App\Models\Puja;
use App\Models\PujaCategory;
use App\Models\PujaOrder;
use App\Models\Pujapackage;
use App\Models\PujaRecommend;
use App\Models\PujaSubCategory;
use App\services\OneSignalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
if (!defined('LOGINPATH')) define('LOGINPATH', '/admin/login');

class PujaController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function AddPuja()
    {
        $pujaCategory = PujaCategory::all();
        $pujaSubCategory = PujaSubCategory::all();
        $packages = Pujapackage::all();

        $currency = SystemFlag::where('name', 'CurrencySymbol')
            ->select('value')
            ->first();

        return view('pages.add-puja', compact('pujaCategory', 'packages', 'currency', 'pujaSubCategory'));
    }

    // ---------------------------------------------------------------------------------------------------------
    public function getPujaList(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                $query = Puja::query()->where('created_by', 'admin');

                $userCount = $query->count();
                $totalPages = ceil($userCount / $this->limit);
                $totalRecords = $userCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                $pujalist = $query
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();
                return view('pages.puja-list', compact('pujalist', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ---------------------------------------------------------------------------------------------------------------------------

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'category_id' => 'required|integer',
                'place' => 'required|string|max:255',
                'benefit_title.*' => 'required|string',
                'puja_images' => 'required',
                'package_id' => 'required',
                'puja_start_datetime' => 'nullable',
                'puja_duration' => 'nullable',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Please fix the errors below.');
            }
            $benefits = [];
            if ($request->has('benefit_title') && $request->has('benefit_description')) {
                foreach ($request->benefit_title as $index => $title) {
                    $description = $request->benefit_description[$index] ?? '';
                    if ($title) {
                        $benefits[] = [
                            'title' => $title,
                            'description' => $description,
                        ];
                    }
                }
            }
            // Handle file upload with StorageHelper
            $imagePaths = [];
            if ($request->hasFile('puja_images')) {
                foreach ($request->file('puja_images') as $file) {
                    $imageContent = file_get_contents($file->getRealPath());
                    $extension = $file->getClientOriginalExtension() ?? 'png';
                    $imageName = 'puja_' . time() . rand() . '.' . $extension;

                    try {
                        $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'puja_images');
                        $imagePaths[] = $path;
                    } catch (Exception $ex) {
                        return redirect()->back()->with('error', $ex->getMessage());
                    }
                }
            }
            $slug = Str::slug($request->title, '-');
            $originalSlug = $slug;
            $counter = 1;
            while (Puja::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $pujaStartDatetime = $request->puja_start_datetime ? Carbon::parse($request->puja_start_datetime) : null;
            $pujaEndDatetime = $pujaStartDatetime && $request->puja_duration ? $pujaStartDatetime->copy()->addMinutes($request->puja_duration) : null;
            if (Auth::guard('web')->check()) {
                Puja::create([
                    'category_id' => $request->category_id,
                    'sub_category_id' => $request->sub_category_id,
                    'puja_title' => $request->title,
                    'slug' => $slug,
                    'puja_subtitle' => $request->subtitle,
                    'puja_place' => $request->place,
                    'long_description' => $request->description,
                    'puja_benefits' => $benefits,
                    'puja_images' => $imagePaths,
                    'package_id' => $request->package_id,
                    'puja_start_datetime' => $pujaStartDatetime,
                    'puja_duration' => $request->puja_duration,
                    'puja_end_datetime' => $pujaEndDatetime,
                    'created_by' => 'admin',
                    'isAdminApproved' => 'Approved',
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('puja-list')->with('success', 'Puja package added successfully!');
    }

    // ------------------------------------------------------------

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'nullable|string|max:255',
                'subtitle' => 'nullable|string|max:255',
                'category_id' => 'nullable|integer',
                'package_id' => 'nullable',
                'place' => 'nullable|string|max:255',
                'benefit_title.*' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            $puja = Puja::findOrFail($id);

            // Prepare puja_benefits as JSON
            $benefits = [];
            if ($request->has('benefit_title') && $request->has('benefit_description')) {
                foreach ($request->benefit_title as $index => $title) {
                    $description = $request->benefit_description[$index] ?? '';
                    if ($title) {
                        $benefits[] = [
                            'title' => $title,
                            'description' => $description,
                        ];
                    }
                }
            }

            $imagePaths = $request->input('existing_images', []);

            if ($request->hasFile('old_images')) {
                foreach ($request->file('old_images') as $file) {
                    $imageContent = file_get_contents($file->getRealPath());
                    $extension = $file->getClientOriginalExtension() ?? 'png';
                    $imageName = 'puja_' . time() . rand() . '.' . $extension;

                    try {
                        $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'puja_images');
                        $imagePaths[] = $path;
                    } catch (Exception $ex) {
                        return response()->json(['error' => $ex->getMessage()]);
                    }
                }
            }

            if ($request->hasFile('puja_images')) {
                foreach ($request->file('puja_images') as $file) {
                    $imageContent = file_get_contents($file->getRealPath());
                    $extension = $file->getClientOriginalExtension() ?? 'png';
                    $imageName = 'puja_' . time() . rand() . '.' . $extension;

                    try {
                        $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'puja_images');
                        $imagePaths[] = $path;
                    } catch (Exception $ex) {
                        return response()->json(['error' => $ex->getMessage()]);
                    }
                }
            }

            if (Auth::guard('web')->check()) {
                $slug = $request->title ? Str::slug($request->title, '-') : $puja->slug;
                $originalSlug = $slug;
                $counter = 1;
                while (Puja::where('slug', $slug)->where('id', '!=', $id)->exists()) {
                    $slug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $pujaStartDatetime = $request->puja_start_datetime ? Carbon::parse($request->puja_start_datetime) : $puja->puja_start_datetime;
                $pujaEndDatetime = $pujaStartDatetime && $request->puja_duration ? $pujaStartDatetime->copy()->addMinutes($request->puja_duration) : $puja->puja_end_datetime;

                $puja->update([
                    'category_id' => $request->category_id ?? $puja->category_id,
                    'sub_category_id' => $request->sub_category_id ?? $puja->sub_category_id,
                    'puja_title' => $request->title ?? $puja->puja_title,
                    'slug' => $slug,
                    'puja_subtitle' => $request->subtitle ?? $puja->puja_subtitle,
                    'puja_place' => $request->place ?? $puja->puja_place,
                    'long_description' => $request->description ?? $puja->long_description,
                    'puja_benefits' => $benefits ?: $puja->puja_benefits,
                    'puja_images' => $imagePaths,
                    'package_id' => $request->package_id ?? $puja->package_id,
                    'puja_start_datetime' => $pujaStartDatetime,
                    'puja_duration' => $request->puja_duration ?? $puja->puja_duration,
                    'puja_end_datetime' => $pujaEndDatetime,
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('puja-list')->with('success', 'Puja package updated successfully!');
    }

    // ------------------------------------------------------------------------------------------------------------------------------

    public function editpuja($id)
    {
        $puja = Puja::findOrFail($id);
        $pujaCategory = PujaCategory::all();
        $pujaSubCategory = PujaSubCategory::all();
        $packages = Pujapackage::all();

        return view('pages.add-puja', compact('puja', 'pujaCategory', 'packages', 'pujaSubCategory'));
    }

    // -------------------------------------------------------------------------------------------------------------------------

    public function viewpuja($id)
    {
        $puja = Puja::findOrFail($id);
        $pujaCategory = PujaCategory::all();
        $pujaSubCategory = PujaSubCategory::all();
        $packages = Pujapackage::all();

        return view('pages.view-puja', compact('puja', 'pujaCategory', 'packages', 'pujaSubCategory'));
    }

    // --------------------------------------------------------------------------------------------------------------------------

    public function PujaStatus(Request $request)
    {
        try {
            $pujastatus = Puja::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $pujastatus->puja_status = !$pujastatus->puja_status;
                $pujastatus->update();
                return redirect()->route('puja-list');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function deletePuja(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $Puja = Puja::find($request->del_id);

                if ($Puja) {
                    $image = $Puja->puja_images;
                    if ($image) {
                        $length = count($image);
                        for ($i = 0; $i < $length; $i++) {
                            $imgPath = $image[$i];
                            if ($imgPath) {
                                if (strpos($imgPath, 'http://') === 0 || strpos($imgPath, 'https://') === 0) {
                                    $parsedUrl = parse_url($imgPath);
                                    $path = isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') : '';
                                    if ($path) {
                                        $localPath = public_path($path);
                                        if (file_exists($localPath)) {
                                            @unlink($localPath);
                                        }
                                        $localPath2 = base_path($path);
                                        if (file_exists($localPath2)) {
                                            @unlink($localPath2);
                                        }
                                        if (strpos($path, 'public/') === 0) {
                                            $strippedPath = substr($path, 7);
                                            $localPath3 = public_path($strippedPath);
                                            if (file_exists($localPath3)) {
                                                @unlink($localPath3);
                                            }
                                        }
                                    }
                                } else {
                                    if (file_exists($imgPath)) {
                                        @unlink($imgPath);
                                    } else {
                                        $basePath = base_path($imgPath);
                                        if (file_exists($basePath)) {
                                            @unlink($basePath);
                                        }
                                        $pubPath = public_path($imgPath);
                                        if (file_exists($pubPath)) {
                                            @unlink($pubPath);
                                        }
                                        if (strpos($imgPath, 'public/') === 0) {
                                            $strippedPath = substr($imgPath, 7);
                                            $pubPath2 = public_path($strippedPath);
                                            if (file_exists($pubPath2)) {
                                                @unlink($pubPath2);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $Puja->delete();
                } else {
                    return redirect(LOGINPATH);
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------------------------------------------------------------------------

    public function getPujaOrderList(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                $query = PujaOrder::with('user');
                $userCount = $query->count();
                $totalPages = ceil($userCount / $this->limit);
                $totalRecords = $userCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                // Clone query for counting records
                $countQuery = clone $query;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $query->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $query->where('created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $query->where('created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('created_at', '<=', $to_date . ' 23:59:59');
                }

                $pujaOrderlist = $query
                    ->orderBy('created_at', 'desc')  // Replace 'created_at' with the desired column
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();
                // return $pujaOrderlist;

                $astrologers = Astrologer::where('isVerified', 1)
                    ->where('isActive', 1)
                    ->where('isDelete', 0)
                    ->get();

                $currency = SystemFlag::where('name', 'currencySymbol')
                    ->select('value')
                    ->first();

                return view('pages.puja-order', compact('pujaOrderlist', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'currency', 'astrologers', 'from_date', 'to_date'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ------------------------------------------------------------------------------------------------------------------------------------------------------

    public function PujaOrderupdate(Request $request)
    {
        $pujaOrder = PujaOrder::find($request->puja_order_id);

        if ($pujaOrder) {
            $pujaOrder->astrologer_id = $request->astrologer_id;
            $pujaOrder->save();

            $userDeviceDetail = UserDeviceDetail::select('user_device_details.*', 'astrologers.userId as astrologerUserId', 'astrologers.name')
                ->join('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
                ->where('astrologers.id', '=', $request->astrologer_id)
                ->get();

            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                $oneSignalService = new OneSignalService();
                $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();
                $notificationData = [
                    'title' => 'Hey ' . $userDeviceDetail[0]->name . ', puja has been assigned to you',
                    'body' => ['description' => 'Hey ' . $userDeviceDetail[0]->name . ', puja has been assigned to you'],
                ];

                $response = $oneSignalService->sendNotification($userPlayerIds, $notificationData);

                $notification = array(
                    'userId' => $userDeviceDetail[0]->astrologerUserId,
                    'title' => 'Hey ' . $userDeviceDetail[0]->name . ', puja has been assigned to you',
                    'description' => 'Hey ' . $userDeviceDetail[0]->name . ', puja has been assigned to you',
                    'notificationId' => null,
                    'createdBy' => $userDeviceDetail[0]->astrologerUserId,
                    'modifiedBy' => $userDeviceDetail[0]->astrologerUserId,
                    'notification_type' => 0,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                );
                UserNotification::insert($notification);
            }

            return response()->json(['message' => 'Puja order updated successfully!', 'success' => true]);
        }

        return response()->json(['message' => 'Puja order not found.'], 404);
    }

    public function pujaRecommend(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $pujarecommend = PujaRecommend::join('users', 'users.id', 'puja_recommends.userId')
                    ->join('astrologers', 'astrologers.id', 'puja_recommends.astrologerId')
                    ->join('pujas', 'pujas.id', 'puja_recommends.puja_id')
                    ->select(
                        'users.name as userName',
                        'puja_recommends.*',
                        'astrologers.name as astrologerName',
                        'pujas.puja_title',
                        'pujas.puja_images',
                    )
                    ->orderBy('puja_recommends.id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                // dd( $pujarecommend );

                $totalRecords = $pujarecommend->count();

                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.puja-recommends', compact('pujarecommend', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Get Astrologer Puja

    public function getAstrologerPuja(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;
                $searchString = $request->searchString ?? null;

                $query = Puja::query()->where('created_by', 'astrologer')->orderBy('id', 'DESC');

                if ($searchString) {
                    $query->where(function ($query) use ($searchString) {
                        $query
                            ->orWhereHas('astrologerRelation', function ($q) use ($searchString) {
                                $q->where('name', 'LIKE', '%' . $searchString . '%');
                            });
                    });
                }
                $userCount = $query->count();
                $totalPages = ceil($userCount / $this->limit);
                $totalRecords = $userCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                $pujalist = $query
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();
                //    dd($pujalist);
                return view('pages.astrologer-puja-list', compact('pujalist', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function adminPujaApproveStatus(Request $request)
    {
        $eid = $request->filed_id;
        $puja = Puja::find($eid);
        if (!$puja) {
            return redirect()->back()->with('error', 'Puja not found!');
        }

        // Get the previous status before changing it
        $previousStatus = $puja->isAdminApproved;

        // Toggle the status
        if ($puja->isAdminApproved === 'Pending' || $puja->isAdminApproved === 'Rejected') {
            $puja->isAdminApproved = 'Approved';
        } elseif ($puja->isAdminApproved === 'Approved') {
            $puja->isAdminApproved = 'Rejected';
        }

        $puja->save();

        // Send notification based on the new status
        $userDeviceDetail = UserDeviceDetail::select('user_device_details.*', 'astrologers.userId as astrologerUserId', 'astrologers.name')
            ->join('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
            ->where('astrologers.id', '=', $puja->astrologerId)
            ->get();
        if ($userDeviceDetail && count($userDeviceDetail) > 0) {
            $oneSignalService = new OneSignalService();
            $userPlayerIds = $userDeviceDetail->pluck('subscription_id')->merge($userDeviceDetail->pluck('subscription_id_web'))->values()->toArray();

            // Customize notification based on approval status
            if ($puja->isAdminApproved === 'Approved') {
                $notificationTitle = 'Puja Approved';
                $notificationBody = 'Hey ' . $userDeviceDetail[0]->name . ', your puja has been approved by admin.';
            } else {
                $notificationTitle = 'Puja Rejected';
                $notificationBody = 'Hey ' . $userDeviceDetail[0]->name . ', your puja has been rejected by admin.';
            }

            // Send push notification
            $notification = [
                'title' => $notificationTitle,
                'body' => ['description' => $notificationBody],
            ];
            $oneSignalService->sendNotification($userPlayerIds, $notification);

            // Save notification to database
            $notificationData = [
                'userId' => $userDeviceDetail[0]->astrologerUserId,
                'title' => $notificationTitle,
                'description' => $notificationBody,
                'notificationId' => null,
                'createdBy' => $userDeviceDetail[0]->astrologerUserId,
                'modifiedBy' => $userDeviceDetail[0]->astrologerUserId,
                'notification_type' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            UserNotification::insert($notificationData);
        }

        return redirect()->back()->with('success', 'Puja status updated successfully!');
    }
}
