<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Exception;

class CouponController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function addCoupon()
    {
        return view('pages.coupon-list');
    }

    public function addCouponApi(Request $request)
    {
        try {
            $this->path = env('API_URL');
            $http = new \GuzzleHttp\Client;

            $name = $request->name;
            $couponCode = $request->couponCode;
            $validFrom = $request->validFrom;
            $validTo = $request->validTo;
            $minAmount = $request->minAmount;
            $maxAmount = $request->maxAmount;
            $description = $request->description;
            $session = new Session();
            $tokenValue = $session->get('tokenVal');

            $http->post($this->path . '/couponcode/add', [
                'headers' => [
                    'Authorization' => 'Bearer' . $tokenValue,
                ],
                'query' => [
                    'name' => $name,
                    'couponCode' => $couponCode,
                    'validFrom' => $validFrom,
                    'validTo' => $validTo,
                    'minAmount' => $minAmount,
                    'maxAmount' => $maxAmount,
                    'description' => $description,
                ],
            ]);

            return redirect()->route('coupon-list');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Get Skill Api

    public function setCouponPage(Request $request)
    {
        try {
            $this->path = env('API_URL');
            $page = $request->page;
            $this->paginationStart = ($page - 1) * $this->limit;
            $http = new \GuzzleHttp\Client;
            $session = new Session();
            $tokenValue = $session->get('tokenVal');
            $page = $request->page ? $request->page : 1;
            $response = $http->post($this->path . '/getCouponcode', [
                'headers' => [
                    'Authorization' => 'Bearer' . $tokenValue,
                ],
                'multipart' => [
                    [
                        'name' => 'startIndex',
                        'contents' => $this->paginationStart,
                    ],
                    [
                        'name' => 'fetchRecord',
                        'contents' => $this->limit,
                    ],
                ],
            ]);
            $result = json_decode((string) $response->getBody(), true);
            $totalPages = ceil($result['totalRecords'] / $this->limit);
            $coupons = $result['recordList'];
            $totalRecords = $result['totalRecords'];
            $start = ($this->limit * ($page - 1)) + 1;
            $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
            return view('pages.coupon-list', compact('coupons', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getCoupon(Request $request)
    {
        try {
            $this->path = env('API_URL');
            $http = new \GuzzleHttp\Client;
            $session = new Session();
            $tokenValue = $session->get('tokenVal');
            $page = $request->page ? $request->page : 1;
            $response = $http->post($this->path . '/getCouponcode', [
                'headers' => [
                    'Authorization' => 'Bearer' . $tokenValue,
                ],
                'multipart' => [
                    [
                        'name' => 'startIndex',
                        'contents' => $this->paginationStart,
                    ],
                    [
                        'name' => 'fetchRecord',
                        'contents' => $this->limit,
                    ],
                ],
            ]);
            $result = json_decode((string) $response->getBody(), true);
            $totalPages = ceil($result['totalRecords'] / $this->limit);
            $coupons = $result['recordList'];
            $totalRecords = $result['totalRecords'];
            $start = ($this->limit * ($page - 1)) + 1;
            $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
            return view('pages.coupon-list', compact('coupons', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Edit

    public function editCoupon()
    {
        return view('pages.coupon-list');
    }

    public function editCouponApi(Request $request)
    {
        try {
            $this->path = env('API_URL');
            $http = new \GuzzleHttp\Client;
            $session = new Session();
            $tokenValue = $session->get('tokenVal');

            $eid = $request->filed_id;
            $response = $http->post($this->path . '/couponcode/update', [
                'headers' => [
                    'Authorization' => 'Bearer' . $tokenValue,
                ],
                'multipart' => [
                    [
                        'name' => 'id',
                        'contents' => $eid,
                    ],
                    [
                        'name' => 'name',
                        'contents' => $request->name,
                    ],
                    [
                        'name' => 'couponCode',
                        'contents' => $request->couponCode,
                    ],
                    [
                        'name' => 'validFrom',
                        'contents' => $request->validFrom,
                    ],
                    [
                        'name' => 'validTo',
                        'contents' => $request->validTo,
                    ],
                    [
                        'name' => 'minAmount',
                        'contents' => $request->minAmount,
                    ],
                    [
                        'name' => 'maxAmount',
                        'contents' => $request->maxAmount,
                    ],
                    [
                        'name' => 'description',
                        'contents' => $request->description,
                    ],
                ],
            ]);
            $response = $response->getBody();
            return redirect()->route('coupon-list');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function couponStatus(Request $request)
    {
        return view('pages.coupon-list');
    }

    public function couponStatusApi(Request $request)
    {
        try {
            $this->path = env('API_URL');
            $http = new \GuzzleHttp\Client;
            $session = new Session();
            $tokenValue = $session->get('tokenVal');
            $sid = $request->status_id;
            $response = $http->post($this->path . '/couponStatus/update/' . $sid, [
                'headers' => [
                    'Authorization' => 'Bearer' . $tokenValue,
                ],
            ]);
            $response = $response->getBody();

            return redirect()->route('coupon-list');
        } catch (Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
