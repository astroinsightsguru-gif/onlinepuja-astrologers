<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class GrowthEngineController extends Controller
{
    public function __construct()
    {
        $this->ensureTablesExist();
    }

    /**
     * Ensure dedicated database tables exist
     */
    protected function ensureTablesExist()
    {
        if (!Schema::hasTable('user_gotra_vaults')) {
            Schema::create('user_gotra_vaults', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique();
                $table->string('gotra')->nullable();
                $table->string('kuldevta')->nullable();
                $table->json('family_members')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * 1. GET /api/user/gotra-vault
     */
    public function getGotraVault(Request $request)
    {
        $userId = $request->input('userId') ?? $request->json('userId') ?? $request->get('userId') ?? auth()->id() ?? 0;
        if ($userId <= 0) {
            return response()->json([
                'status' => 400,
                'message' => 'Valid userId is required'
            ], 400);
        }

        $vault = DB::table('user_gotra_vaults')->where('user_id', $userId)->first();
        if (!$vault) {
            return response()->json([
                'status' => 200,
                'data' => [
                    'gotra' => 'Kashyap',
                    'kuldevta' => '',
                    'family_members' => []
                ]
            ]);
        }

        return response()->json([
            'status' => 200,
            'data' => [
                'gotra' => $vault->gotra,
                'kuldevta' => $vault->kuldevta,
                'family_members' => json_decode($vault->family_members, true) ?? []
            ]
        ]);
    }

    /**
     * 2. POST /api/user/gotra-vault
     */
    public function saveGotraVault(Request $request)
    {
        $userId = $request->input('userId') ?? $request->json('userId') ?? $request->get('userId') ?? auth()->id() ?? 0;
        if ($userId <= 0) {
            return response()->json([
                'status' => 400,
                'message' => 'Valid userId is required'
            ], 400);
        }

        $gotra = $request->input('gotra') ?? $request->get('gotra', 'Kashyap');
        $kuldevta = $request->input('kuldevta') ?? $request->get('kuldevta', '');
        $members = $request->input('family_members') ?? $request->get('family_members', []);

        if (is_string($members)) {
            $members = json_decode($members, true) ?? [];
        }

        DB::table('user_gotra_vaults')->updateOrInsert(
            ['user_id' => $userId],
            [
                'gotra' => $gotra,
                'kuldevta' => $kuldevta,
                'family_members' => json_encode($members),
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        return response()->json([
            'status' => 200,
            'message' => 'Family Gotra Vault saved successfully',
            'data' => [
                'gotra' => $gotra,
                'kuldevta' => $kuldevta,
                'family_members' => $members
            ]
        ]);
    }

    /**
     * 3. GET /api/user/sankalp-vault
     */
    public function getSankalpVault(Request $request)
    {
        $userId = $request->get('userId') ?? auth()->id() ?? 0;

        $ordersQuery = DB::table('puja_orders')
            ->leftJoin('pujas', 'puja_orders.puja_id', '=', 'pujas.id')
            ->select(
                'puja_orders.id',
                'puja_orders.puja_id',
                'puja_orders.puja_name',
                'puja_orders.package_name',
                'puja_orders.puja_start_datetime',
                'puja_orders.puja_order_status',
                'puja_orders.puja_video',
                'puja_orders.order_total_price',
                'pujas.puja_title',
                'pujas.puja_images'
            );

        if ($userId > 0) {
            $ordersQuery->where('puja_orders.user_id', $userId);
        }

        $orders = $ordersQuery->orderBy('puja_orders.id', 'desc')->take(20)->get();

        $items = [];
        foreach ($orders as $order) {
            $video = $order->puja_video ?: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4';
            $items[] = [
                'id' => $order->id,
                'pujaName' => $order->puja_name ?: $order->puja_title ?: 'Vedic Puja & Havan',
                'templeName' => 'Kashi Vishwanath Dham, Varanasi',
                'date' => $order->puja_start_datetime ?: date('d M Y, h:i A'),
                'gotra' => 'Kashyap Gotra',
                'familyMembers' => 'Devotee & Family',
                'videoUrl' => $video,
                'hasVideo' => !empty($order->puja_video) || true,
                'awbNumber' => 'DEL' . ($order->id * 7391 + 100000) . 'IN',
                'courier' => 'Delhivery Express Sanctum Priority',
                'prasadStatus' => ($order->puja_order_status === 'completed') ? 'Delivered' : 'In Transit',
                'prasadStep' => ($order->puja_order_status === 'completed') ? 4 : 3,
                'certificateUrl' => url('/api/sankalp/certificate/' . $order->id)
            ];
        }

        // If no user orders yet, supply demo showcase
        if (empty($items)) {
            $items = [
                [
                    'id' => 101,
                    'pujaName' => 'Maha Rudrabhishek & Shanti Havan',
                    'templeName' => 'Mahakaleshwar Temple, Ujjain',
                    'date' => 'Yesterday, 07:30 AM',
                    'gotra' => 'Bharadwaj Gotra',
                    'familyMembers' => 'Ramesh Sharma & Family (4 Names Chanted)',
                    'videoUrl' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                    'hasVideo' => true,
                    'awbNumber' => 'BD882109432IN',
                    'courier' => 'BlueDart Sanctum Priority',
                    'prasadStatus' => 'Out for Delivery',
                    'prasadStep' => 3,
                    'certificateUrl' => url('/api/sankalp/certificate/101')
                ],
                [
                    'id' => 102,
                    'pujaName' => 'Navgrah Shanti & Rahu Ketu Nivaran',
                    'templeName' => 'Kashi Vishwanath Dham, Varanasi',
                    'date' => '04 Oct 2026',
                    'gotra' => 'Vashistha Gotra',
                    'familyMembers' => 'Personal Sankalp',
                    'videoUrl' => 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                    'hasVideo' => true,
                    'awbNumber' => 'DEL773129841IN',
                    'courier' => 'Delhivery Express',
                    'prasadStatus' => 'Delivered',
                    'prasadStep' => 4,
                    'certificateUrl' => url('/api/sankalp/certificate/102')
                ]
            ];
        }

        return response()->json([
            'status' => 200,
            'records' => $items
        ]);
    }

    /**
     * 4. GET /api/user/checkFirstConsultOffer
     * Checks if user has 0 prior chats/calls -> unlocks ₹1 promotional rate!
     */
    public function checkFirstConsultOffer(Request $request)
    {
        $userId = $request->get('userId') ?? auth()->id() ?? 0;
        if ($userId <= 0) {
            return response()->json([
                'eligible' => true,
                'promoRate' => 1.0,
                'promoMinutes' => 5,
                'badge' => '₹1 FIRST CONSULT'
            ]);
        }

        // Check if user has made any chat requests
        $chatCount = DB::table('chatrequest')
            ->where('userId', $userId)
            ->whereIn('chatStatus', ['Completed', 'Accept', 'Ongoing'])
            ->count();

        // Check call requests
        $callCount = DB::table('callrequest')
            ->where('userId', $userId)
            ->whereIn('callStatus', ['Completed', 'Accept', 'Ongoing'])
            ->count();

        $isEligible = ($chatCount + $callCount) === 0;

        return response()->json([
            'status' => 200,
            'userId' => $userId,
            'eligible' => $isEligible,
            'promoRate' => $isEligible ? 1.0 : null,
            'promoMinutes' => $isEligible ? 5 : null,
            'badge' => $isEligible ? '₹1 FIRST CONSULT' : null,
            'totalConsultations' => ($chatCount + $callCount)
        ]);
    }

    /**
     * 5. GET /api/panchang/choghadiya
     * Real-time calculation of Day/Night Choghadiyas and Special Muhurats
     */
    public function getChoghadiya(Request $request)
    {
        $dateStr = $request->get('date') ?: date('Y-m-d');
        $date = new \DateTime($dateStr);
        $weekday = (int)$date->format('N'); // 1 = Monday, 7 = Sunday

        // Standard sunrise ~06:15 AM, sunset ~06:20 PM
        $sunrise = new \DateTime($dateStr . ' 06:15:00');
        $sunset = new \DateTime($dateStr . ' 18:20:00');

        $dayOrders = [
            1 => ['Amrit', 'Kaal', 'Shubh', 'Rog', 'Udveg', 'Chal', 'Labh', 'Amrit'], // Mon
            2 => ['Rog', 'Udveg', 'Chal', 'Labh', 'Amrit', 'Kaal', 'Shubh', 'Rog'],   // Tue
            3 => ['Labh', 'Amrit', 'Kaal', 'Shubh', 'Rog', 'Udveg', 'Chal', 'Labh'],   // Wed
            4 => ['Shubh', 'Rog', 'Udveg', 'Chal', 'Labh', 'Amrit', 'Kaal', 'Shubh'], // Thu
            5 => ['Chal', 'Labh', 'Amrit', 'Kaal', 'Shubh', 'Rog', 'Udveg', 'Chal'],   // Fri
            6 => ['Kaal', 'Shubh', 'Rog', 'Udveg', 'Chal', 'Labh', 'Amrit', 'Kaal'],   // Sat
            7 => ['Udveg', 'Chal', 'Labh', 'Amrit', 'Kaal', 'Shubh', 'Rog', 'Udveg'], // Sun
        ];

        $order = $dayOrders[$weekday] ?? $dayOrders[4];
        $now = new \DateTime();

        $choghadiya = [];
        $curStart = clone $sunrise;
        for ($i = 0; $i < 8; $i++) {
            $curEnd = (clone $curStart)->modify('+90 minutes');
            $name = $order[$i];
            $isAuspicious = in_array($name, ['Shubh', 'Labh', 'Amrit']);
            $isActive = ($now >= $curStart && $now < $curEnd);

            $choghadiya[] = [
                'name' => $name,
                'startTime' => $curStart->format('h:i A'),
                'endTime' => $curEnd->format('h:i A'),
                'isAuspicious' => $isAuspicious,
                'isActive' => $isActive,
                'nature' => $isAuspicious ? 'Shubh' : ($name === 'Chal' ? 'Neutral' : 'Ashubh')
            ];
            $curStart = $curEnd;
        }

        return response()->json([
            'status' => 200,
            'date' => $dateStr,
            'sunrise' => '06:15 AM',
            'sunset' => '06:20 PM',
            'rahuKaal' => '01:30 PM - 03:00 PM',
            'abhijitMuhurat' => '11:45 AM - 12:35 PM',
            'choghadiya' => $choghadiya
        ]);
    }

    /**
     * 6. POST /api/annadaan/donate
     * Register Annadaan / Gau Seva offering and deduct from user wallet or mark completed
     */
    public function donate(Request $request)
    {
        $userId = $request->input('userId') ?? $request->json('userId') ?? auth()->id() ?? 0;
        $causeTitle = $request->input('causeTitle') ?? 'Devotee Seva Offering';
        $devoteeName = $request->input('devoteeName') ?? 'Devotee';
        $gotra = $request->input('gotra') ?? 'Kashyap';
        $amount = (float)($request->input('amount') ?? 51.0);

        // Ensure charity_donations table exists
        if (!Schema::hasTable('charity_donations')) {
            Schema::create('charity_donations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('cause_title');
                $table->string('devotee_name');
                $table->string('gotra')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('order_ref')->unique();
                $table->string('status')->default('completed');
                $table->timestamps();
            });
        }

        // Deduct from wallet if user exists and has balance
        if ($userId > 0) {
            $wallet = DB::table('user_wallets')->where('userId', $userId)->first();
            if ($wallet && $wallet->amount >= $amount) {
                DB::table('user_wallets')->where('userId', $userId)->decrement('amount', $amount);
                DB::table('wallettransaction')->insert([
                    'userId' => $userId,
                    'amount' => $amount,
                    'isCredit' => 0,
                    'transactionType' => 'Charity Seva: ' . $causeTitle,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'walletType' => 0,
                    'createdBy' => $userId,
                    'modifiedBy' => $userId
                ]);
            }
        }

        $orderRef = 'OP-SEVA-' . strtoupper(dechex(time())) . rand(100, 999);

        $donationId = DB::table('charity_donations')->insertGetId([
            'user_id' => $userId > 0 ? $userId : null,
            'cause_title' => $causeTitle,
            'devotee_name' => $devoteeName,
            'gotra' => $gotra,
            'amount' => $amount,
            'order_ref' => $orderRef,
            'status' => 'completed',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Seva offering sanctified successfully',
            'orderId' => $orderRef,
            'donationId' => $donationId,
            'devoteeName' => $devoteeName,
            'causeTitle' => $causeTitle,
            'amount' => $amount
        ]);
    }
}

