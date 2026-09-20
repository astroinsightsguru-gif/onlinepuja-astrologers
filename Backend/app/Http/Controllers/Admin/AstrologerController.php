<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminModel\DegreeOrDiploma;
use App\Models\AdminModel\FulltimeJob;
use App\Models\AdminModel\HighestQualification;
use App\Models\AdminModel\Language;
use App\Models\AdminModel\MainSourceOfBusiness;
use App\Models\AdminModel\TravelCountry;
use App\Models\AstrologerModel\AstrologerAssistant;
use App\Models\AstrologerModel\AstrologerAvailability;
use App\Models\AstrologerModel\AstrologerCategory;
use App\Models\AstrologerModel\AstrologerGift;
use App\Models\AstrologerModel\Skill;
use App\Models\UserModel\CallRequest;
use App\Models\UserModel\ChatRequest;
use App\Models\UserModel\UserReport;
use App\Models\UserModel\UserRole;
use App\Models\Astrologer;
use App\Models\AstrologerDocument;
use App\Models\Country;
use App\Models\EmailTemplate;
use App\Models\ProfileBoosted;
use App\Models\PujaOrder;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Exception;
use PDF;
use Response;

class AstrologerController extends Controller
{
    // Get Customer
    public $user;
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function getAstrologer(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $req->page ?? 1;
                $paginationStart = ($page - 1) * $this->limit;
                $searchString = $req->searchString ?? null;

                $astrologers = Astrologer::where('isDelete', false)->where('isVerified', 1);
                if ($searchString) {
                    $astrologers->where(function ($query) use ($searchString) {
                        $query
                            ->where('name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }

                // Clone query for counting records
                $countQuery = clone $astrologers;
                // Date filter
                $from_date = $req->from_date ?? null;
                $to_date = $req->to_date ?? null;

                if ($from_date && $to_date) {
                    $astrologers->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $astrologers->where('created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $astrologers->where('created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('created_at', '<=', $to_date . ' 23:59:59');
                }

                $astrologerCount = $astrologers->count();
                $astrologers = $astrologers
                    ->orderBy('id', 'DESC')
                    ->skip($paginationStart)
                    ->take($this->limit)
                    ->get();

                if ($astrologers->isNotEmpty()) {
                    foreach ($astrologers as $astrologer) {
                        $avgRating = DB::table('user_reviews')
                            ->where('astrologerId', $astrologer->id)
                            ->avg('rating');

                        $astrologer->rating = $avgRating ?: 0;

                        $astrologer->totalCallRequest = DB::table('callrequest')
                            ->where('astrologerId', $astrologer->id)
                            ->count();

                        $astrologer->totalChatRequest = DB::table('chatrequest')
                            ->where('astrologerId', $astrologer->id)
                            ->count();
                    }
                }

                $totalPages = ceil($astrologerCount / $this->limit);
                $totalRecords = $astrologerCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = min(($this->limit * $page), $totalRecords);

                return view('pages.astrologer-list', compact('astrologers', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page', 'from_date', 'to_date'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function editTotalOrder(Request $request)
    {
        $astroId = $request->input('id');
        $totalOrder = $request->input('totalOrder');

        $astro = Astrologer::find($astroId);
        // dd($totalOrder);
        $astro->totalOrder = $totalOrder;
        $astro->save();

        return redirect()->back()->with('status', 'Status updated successfully!');
    }

    public function getAstrologerPendingRequest(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $req->page ? $req->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $astrologers = Astrologer::query();
                $astrologers = $astrologers->where('isDelete', '=', false)->where('isVerified', 0);
                $searchString = $req->searchString ? $req->searchString : null;
                if ($req->searchString) {
                    $astrologers = $astrologers->where(function ($q) use ($searchString) {
                        $q
                            ->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                // Clone query for counting records
                $countQuery = clone $astrologers;
                // Date filter
                $from_date = $req->from_date ?? null;
                $to_date = $req->to_date ?? null;

                if ($from_date && $to_date) {
                    $astrologers->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                    $countQuery->whereBetween('created_at', [$from_date . ' 00:00:00', $to_date . ' 23:59:59']);
                } elseif ($from_date) {
                    $astrologers->where('created_at', '>=', $from_date . ' 00:00:00');
                    $countQuery->where('created_at', '>=', $from_date . ' 00:00:00');
                } elseif ($to_date) {
                    $astrologers->where('created_at', '<=', $to_date . ' 23:59:59');
                    $countQuery->where('created_at', '<=', $to_date . ' 23:59:59');
                }

                $astrologerCount = $astrologers->count();
                $astrologers = $astrologers->orderBy('id', 'DESC');
                $astrologers->skip($paginationStart);
                $astrologers->take($this->limit);

                $astrologers = $astrologers->get();

                if ($astrologers && count($astrologers) > 0) {
                    foreach ($astrologers as $astro) {
                        $review = DB::table('user_reviews')
                            ->where('astrologerId', '=', $astro->id)
                            ->get();
                        if ($review && count($review) > 0) {
                            $avgRating = 0;
                            foreach ($review as $re) {
                                $avgRating += $re->rating;
                            }
                            $avgRating = $avgRating / count($review);
                            $astro['rating'] = $avgRating;
                        }
                        $totalCall = DB::table('callrequest')
                            ->where('astrologerId', '=', $astro['id'])
                            ->count();
                        $astro['totalCallRequest'] = $totalCall;
                        $totalChat = DB::table('chatrequest')
                            ->where('astrologerId', '=', $astro['id'])
                            ->count();
                        $astro['totalChatRequest'] = $totalChat;
                    }
                }
                $totalPages = ceil($astrologerCount / $this->limit);
                $totalRecords = $astrologerCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit
                    : $totalRecords;
                return view('pages.pending-astrologer-list',
                    compact('astrologers', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            $this->path = env('API_URL');
            $response = Http::post($this->path . '/user/delete/' . $id);
            $responseData = json_decode($response->getBody(), true);
            return back()->with('status', $responseData['message'] ?? 'User deleted successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function verifiedAstrologerApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $eid = $request->filed_id;
                $astrologer = Astrologer::find($eid);
                $astrologer->isVerified = !$astrologer->isVerified;
                $astrologer->update();
                $verifyStatus = $astrologer->isVerified ? 'Verified' : 'Unverified';
                $astrologerName = $astrologer->name;

                $logo = DB::table('systemflag')->where('name', 'AdminLogo')->select('value')->first();

                $verify = EmailTemplate::where('name', 'verify')->first();
                $unverify = EmailTemplate::where('name', 'unverify')->first();
                if ($verifyStatus == 'Verified' && $verify) {
                    $body = str_replace(
                        ['{{$username}}', '{{$logo}}'],
                        [$astrologerName, asset($logo->value)],
                        $verify->description
                    );

                    $body = html_entity_decode($body);
                    Mail::send([], [], function ($message) use ($astrologer, $verify, $body) {
                        $message
                            ->to($astrologer->email)
                            ->subject($verify->subject)
                            ->html($body);
                    });
                } else if ($verifyStatus == 'Unverified' && $unverify) {
                    $body = str_replace(
                        ['{{$username}}', '{{$logo}}'],
                        [$astrologerName, asset($logo->value)],
                        $unverify->description
                    );

                    $body = html_entity_decode($body);
                    Mail::send([], [], function ($message) use ($astrologer, $unverify, $body) {
                        $message
                            ->to($astrologer->email)
                            ->subject($unverify->subject)
                            ->html($body);
                    });
                }

                return response()->json([
                    'success' => 'Success',
                ]);
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function astrologerDetail()
    {
        return view('pages.astrologer-detail');
    }

    public function astrologerDetailApi(Request $req, $id)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astrologer = DB::table('astrologers')
                    ->where('id', '=', $req->id)
                    ->get();
                if ($astrologer) {
                    $astrologer[0]->allSkill = array_map('intval', explode(',', $astrologer[0]->allSkill));
                    $astrologer[0]->primarySkill = array_map('intval', explode(',', $astrologer[0]->primarySkill));
                    $astrologer[0]->languageKnown = array_map('intval', explode(',', $astrologer[0]->languageKnown));
                    $astrologer[0]->astrologerCategoryId = array_map('intval', explode(',', $astrologer[0]->astrologerCategoryId));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $astrologer[0]->allSkill)
                        ->select('name', 'id')
                        ->get();
                    $primarySkill = DB::table('skills')
                        ->whereIn('id', $astrologer[0]->primarySkill)
                        ->select('name', 'id')
                        ->get();
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $astrologer[0]->languageKnown)
                        ->select('languageName', 'id')
                        ->get();
                    $category = DB::table('astrologer_categories')
                        ->whereIn('id', $astrologer[0]->astrologerCategoryId)
                        ->select('name', 'id')
                        ->get();
                    $astrologer[0]->allSkill = $allSkill;
                    $astrologer[0]->primarySkill = $primarySkill;
                    $astrologer[0]->languageKnown = $languageKnown;
                    $astrologer[0]->astrologerCategoryId = $category;
                    $astrologerAvailability = DB::table('astrologer_availabilities')
                        ->where('astrologerId', '=', $req->id)
                        ->get();
                    if ($astrologerAvailability && count($astrologerAvailability) > 0) {
                        $day = [];
                        $working = [];
                        $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach ($day as $days) {
                            $day = array(
                                'day' => $days,
                            );
                            $currentday = $days;
                            $result = array_filter(json_decode($astrologerAvailability), function ($event) use ($currentday) {
                                return $event->day === $currentday;
                            });
                            $ti = [];

                            foreach ($result as $available) {
                                $time = array(
                                    'fromTime' => $available->fromTime,
                                    'toTime' => $available->toTime,
                                );
                                array_push($ti, $time);
                            }
                            $weekDay = array(
                                'day' => $days,
                                'time' => $ti,
                            );
                            array_push($working, $weekDay);
                        }
                        $astrologer[0]->astrologerAvailability = $working;
                    } else {
                        $astrologer[0]->astrologerAvailability = [];
                    }

                    $chatHistory = ChatRequest::join('users', 'users.id', '=', 'chatrequest.userId')
                        ->join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
                        ->select('chatrequest.*', 'users.name', 'users.contactNo', 'users.profile', 'astrologers.name as astrologerName', 'astrologers.charge')
                        ->where('chatrequest.astrologerId', '=', $req->id)
                        ->orderBy('chatrequest.id', 'DESC')
                        ->get();
                    $callHistory = CallRequest::join('users', 'users.id', '=', 'callrequest.userId')
                        ->join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                        ->select('callrequest.*', 'users.name', 'users.contactNo', 'users.profile', 'astrologers.name as astrologerName', 'astrologers.charge')
                        ->where('callrequest.astrologerId', '=', $req->id)
                        ->orderBy('callrequest.id', 'DESC')
                        ->get();

                    $wallet = WalletTransaction::leftjoin('order_request', 'order_request.id', '=', 'wallettransaction.orderId')
                        ->leftjoin('users', 'users.id', '=', 'order_request.userId')
                        ->leftJoin('users as user_wallet', 'user_wallet.id', '=', 'wallettransaction.createdBy')
                        ->select('wallettransaction.*', 'users.name', 'order_request.totalMin', 'user_wallet.name as productRefName')
                        ->where('wallettransaction.userId', '=', $astrologer[0]->userId)
                        ->orderBy('wallettransaction.id', 'DESC')
                        ->get();

                    $review = DB::table('user_reviews')
                        ->join('users as u', 'u.id', '=', 'user_reviews.userId')
                        ->where('astrologerId', '=', $req->id)
                        ->select('user_reviews.*', 'u.name as userName', 'u.profile')
                        ->orderBy('user_reviews.id', 'DESC')
                        ->get();

                    $reports = UserReport::join('users as u', 'u.id', '=', 'user_reports.userId')
                        ->join('report_types', 'report_types.id', '=', 'user_reports.reportType')
                        ->where('astrologerId', '=', $req->id)
                        ->select('user_reports.*', 'u.name as userName', 'u.profile', 'u.contactNo', 'report_types.reportImage', 'report_types.title as reportType')
                        ->orderBy('user_reports.id', 'DESC')
                        ->get();

                    $pujaorder = PujaOrder::where('astrologer_id', '=', $req->id);
                    $pujaorder->select('puja_orders.*');
                    $pujaorder->orderBy('puja_orders.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $pujaorder->skip($req->startIndex);
                        $pujaorder->take($req->fetchRecord);
                    }
                    $pujaorder = $pujaorder->get();

                    $callMin = DB::table('callrequest')
                        ->where('astrologerId', '=', $req->id)
                        ->sum('totalMin');

                    $chatMin = DB::table('chatrequest')
                        ->where('astrologerId', '=', $req->id)
                        ->sum('totalMin');
                    $follower = DB::table('astrologer_followers')
                        ->join('users', 'users.id', '=', 'astrologer_followers.userId')
                        ->where('astrologerId', '=', $req->id)
                        ->select('astrologer_followers.*', 'users.name as userName', 'users.contactNo', 'users.profile', 'users.id as userId')
                        ->get();

                    $notification = DB::table('user_notifications')
                        ->join('astrologers', 'astrologers.userId', 'user_notifications.userId')
                        ->where('astrologers.id', '=', $req->id)
                        ->select('user_notifications.*')
                        ->orderBy('user_notifications.id', 'DESC')
                        ->get();

                    $gift = AstrologerGift::join('users', 'users.id', '=', 'astrologer_gifts.userId')
                        ->join('gifts', 'gifts.id', '=', 'astrologer_gifts.giftId')
                        ->where('astrologer_gifts.astrologerId', '=', $req->id)
                        ->select('astrologer_gifts.*', 'users.name as userName',
                            'users.profile', 'users.contactNo', 'gifts.name as giftName', 'gifts.image as giftImage', 'gifts.amount as giftAmount')
                        ->get();
                    $fiveStarRating = 0;
                    $fourStarRating = 0;
                    $threeStarRating = 0;
                    $twoStarRating = 0;
                    $oneStarRating = 0;
                    if ($review && count($review) > 0) {
                        for ($i = 0; $i < count($review); $i++) {
                            if ($review[$i]->rating == 1) {
                                $oneStarRating += 1;
                            }
                            if ($review[$i]->rating == 2) {
                                $twoStarRating += 1;
                            }
                            if ($review[$i]->rating == 3) {
                                $threeStarRating += 1;
                            }
                            if ($review[$i]->rating == 4) {
                                $fourStarRating += 1;
                            }
                            if ($review[$i]->rating == 5) {
                                $fiveStarRating += 1;
                            }
                        }
                    }
                    $starRating = $oneStarRating + $twoStarRating
                        + $threeStarRating + $fourStarRating + $fiveStarRating;
                    error_log(empty($review));
                    $avgRating = $review && count($review) > 0 ? $starRating / count($review) : 0;
                    $rating = array(
                        'oneStarRating' => $oneStarRating > 0 ? $oneStarRating * 100 / count($review) : 0,
                        'twoStarRating' => $twoStarRating > 0 ? $twoStarRating * 100 / count($review) : 0,
                        'threeStarRating' => $threeStarRating > 0 ? $threeStarRating * 100 / count($review) : 0,
                        'fourStarRating' => $fourStarRating > 0 ? $fourStarRating * 100 / count($review) : 0,
                        'fiveStarRating' => $fiveStarRating > 0 ? $fiveStarRating * 100 / count($review) : 0,
                    );
                    $totalFollower = DB::Table('astrologer_followers')
                        ->where('astrologerId', '=', $req->id)
                        ->count();

                    $astrologer[0]->chatHistory = $chatHistory;
                    $astrologer[0]->callHistory = $callHistory;
                    $astrologer[0]->wallet = $wallet;
                    $astrologer[0]->review = $review;
                    $astrologer[0]->report = $reports;
                    $astrologer[0]->chatMin = $chatMin;
                    $astrologer[0]->callMin = $callMin;
                    $astrologer[0]->totalFollower = $totalFollower;
                    $astrologer[0]->astrologerRating = $rating;
                    $astrologer[0]->rating = $avgRating;
                    $astrologer[0]->follower = $follower;
                    $astrologer[0]->notification = $notification;
                    $astrologer[0]->gifts = $gift;
                    $astrologer[0]->pujaorders = $pujaorder;
                    $result = json_decode($astrologer);
                    return view('pages.astrologer-detail')->with(['result' => $result]);
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function printAstrologer(Request $req)
    {
        try {
            $astrologers = Astrologer::query();
            $astrologers = $astrologers->where('isDelete', '=', false);
            $searchString = $req->searchString ? $req->searchString : null;
            if ($req->searchString) {
                $astrologers = $astrologers->where(function ($q) use ($searchString) {
                    $q
                        ->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $astrologers = $astrologers->orderBy('id', 'DESC');
            $astrologers = $astrologers->get();
            if ($astrologers && count($astrologers) > 0) {
                foreach ($astrologers as $astro) {
                    $review = DB::table('user_reviews')
                        ->where('astrologerId', '=', $astro->id)
                        ->get();
                    if ($review && count($review) > 0) {
                        $avgRating = 0;
                        foreach ($review as $re) {
                            $avgRating += $re->rating;
                        }
                        $avgRating = $avgRating / count($review);
                        $astro['rating'] = $avgRating;
                    }
                    $totalCall = DB::table('callrequest')
                        ->where('astrologerId', '=', $astro['id'])
                        ->count();
                    $astro['totalCallRequest'] = $totalCall;
                    $totalChat = DB::table('chatrequest')
                        ->where('astrologerId', '=', $astro['id'])
                        ->count();
                    $astro['totalChatRequest'] = $totalChat;
                }
            }
            DB::table('systemflag')
                ->where('name', 'AdminLogo')
                ->select('value')
                ->first();
            $data = [
                'title' => 'Astrologers',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'astrologers' => $astrologers,
            ];
            $pdf = PDF::loadView('pages.astrologerList', $data);
            return $pdf->download('astrologers.pdf');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function exportAstrologer(Request $request)
    {
        $this->path = env('API_URL');
        $astrologers = Astrologer::query();
        $astrologers = $astrologers->where('isDelete', '=', false);
        $searchString = $request->searchString ? $request->searchString : null;
        if ($request->searchString) {
            $astrologers = $astrologers->where(function ($q) use ($searchString) {
                $q
                    ->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $astrologers = $astrologers->orderBy('id', 'DESC');
        $astrologers = $astrologers->get();
        if ($astrologers && count($astrologers) > 0) {
            foreach ($astrologers as $astro) {
                $review = DB::table('user_reviews')
                    ->where('astrologerId', '=', $astro->id)
                    ->get();
                if ($review && count($review) > 0) {
                    $avgRating = 0;
                    foreach ($review as $re) {
                        $avgRating += $re->rating;
                    }
                    $avgRating = $avgRating / count($review);
                    $astro['rating'] = $avgRating;
                }
                $totalCall = DB::table('callrequest')
                    ->where('astrologerId', '=', $astro['id'])
                    ->count();
                $astro['totalCallRequest'] = $totalCall;
                $totalChat = DB::table('chatrequest')
                    ->where('astrologerId', '=', $astro['id'])
                    ->count();
                $astro['totalChatRequest'] = $totalChat;
            }
        }
        $headers = array(
            'Content-type' => 'text/csv',
        );
        $filename = public_path('astrologers.csv');
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            'ID',
            'Name',
            'ContactNo',
            'Email',
            'Gender',
            'TotalCallRequest',
            'TotalChatRequest',
            'status',
        ]);
        for ($i = 0; $i < count($astrologers); $i++) {
            fputcsv($handle, [
                $i + 1,
                $astrologers[$i]->name,
                $astrologers[$i]->contactNo,
                $astrologers[$i]->email,
                $astrologers[$i]->gender,
                $astrologers[$i]->totalCallRequest,
                $astrologers[$i]->totalChatRequest,
                $astrologers[$i]->isVerified ? 'Verified' : 'UnVerified',
            ]);
        }
        fclose($handle);
        return Response::download($filename, 'astrologers.csv', $headers);
    }

    public function editAstrologer(Request $req)
    {
        $country = Country::all();
        $astrologer = Astrologer::find($req->id);
        $astrologerCategory = AstrologerCategory::query()->where('isActive', true)->where('isDelete', false)->get();
        $skills = Skill::query()->where('isActive', true)->where('isDelete', false)->get();
        $language = Language::query()->get();
        $mainSourceBusiness = MainSourceOfBusiness::query()->get();
        $highestQualification = HighestQualification::query()->get();
        $qualifications = DegreeOrDiploma::query()->get();
        $jobs = FulltimeJob::query()->get();
        $countryTravel = TravelCountry::query()->get();
        $astrologerAvailability = DB::table('astrologer_availabilities')->where('astrologerId', $req->id)->get();
        $day = [];
        $working = [];
        $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $documents = AstrologerDocument::query()->get();
        if ($astrologerAvailability && count($astrologerAvailability) > 0) {
            foreach ($day as $days) {
                $day = array(
                    'day' => $days,
                );
                $currentday = $days;
                $result =
                    array_filter(
                        json_decode($astrologerAvailability),
                        function ($event) use ($currentday) {
                            return $event->day === $currentday;
                        }
                    );
                $ti = [];
                foreach ($result as $available) {
                    // Process fromTime
                    if ($available->fromTime) {
                        $available->fromTime = $this->parseTime($available->fromTime);
                    }

                    // Process toTime
                    if ($available->toTime) {
                        $available->toTime = $this->parseTime($available->toTime);
                    }

                    $time = array(
                        'fromTime' => $available->fromTime,
                        'toTime' => $available->toTime,
                    );
                    array_push($ti, $time);
                }

                if (count($ti) == 0) {
                    $time = array(
                        'fromTime' => null,
                        'toTime' => null,
                    );
                    array_push($ti, $time);
                }
                $weekDay = array(
                    'day' => $days,
                    'time' => $ti,
                );
                array_push($working, $weekDay);
            }
            $astrologer->astrologerAvailability = $working;
        } else {
            foreach ($day as $days) {
                $ti = [];
                $time = array(
                    'fromTime' => null,
                    'toTime' => null,
                );
                array_push($ti, $time);
                $weekDay = array(
                    'day' => $days,
                    'time' => $ti,
                );
                array_push($working, $weekDay);
            }

            $astrologer->astrologerAvailability = $working;
        }

        return view('pages.edit-astrologer')->with(['astrologer' => $astrologer, 'astrologerCategory' => $astrologerCategory, 'skills' => $skills, 'language' => $language, 'mainSourceBusiness' => $mainSourceBusiness, 'highestQualification' => $highestQualification, 'qualifications' => $qualifications, 'jobs' => $jobs, 'countryTravel' => $countryTravel, 'documents' => $documents, 'country' => $country]);
    }

    private function parseTime($timeString)
    {
        try {
            // Try parsing as 12-hour format
            $time = Carbon::createFromFormat('h:i A', $timeString);
            return $time->format('H:i');
        } catch (\Exception $e) {
            try {
                // If it fails, try parsing as 24-hour format
                $time = Carbon::createFromFormat('H:i', $timeString);
                return $time->format('H:i');
            } catch (\Exception $e) {
                // If both fail, return the original time string or null
                return null;
            }
        }
    }

    public function editAstrologerApi(Request $req)
    {
        try {
            $data = $req->only(
                'id',
                'name',
                'email',
                'contactNo',
                'gender',
                'birthDate',
                'primarySkill',
                'allSkill',
                'languageKnown',
                'astro_video',
                'charge',
                'videoCallRate',
                'reportRate',
                'charge_usd',
                'videoCallRate_usd',
                'reportRate_usd',
                'experienceInYears',
                'dailyContribution',
                'hearAboutAstroguru',
                'isWorkingOnAnotherPlatform',
                'whyOnBoard',
                'interviewSuitableTime',
                'currentCity',
                'mainSourceOfBusiness',
                'highestQualification',
                'degree',
                'college',
                'learnAstrology',
                'astrologerCategoryId',
                'instaProfileLink',
                'facebookProfileLink',
                'linkedInProfileLink',
                'youtubeChannelLink',
                'websiteProfileLink',
                'isAnyBodyRefer',
                'referedPerson',
                'minimumEarning',
                'maximumEarning',
                'loginBio',
                'NoofforeignCountriesTravel',
                'currentlyworkingfulltimejob',
                'goodQuality',
                'biggestChallenge',
                'whatwillDo',
                'isVerified',
                'country',
                'countryCode',
                'whatsappNo',
                'pancardNo',
                'aadharNo',
                'ifscCode',
                'bankBranch',
                'bankName',
                'accountType',
                'accountNumber',
                'accountHolderName',
                'upi'
            );

            $astrologer = Astrologer::find($req->id);
            $user = User::find($astrologer->userId);

            $validator = Validator::make($data, [
                'id' => 'required',
                'astrologerCategoryId' => 'required',
                'name' => 'required|string',
                'contactNo' => 'required|unique:users,contactNo,' . $user->id,
                'email' => 'required|email|unique:users,email,' . $user->id,
                'gender' => 'required',
                'birthDate' => 'required',
                'dailyContribution' => 'required',
                'languageKnown' => 'required',
                'primarySkill' => 'required',
                'allSkill' => 'required',
                'interviewSuitableTime' => 'required',
                'mainSourceOfBusiness' => 'required',
                'minimumEarning' => 'required',
                'maximumEarning' => 'required',
                'NoofforeignCountriesTravel' => 'required',
                'currentlyworkingfulltimejob' => 'required',
                'goodQuality' => 'required',
                'biggestChallenge' => 'required',
                'whatwillDo' => 'required',
                'charge' => 'required',
                'whyOnBoard' => 'required',
                'highestQualification' => 'required',
                'countryCode' => 'required',
                'whatsappNo' => 'required',
                'aadharNo' => 'required',
                'pancardNo' => 'required',
                'ifscCode' => 'required',
                'bankBranch' => 'required',
                'bankName' => 'required',
                'accountNumber' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            // Handle Profile Image Upload
            $profileImagePath = $astrologer->profileImage;  // Keep existing path by default

            if ($req->hasFile('profileImage')) {
                try {
                    $imageContent = file_get_contents($req->file('profileImage')->getRealPath());
                    $time = Carbon::now()->timestamp;
                    $imageName = 'astro_' . $user->id . '_' . $time . '.png';
                    $profileImagePath = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'profileImage');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            // Handle Video Upload
            $videoPath = $astrologer->astro_video;  // Keep existing path by default

            if ($req->hasFile('astro_video')) {
                try {
                    $videoContent = file_get_contents($req->file('astro_video')->getRealPath());
                    $time = Carbon::now()->timestamp;
                    $videoName = 'astro_video_' . $user->id . '_' . $time . '.mp4';
                    $videoPath = StorageHelper::uploadToActiveStorage($videoContent, $videoName, 'astrovideo');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }
            $countryCode = !empty($req->countryCode) ? $req->countryCode : '+91';

            if (!str_starts_with($countryCode, '+')) {
                $countryCode = '+' . $countryCode;
            }

            // Update User
            if ($user) {
                $user->name = $req->name;
                $user->contactNo = $req->contactNo;
                $user->email = $req->email;
                $user->birthDate = $req->birthDate;
                $user->gender = $req->gender;
                $user->profile = $profileImagePath;  // Update profile image if changed
                $user->location = $req->currentCity;
                $user->countryCode = $countryCode;
                $user->country = $req->country ? $req->country : $user->country;
                $user->update();
            }

            // Generate unique slug
            $slug = Str::slug($req->name, '-');
            $originalSlug = $slug;
            $counter = 1;
            while (DB::table('astrologers')->where('slug', $slug)->where('id', '!=', $req->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Update Astrologer
            if ($astrologer) {
                $astrologer->name = $req->name;
                $astrologer->slug = $slug;
                $astrologer->email = $req->email;
                $astrologer->countryCode = $countryCode;
                $astrologer->contactNo = $req->contactNo;
                $astrologer->gender = $req->gender;
                $astrologer->birthDate = $req->birthDate;
                $astrologer->primarySkill = implode(',', $req->primarySkill);
                $astrologer->allSkill = implode(',', $req->allSkill);
                $astrologer->languageKnown = implode(',', $req->languageKnown);
                $astrologer->profileImage = $profileImagePath;  // Use the profile image path (new or existing)
                $astrologer->astro_video = $videoPath;  // Use the video path (new or existing)
                $astrologer->charge = $req->charge;
                $astrologer->videoCallRate = $req->videoCallRate;
                $astrologer->reportRate = $req->reportRate;
                $astrologer->charge_usd = $req->charge_usd;
                $astrologer->videoCallRate_usd = $req->videoCallRate_usd;
                $astrologer->reportRate_usd = $req->reportRate_usd;
                $astrologer->experienceInYears = $req->experienceInYears;
                $astrologer->dailyContribution = $req->dailyContribution;
                $astrologer->hearAboutAstroguru = $req->hearAboutAstroguru;
                $astrologer->isWorkingOnAnotherPlatform = $req->isWorkingOnAnotherPlatform;
                $astrologer->whyOnBoard = $req->whyOnBoard;
                $astrologer->interviewSuitableTime = $req->interviewSuitableTime;
                $astrologer->currentCity = $req->currentCity;
                $astrologer->mainSourceOfBusiness = $req->mainSourceOfBusiness;
                $astrologer->highestQualification = $req->highestQualification;
                $astrologer->degree = $req->degree;
                $astrologer->college = $req->college;
                $astrologer->learnAstrology = $req->learnAstrology;
                $astrologer->astrologerCategoryId = implode(',', $req->astrologerCategoryId);
                $astrologer->instaProfileLink = $req->instaProfileLink;
                $astrologer->linkedInProfileLink = $req->linkedInProfileLink;
                $astrologer->facebookProfileLink = $req->facebookProfileLink;
                $astrologer->websiteProfileLink = $req->websiteProfileLink;
                $astrologer->youtubeChannelLink = $req->youtubeChannelLink;
                $astrologer->isAnyBodyRefer = $req->isAnyBodyRefer;
                $astrologer->referedPerson = $req->referedPerson;
                $astrologer->minimumEarning = $req->minimumEarning;
                $astrologer->maximumEarning = $req->maximumEarning;
                $astrologer->loginBio = $req->loginBio;
                $astrologer->NoofforeignCountriesTravel = $req->NoofforeignCountriesTravel;
                $astrologer->currentlyworkingfulltimejob = $req->currentlyworkingfulltimejob;
                $astrologer->goodQuality = $req->goodQuality;
                $astrologer->biggestChallenge = $req->biggestChallenge;
                $astrologer->whatwillDo = $req->whatwillDo;
                $astrologer->country = $req->country ? $req->country : $astrologer->country;
                $astrologer->whatsappNo = $req->whatsappNo;
                $astrologer->aadharNo = $req->aadharNo;
                $astrologer->pancardNo = $req->pancardNo;
                $astrologer->ifscCode = $req->ifscCode;
                $astrologer->bankBranch = $req->bankBranch;
                $astrologer->accountType = $req->accountType;
                $astrologer->bankName = $req->bankName;
                $astrologer->accountNumber = $req->accountNumber;
                $astrologer->accountHolderName = $req->accountHolderName;
                $astrologer->upi = $req->upi;

                // Handle dynamic documents
                $documents = AstrologerDocument::all();
                foreach ($documents as $document) {
                    $inputName = Str::snake($document->name);

                    // Check if column exists, create if not
                    if (!Schema::hasColumn('astrologers', $inputName)) {
                        Schema::table('astrologers', function (Blueprint $table) use ($inputName) {
                            $table->string($inputName)->nullable();
                        });
                    }

                    if ($req->hasFile($inputName)) {
                        $docImage = base64_encode(file_get_contents($req->file($inputName)));
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/documents/';
                        $imageName = $inputName . '_' . $astrologer->id . '_' . $time;
                        $docPath = $destinationpath . $imageName . '.png';
                        file_put_contents($docPath, base64_decode($docImage));
                        $astrologer->$inputName = $docPath;
                    }
                }

                $astrologer->update();

                // Update Astrologer Availability
                if ($req->astrologerAvailability) {
                    DB::Table('astrologer_availabilities')
                        ->where('astrologerId', '=', $req->id)
                        ->delete();

                    foreach ($req->astrologerAvailability as $astrologeravailable) {
                        if (array_key_exists('time', $astrologeravailable)) {
                            foreach ($astrologeravailable['time'] as $availability) {
                                $fromTime = null;
                                $toTime = null;

                                if (isset($availability['fromTime']) && $availability['fromTime']) {
                                    $fromTime = Carbon::createFromFormat('H:i', $availability['fromTime'])->format('h:i A');
                                }
                                if (isset($availability['toTime']) && $availability['toTime']) {
                                    $toTime = Carbon::createFromFormat('H:i', $availability['toTime'])->format('h:i A');
                                }

                                AstrologerAvailability::create([
                                    'astrologerId' => $req->id,
                                    'day' => $astrologeravailable['day'],
                                    'fromTime' => $fromTime,
                                    'toTime' => $toTime,
                                    'createdBy' => $req->id,
                                    'modifiedBy' => $req->id,
                                ]);
                            }
                        }
                    }
                }

                return response()->json([
                    'message' => 'Astrologer updated successfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function addAstrologer()
    {
        $astrologerCategory = AstrologerCategory::query()->where('isActive', true)->where('isDelete', false)->get();
        $skills = Skill::query()->where('isActive', true)->where('isDelete', false)->get();
        $language = Language::query()->get();
        $mainSourceBusiness = MainSourceOfBusiness::query()->get();
        $highestQualification = HighestQualification::query()->get();
        $qualifications = DegreeOrDiploma::query()->get();
        $jobs = FulltimeJob::query()->get();
        $countryTravel = TravelCountry::query()->get();
        $documents = AstrologerDocument::query()->get();

        $country = Country::all();

        // Define the days of the week
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $working = [];

        foreach ($days as $day) {
            $ti = [];
            $time = [
                'fromTime' => null,
                'toTime' => null,
            ];
            array_push($ti, $time);
            $weekDay = [
                'day' => $day,
                'time' => $ti,
            ];
            array_push($working, $weekDay);
        }

        $astrologer = new Astrologer();
        $astrologer->astrologerAvailability = $working;

        return view('pages.astrologer-add')->with([
            'astrologer' => $astrologer,
            'astrologerCategory' => $astrologerCategory,
            'skills' => $skills,
            'language' => $language,
            'mainSourceBusiness' => $mainSourceBusiness,
            'highestQualification' => $highestQualification,
            'qualifications' => $qualifications,
            'jobs' => $jobs,
            'countryTravel' => $countryTravel,
            'country' => $country,
            'documents' => $documents,
            'days' => $days,  // Pass the days variable to the view
        ]);
    }

    public function addAstrologerApi(Request $req)
    {
        DB::beginTransaction();
        try {
            $data = $req->only(
                'name',
                'email',
                'contactNo',
                'countryCode',
                'gender',
                'birthDate',
                'primarySkill',
                'allSkill',
                'languageKnown',
                'profileImage',
                'charge',
                'experienceInYears',
                'dailyContribution',
                'isWorkingOnAnotherPlatform',
                'whyOnBoard',
                'interviewSuitableTime',
                'mainSourceOfBusiness',
                'highestQualification',
                'degree',
                'college',
                'learnAstrology',
                'astrologerCategoryId',
                'instaProfileLink',
                'facebookProfileLink',
                'linkedInProfileLink',
                'youtubeChannelLink',
                'websiteProfileLink',
                'isAnyBodyRefer',
                'minimumEarning',
                'maximumEarning',
                'loginBio',
                'NoofforeignCountriesTravel',
                'currentlyworkingfulltimejob',
                'goodQuality',
                'biggestChallenge',
                'whatwillDo',
                'isVerified',
                'country',
                'whatsappNo',
                'pancardNo',
                'aadharNo',
                'ifscCode',
                'bankBranch',
                'bankName',
                'accountType',
                'accountNumber',
                'upi',
                'videoCallRate',
                'reportRate',
                'astrologerCategoryId',
                'videoCallRate',
                'reportRate',
            );

            $validator = Validator::make($data, [
                'name' => 'required|string',
                'contactNo' => 'required|unique:users,contactNo',
                'email' => 'required|unique:users,email',
                'gender' => 'required',
                'birthDate' => 'required',
                'dailyContribution' => 'required',
                'astrologerCategoryId' => 'required',
                'languageKnown' => 'required',
                'primarySkill' => 'required',
                'allSkill' => 'required',
                'interviewSuitableTime' => 'required',
                'mainSourceOfBusiness' => 'required',
                'minimumEarning' => 'required',
                'maximumEarning' => 'required',
                'NoofforeignCountriesTravel' => 'required',
                'currentlyworkingfulltimejob' => 'required',
                'goodQuality' => 'required',
                'biggestChallenge' => 'required',
                'whatwillDo' => 'required',
                'charge' => 'required',
                'charge' => 'required',
                'whyOnBoard' => 'required',
                'highestQualification' => 'required',
                'country' => 'required',
                'countryCode' => 'required',
                'whatsappNo' => 'required',
                'aadharNo' => 'required',
                'pancardNo' => 'required',
                'ifscCode' => 'required',
                'bankBranch' => 'required',
                'bankName' => 'required',
                'accountNumber' => 'required',
                'videoCallRate' => 'required',
                'loginBio' => 'required',
                'reportRate' => 'required',
                // 'confirmaccountNumber' => 'required|same:accountNumber',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }

            // Handle profileImage upload
            $path = null;
            $time = Carbon::now()->timestamp;

            if ($req->hasFile('profileImage')) {
                $imageContent = file_get_contents($req->file('profileImage')->getRealPath());
                $extension = $req->file('profileImage')->getClientOriginalExtension() ?? 'png';
                $imageName = 'astro_' . $time . '.' . $extension;

                try {
                    // Upload to active storage (local / external)
                    $path = StorageHelper::uploadToActiveStorage($imageContent, $imageName, 'profileImage');
                } catch (Exception $ex) {
                    return response()->json(['error' => $ex->getMessage()]);
                }
            }

            $countryCode = !empty($req->countryCode) ? $req->countryCode : '+91';

            if (!str_starts_with($countryCode, '+')) {
                $countryCode = '+' . $countryCode;
            }

            // Create User
            $user = new User();
            $user->name = $req->name;
            $user->contactNo = $req->contactNo;
            $user->email = $req->email;
            $user->birthDate = $req->birthDate;
            $user->profile = $path;
            $user->gender = $req->gender;
            $user->location = $req->currentCity;
            $user->countryCode = $countryCode;
            $user->country = $req->country;
            $user->save();

            $referral_token = 'REF' . numberToCharacterString($user->id);
            $user->update([
                'referral_token' => $referral_token,
            ]);

            // Get the last inserted ID of the user
            $userId = $user->id;

            UserRole::create([
                'userId' => $userId,
                'roleId' => 2,
            ]);

            $slug = Str::slug($req->name, '-');
            $originalSlug = $slug;
            $counter = 1;
            while (DB::table('astrologers')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Create Astrologer
            $astrologer = new Astrologer();
            $astrologer->name = $req->name;
            $astrologer->userId = $userId;
            $astrologer->slug = $slug;
            $astrologer->email = $req->email;
            $astrologer->countryCode = $countryCode;
            $astrologer->contactNo = $req->contactNo;
            $astrologer->gender = $req->gender;
            $astrologer->birthDate = $req->birthDate;
            $astrologer->primarySkill = implode(',', $req->primarySkill);
            $astrologer->allSkill = implode(',', $req->allSkill);
            $astrologer->languageKnown = implode(',', $req->languageKnown);
            $astrologer->profileImage = $path;
            $astrologer->charge = $req->charge;
            $astrologer->experienceInYears = $req->experienceInYears;
            $astrologer->dailyContribution = $req->dailyContribution;
            $astrologer->hearAboutAstroguru = $req->hearAboutAstroguru;
            $astrologer->isWorkingOnAnotherPlatform = $req->isWorkingOnAnotherPlatform;
            $astrologer->whyOnBoard = $req->whyOnBoard;
            $astrologer->interviewSuitableTime = $req->interviewSuitableTime;
            $astrologer->currentCity = $req->currentCity;
            $astrologer->mainSourceOfBusiness = $req->mainSourceOfBusiness;
            $astrologer->highestQualification = $req->highestQualification;
            $astrologer->degree = $req->degree;
            $astrologer->college = $req->college;
            $astrologer->learnAstrology = $req->learnAstrology;
            $astrologer->astrologerCategoryId = implode(',', $req->astrologerCategoryId);
            $astrologer->instaProfileLink = $req->instaProfileLink;
            $astrologer->linkedInProfileLink = $req->linkedInProfileLink;
            $astrologer->facebookProfileLink = $req->facebookProfileLink;
            $astrologer->websiteProfileLink = $req->websiteProfileLink;
            $astrologer->youtubeChannelLink = $req->youtubeChannelLink;
            $astrologer->isAnyBodyRefer = $req->isAnyBodyRefer;
            $astrologer->minimumEarning = $req->minimumEarning;
            $astrologer->maximumEarning = $req->maximumEarning;
            $astrologer->loginBio = $req->loginBio;
            $astrologer->NoofforeignCountriesTravel = $req->NoofforeignCountriesTravel;
            $astrologer->currentlyworkingfulltimejob = $req->currentlyworkingfulltimejob;
            $astrologer->goodQuality = $req->goodQuality;
            $astrologer->biggestChallenge = $req->biggestChallenge;
            $astrologer->whatwillDo = $req->whatwillDo;
            $astrologer->videoCallRate = $req->videoCallRate;
            $astrologer->reportRate = $req->reportRate;
            $astrologer->nameofplateform = $req->nameofplateform;
            $astrologer->monthlyEarning = $req->monthlyEarning;
            $astrologer->referedPerson = $req->referedPerson;
            $astrologer->country = $req->country;
            $astrologer->charge_usd = $req->charge_usd;
            $astrologer->videoCallRate_usd = $req->videoCallRate_usd;
            $astrologer->reportRate_usd = $req->reportRate_usd;

            $astrologer->whatsappNo = $req->whatsappNo;
            $astrologer->aadharNo = $req->aadharNo;
            $astrologer->pancardNo = $req->pancardNo;
            $astrologer->ifscCode = $req->ifscCode;
            $astrologer->bankBranch = $req->bankBranch;
            $astrologer->accountType = $req->accountType;
            $astrologer->bankName = $req->bankName;
            $astrologer->accountNumber = $req->accountNumber;
            $astrologer->upi = $req->upi;
            $astrologer->accountHolderName = $req->accountHolderName;
            $astrologer->createdBy = $userId;
            $astrologer->modifiedBy = $userId;

            $documents = AstrologerDocument::query()->get();
            foreach ($documents as $document) {
                $columnName = Str::snake($document->name);

                if (!Schema::hasColumn('astrologers', $columnName)) {
                    Schema::table('astrologers', function (Blueprint $table) use ($columnName) {
                        $table->string($columnName)->nullable();
                    });
                }

                if ($req->hasFile($columnName)) {
                    $docImage = base64_encode(file_get_contents($req->file($columnName)));
                    if ($docImage) {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/documents/';
                        $imageName = $columnName . '_' . $req->id . '_' . $time;
                        $docPath = $destinationpath . $imageName . '.png';
                        if (!file_exists($docPath)) {
                            file_put_contents($docPath, base64_decode($docImage));
                        }
                        $astrologer->$columnName = $docPath;
                    }
                }
            }

            $astrologer->save();

            $astroId = $astrologer->id;
            if ($req->astrologerAvailability) {
                $availability = DB::Table('astrologer_availabilities')
                    ->where('astrologerId', '=', $req->id)
                    ->delete();
                foreach ($req->astrologerAvailability as $astrologeravailable) {
                    if (array_key_exists('time', $astrologeravailable)) {
                        foreach ($astrologeravailable['time'] as $availability) {
                            if ($availability['fromTime']) {
                                $availability['fromTime'] = Carbon::createFromFormat('H:i', $availability['fromTime'])->format('h:i A');
                            }
                            if ($availability['toTime']) {
                                $availability['toTime'] = Carbon::createFromFormat('H:i', $availability['toTime'])->format('h:i A');
                            }
                            AstrologerAvailability::create([
                                'astrologerId' => $astroId,
                                'day' => $astrologeravailable['day'],
                                'fromTime' => $availability['fromTime'],
                                'toTime' => $availability['toTime'],
                                'createdBy' => Auth::user()->id,
                                'modifiedBy' => Auth::user()->id,
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Astrologer added successfully',
            ]);
        } catch (\Exception $e) {
            DB::rollback();  // Rollback the transaction if an error occurs

            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    // ----------------------------------------------------------------------------------------------------------------------------------

    public function updateSectionStatus(Request $request)
    {
        $astrologer = Astrologer::find($request->astro_id);

        if ($request->section === 'call_sections') {
            $astrologer->call_sections = $request->status;
            $sectionName = 'Call';
        } elseif ($request->section === 'chat_sections') {
            $astrologer->chat_sections = $request->status;
            $sectionName = 'Chat';
        } elseif ($request->section === 'live_sections') {
            $astrologer->live_sections = $request->status;
            $sectionName = 'Live';
        } else {
            return response()->json(['message' => 'Invalid section'], 400);
        }

        $astrologer->save();

        $statusMessage = $request->status === '1' ? " $sectionName  section is ON SuccessFully !" : "$sectionName section is OFF SuccessFully !";
        return response()->json(['message' => $statusMessage]);
    }

    // -------------------------------------------------------------------------------------------------------------------------------------------------------
    public function astrologerAssistant(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $assistants = AstrologerAssistant::orderBy('id', 'DESC')
                    ->join('astrologers', 'astrologers.id', '=', 'astrologer_assistants.astrologerId')
                    ->select('astrologer_assistants.*', 'astrologers.name as astrologerName', 'astrologers.profileImage');
                // Pagination
                $assistants = $assistants->skip($paginationStart)->take($this->limit)->get();

                $totalRecords = $assistants->count();

                $totalPages = ceil($totalRecords / $this->limit);
                $page = min($page, $totalPages);

                $start = ($this->limit * ($page - 1)) + 1;
                $end = min($this->limit * $page, $totalRecords);

                return view('pages.astrologer-assistant', compact('assistants', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteassistant(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $assistant = AstrologerAssistant::find($request->del_id);
                if ($assistant) {
                    if ($assistant->profile) {
                        $path = $assistant->profile;

                        if (File::exists($path)) {
                            File::delete($path);
                        }
                    }
                    $assistant->delete();
                }
                return redirect()->back();
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
