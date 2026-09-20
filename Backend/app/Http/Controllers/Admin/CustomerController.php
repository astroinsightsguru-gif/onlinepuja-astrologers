<?php
namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerGift;
use App\Models\UserModel\CallRequest;
use App\Models\UserModel\ChatRequest;
use App\Models\UserModel\Payment;
use App\Models\UserModel\User;
use App\Models\UserModel\UserOrder;
use App\Models\UserModel\UserReport;
use App\Models\UserModel\UserWallet;
use App\Models\Country;
use App\Models\Customer;
use App\Models\PujaOrder;
use App\Models\UserRole;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use PDF;
use Response;

class CustomerController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;
    public $customers = [];

    public function addUser()
    {
        $country = Country::all();
        return view('pages.add-customer', compact('country'));
    }

    public function addUserApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required',
                'contactNo' => 'required|unique:users,contactNo',
                'email' => 'required|unique:users,email',
                'gender' => 'required',
                'countryCode' => 'required',
                'country' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            // ✅ Create user record (without image first)
            $user = Customer::create([
                'name' => $req->name,
                'contactNo' => $req->contactNo,
                'email' => $req->email,
                'password' => Hash::make($req->password),
                'birthDate' => $req->birthDate,
                'birthTime' => $req->birthTime,
                'birthPlace' => $req->birthPlace,
                'addressLine1' => $req->addressLine1,
                'location' => $req->location,
                'pincode' => $req->pincode,
                'gender' => $req->gender,
                'countryCode' => $req->countryCode,
                'country' => $req->country,
            ]);

            // Generate referral token
            $referral_token = 'REF' . numberToCharacterString($user->id);
            $user->update(['referral_token' => $referral_token]);

            // Handle profile upload
            $path = null;
            $time = Carbon::now()->timestamp;

            if ($req->hasFile('profile')) {
                $imageContent = file_get_contents($req->file('profile')->getRealPath());
                $extension = $req->file('profile')->getClientOriginalExtension() ?? 'png';
                $imageName = 'user_' . $user->id . '_' . $time . '.' . $extension;

                try {
                    // Upload to active storage (local / external)
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'profile');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            // Update user profile image path
            $user->profile = $path;
            $user->save();

            // Assign default role (3 = Customer)
            UserRole::create([
                'userId' => $user->id,
                'roleId' => 3,
            ]);

            return response()->json([
                'success' => 'Customer Added Successfully',
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function getUser(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;

                $query = User::query()
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('users.isDelete', false)
                    ->where('user_roles.roleId', 3)
                    ->select('users.*', 'user_roles.roleId')
                    ->orderByDesc('users.id');

                $searchString = $request->searchString ?? null;
                if ($searchString) {
                    $query->where(function ($q) use ($searchString) {
                        $q
                            ->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }

                // Clone query for counting records
                $countQuery = clone $query;
                // Date filter
                $from_date = $request->from_date ?? null;
                $to_date = $request->to_date ?? null;

                if ($from_date && $to_date) {
                    $query->whereBetween('users.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('users.created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $query->where('users.created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('users.created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $query->where('users.created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('users.created_at', '<=', $to_date . ' 23:59:59');
                }

                $userCount = $query->count();
                $totalPages = ceil($userCount / $this->limit);
                $totalRecords = $userCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                $customers = $query
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                $userdatas = DB::table('users as u')
                    ->join('user_roles', 'user_roles.userId', '=', 'u.id')
                    ->where('u.isDelete', '=', false)
                    ->where('user_roles.roleId', '=', 3)
                    ->select('u.name as userName', 'u.id as userId', 'u.contactNo as usercontactNo', 'u.email')
                    ->orderBy('u.id', 'DESC')
                    ->get();

                return view('pages.customer-list', compact('customers', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'userdatas', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $user = Customer::find($request->del_id);
                if ($user) {
                    // $user->isDelete = true;
                    $user->delete();
                } else {
                    return redirect('/admin/login');
                }
                return redirect()->route('customers');
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editUser(Request $req)
    {
        $user = Customer::find($req->id);
        return view('pages.edit-customer')->with(['customer' => $user]);
    }

    public function editUserApi(Request $req)
    {
        try {
            // Check if user is logged in
            if (!Auth::guard('web')->check()) {
                return redirect('/admin/login');
            }

            $user = Customer::find($req->field_id);
            if (!$user) {
                return response()->json(['error' => 'User not found']);
            }

            // Validate request
            $validator = Validator::make($req->all(), [
                'contactNo' => 'required|unique:users,contactNo,' . $user->id,
                'email' => 'required|email|unique:users,email,' . $user->id,
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->getMessageBag()->toArray()]);
            }

            // Prepare image content
            $imageContent = null;
            if ($req->hasFile('profile')) {
                $imageContent = file_get_contents($req->file('profile')->getRealPath());
            }

            $time = Carbon::now()->timestamp;
            $path = $user->profile;
            if ($imageContent) {
                try {
                    $imageName = 'user_' . $user->id . '_' . $time . '.png';
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'profile');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            // Update user data
            $user->name = $req->name;
            $user->contactNo = $req->contactNo;
            $user->password = Hash::make($req->password);
            $user->birthDate = $req->birthDate;
            $user->birthTime = $req->birthTime;
            $user->birthPlace = $req->birthPlace;
            $user->addressLine1 = $req->addressLine1;
            $user->location = $req->location;
            $user->profile = $path;
            $user->pincode = $req->pincode;
            $user->gender = $req->gender;
            $user->email = $req->email;
            $user->countryCode = $req->countryCode;
            $user->update();

            return response()->json(['success' => 'Customer Updated']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function editUserDetail()
    {
        return view('pages.customer-detail');
    }

    public function getUserDetailApi(Request $req, $id)
    {
        try {
            if (Auth::guard('web')->check()) {
                $user = DB::table('users')
                    ->where('id', '=', $id)
                    ->get();
                if ($user) {
                    $follower = DB::table('astrologer_followers')
                        ->join('astrologers', 'astrologer_followers.astrologerId', '=', 'astrologers.id')
                        ->where('astrologer_followers.userId', '=', $id)
                        ->select('astrologers.*', 'astrologer_followers.created_at as followingDate')
                        ->orderBy('astrologer_followers.id', 'DESC')
                        ->get();
                    if ($follower && count($follower) > 0) {
                        foreach ($follower as $follow) {
                            $languages = DB::table('languages')
                                ->whereIn('id', explode(',', $follow->languageKnown))
                                ->select('languageName')
                                ->get();

                            $allSkill = DB::table('skills')
                                ->whereIn('id', explode(',', $follow->languageKnown))
                                ->get('name');
                            $follow->languageKnown = $languages;
                            $follow->allSkill = $allSkill;
                        }
                    }
                    $orderRequest = UserOrder::leftjoin('product_categories', 'product_categories.id', '=', 'order_request.productCategoryId')
                        ->leftjoin('astromall_products', 'astromall_products.id', '=', 'order_request.productId')
                        ->leftjoin('order_addresses', 'order_addresses.id', '=', 'order_request.orderAddressId')
                        ->where('order_request.userId', '=', $id)
                        ->where('order_request.orderType', '=', 'astromall');

                    $orderRequestCount = $orderRequest->count();
                    $orderRequest->select('order_request.*', 'product_categories.name as productCategory', 'astromall_products.productImage', 'astromall_products.amount as productAmount', 'astromall_products.description',
                        'order_addresses.name as orderAddressName', 'order_addresses.phoneNumber', 'order_addresses.flatNo', 'order_addresses.locality', 'order_addresses.landmark', 'order_addresses.city', 'order_addresses.state', 'order_addresses.country', 'order_addresses.pincode', 'astromall_products.name as productName');
                    $orderRequest->orderBy('order_request.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $orderRequest->skip($req->startIndex);
                        $orderRequest->take($req->fetchRecord);
                    }
                    $orderRequest = $orderRequest->get();

                    $pujaorder = PujaOrder::where('user_id', '=', $id);

                    $pujaorderCount = $pujaorder->count();
                    $pujaorder->select('puja_orders.*');
                    $pujaorder->orderBy('puja_orders.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $pujaorder->skip($req->startIndex);
                        $pujaorder->take($req->fetchRecord);
                    }
                    $pujaorder = $pujaorder->get();

                    $giftList = AstrologerGift::join('gifts', 'gifts.id', 'astrologer_gifts.giftId')
                        ->join('astrologers as astro', 'astro.id', '=', 'astrologer_gifts.astrologerId')
                        ->where('astrologer_gifts.userId', '=', $id);

                    $giftListCount = $giftList->count();
                    $giftList->select('gifts.name as giftName', 'astrologer_gifts.*', 'astro.id as astrologerId', 'astro.name as astrolgoerName', 'astro.contactNo');

                    $giftList->orderBy('astrologer_gifts.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $giftList->skip($req->startIndex);
                        $giftList->take($req->fetchRecord);
                    }
                    $giftList = $giftList->get();
                    // dd($giftList);
                    $chatHistory = ChatRequest::join('astrologers as astr', 'astr.id', '=', 'chatrequest.astrologerId')
                        ->where('chatrequest.userId', '=', $id);

                    $chatHistoryCount = $chatHistory->count();
                    $chatHistory->select('chatrequest.*', 'astr.id as astrologerId', 'astr.name as astrologerName', 'astr.contactNo', 'astr.profileImage', 'astr.charge');
                    $chatHistory->orderBy('chatrequest.id', 'DESC');
                    $chatHistory = $chatHistory->get();
                    $callHistory = CallRequest::join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                        ->where('callrequest.userId', '=', $id);
                    $callHistoryCount = $callHistory->count();
                    $callHistory->select('callrequest.*', 'astrologers.id as astrologerId', 'astrologers.name as astrologerName', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.charge');
                    $callHistory->orderBy('callrequest.id', 'DESC');

                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $callHistory->skip($req->startIndex);
                        $callHistory->take($req->fetchRecord);
                    }
                    $callHistory = $callHistory->get();

                    $reportHistory = UserReport::join('astrologers', 'astrologers.id', '=', 'user_reports.astrologerId')
                        ->join('report_types', 'report_types.id', '=', 'user_reports.reportType')
                        ->where('user_reports.userId', '=', $id);

                    $reportHistoryCount = $reportHistory->count();

                    $reportHistory->select('user_reports.*', 'astrologers.id as astrologerId', 'astrologers.name as astrologerName', 'astrologers.contactNo', 'report_types.title', 'astrologers.profileImage');

                    $reportHistory->orderBy('user_reports.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $reportHistory->skip($req->startIndex);
                        $reportHistory->take($req->fetchRecord);
                    }
                    $reportHistory = $reportHistory->get();
                    if ($reportHistory && count($reportHistory) > 0) {
                        for ($i = 0; $i < count($reportHistory); $i++) {
                            if (!$reportHistory[$i]->reportFile) {
                                $reportHistory[$i]->isFileUpload = false;
                            } else {
                                $reportHistory[$i]->isFileUpload = true;
                            }
                        }
                    }
                    $wallet = WalletTransaction::leftjoin('order_request', 'order_request.id', '=', 'wallettransaction.orderId')
                        ->leftjoin('astrologers', 'astrologers.id', '=', 'wallettransaction.astrologerId')
                        ->where('wallettransaction.userId', '=', $id);
                    $walletCount = $wallet->count();
                    $wallet->select('wallettransaction.*', 'astrologers.name', 'order_request.totalMin');
                    $wallet->orderBy('wallettransaction.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $wallet->skip($req->startIndex);
                        $wallet->take($req->fetchRecord);
                    }
                    $wallet = $wallet->get();

                    $payment = DB::table('payment')
                        ->where('userId', '=', $id)
                        ->orderBy('id', 'DESC');
                    $paymentCount = $payment->count();
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $payment->skip($req->startIndex);
                        $payment->take($req->fetchRecord);
                    }
                    $payment = $payment->get();

                    $notification = DB::table('user_notifications')
                        ->where('userId', '=', $id)
                        ->orderBy('id', 'DESC')
                        ->get();
                    $orderRequests = array(
                        'totalCount' => $orderRequestCount,
                        'order' => $orderRequest,
                    );

                    $pujaOrders = array(
                        'totalCount' => $pujaorderCount,
                        'pujaorder' => $pujaorder,
                    );

                    $giftLists = array(
                        'totalCount' => $giftListCount,
                        'gifts' => $giftList,
                    );
                    $chatHistorys = array(
                        'totalCount' => $chatHistoryCount,
                        'chatHistory' => $chatHistory,
                    );
                    $callHistorys = array(
                        'totalCount' => $callHistoryCount,
                        'callHistory' => $callHistory,
                    );
                    $reportHistorys = array(
                        'totalCount' => $reportHistoryCount,
                        'reportHistory' => $reportHistory,
                    );
                    $wallets = array(
                        'totalCount' => $walletCount,
                        'wallet' => $wallet,
                    );
                    $payments = array(
                        'totalCount' => $paymentCount,
                        'payment' => $payment,
                    );
                    $user[0]->follower = $follower;
                    $user[0]->orders = $orderRequests;
                    $user[0]->pujas = $pujaOrders;
                    $user[0]->sendGifts = $giftLists;
                    $user[0]->chatRequest = $chatHistorys;
                    $user[0]->callRequest = $callHistorys;
                    $user[0]->reportRequest = $reportHistorys;
                    $user[0]->walletTransaction = $wallets;
                    $user[0]->paymentLogs = $payments;
                    $user[0]->notification = $notification;
                    $result = json_decode($user);
                    return view('pages.customer-detail')->with(['result' => $result]);
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back('error', $e->getMessage());
        }
    }

    public function fetch_data(Request $request)
    {
        if ($request->ajax()) {
            $customers = DB::table('users as us')
                ->join('user_roles', 'user_roles.userId', '=', 'us.id')
                ->where('us.isActive', '=', true)
                ->where('us.isDelete', '=', false)
                ->where('user_roles.roleId', '=', 3)
                ->select('us.*', 'user_roles.roleId')
                ->orderBy('us.id', 'DESC')
                ->paginate(15);
            return view('pages.customer-list', compact('customers'))->render();
        }
    }

    public function printCustomerList(Request $request)
    {
        try {
            $customers = DB::table('users')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                ->where('users.isDelete', '=', false)
                ->where('user_roles.roleId', '=', 3)
                ->select('users.*', 'user_roles.roleId')
                ->orderBy('users.id', 'DESC');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $customers = $customers->where(function ($q) use ($searchString) {
                    $q
                        ->where('users.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $customers = $customers->get();
            $data = [
                'title' => 'Customers',
                'date' => Carbon::now()->format('d-m-Y h:i a'),
                'customers' => $customers,
            ];
            $pdf = PDF::loadView('pages.customerList', $data);
            return $pdf->download('CustomerList.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function exportCustomerCSV(Request $request)
    {
        $this->path = env('APP_URL');
        $customers = DB::table('users')
            ->join('user_roles', 'user_roles.userId', '=', 'users.id')
            ->where('users.isDelete', '=', false)
            ->where('user_roles.roleId', '=', 3)
            ->select('users.*', 'user_roles.roleId')
            ->orderBy('users.id', 'DESC');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $customers = $customers->where(function ($q) use ($searchString) {
                $q
                    ->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $customers = $customers->get();
        $headers = array(
            'Content-type' => 'text/csv',
        );
        $filename = public_path('CustomerList.csv');
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'ID',
            'Name',
            'ContactNo',
            'Email',
            'BirthDate',
            'BirthTime',
        ]);
        for ($i = 0; $i < count($customers); $i++) {
            fputcsv($handle, [
                $i + 1,
                $customers[$i]->name,
                $customers[$i]->contactNo,
                $customers[$i]->email,
                date('d-m-Y', strtotime($customers[$i]->birthDate)),
                $customers[$i]->birthTime,
            ]);
        }
        fclose($handle);
        return Response::download($filename, 'customerList.csv', $headers);
    }

    // Recharge wallet

    public function rechargewallet(Request $req)
    {
        DB::beginTransaction();

        try {
            $validator = Validator::make($req->all(), [
                'userId' => 'required',
                'amount' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            // Create a new payment record
            $payment = Payment::create([
                'amount' => $req->amount,
                'userId' => $req->userId,
                'paymentStatus' => 'success',
                'paymentMode' => 'Admin',
                'createdBy' => Auth::user()->id,
                'modifiedBy' => Auth::user()->id,
            ]);

            $userWallet = UserWallet::query()
                ->where('userId', '=', $req->userId)
                ->get();

            if ($userWallet && count($userWallet) > 0) {
                $userWallet[0]->amount = $userWallet[0]->amount + $req->amount;
                $userWallet[0]->save();  // Use save() instead of update()
            } else {
                $wallet = UserWallet::create([
                    'userId' => $req->userId,
                    'amount' => $req->amount,
                    'createdBy' => Auth::user()->id,
                    'modifiedBy' => Auth::user()->id,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Wallet recharged successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            // Return an error response
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
