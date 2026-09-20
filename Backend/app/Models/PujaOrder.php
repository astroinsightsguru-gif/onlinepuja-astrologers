<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PujaOrder extends Model
{
    use HasFactory;

    protected $table = 'puja_orders';
    public $timestamps = false;
    protected $fillable = [
        'astrologer_id',
        'user_id',
        'puja_id',
        'puja_name',
        'package_id',
        'package_name',
        'package_person',
        'address_id',
        'address_name',
        'address_number',
        'address_flatno',
        'address_ locality',
        'address_city',
        'address_state',
        'address_country',
        'address_pincode',
        'order_price',
        'order_gst_amount',
        'order_total_price',
        'payment_type',
        'payment_id',
        'address_landmark',
        'puja_order_status',
        'puja_video',
        'puja_start_datetime',
        'puja_end_datetime',
        'astrologer_joined_at',
        'is_puja_approved',
        'inr_usd_conversion_rate',
        'reminder_sent',
        'puja_duration',
        'puja_refund_status'

    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function package()
    {
        return $this->belongsTo(Pujapackage::class, 'package_id');
    }

    public function astrologer()
    {
        return $this->belongsTo(Astrologer::class, 'astrologer_id');
    }



    //   public function Pujabroadcast($userid = 0, $returnHtml = true)
    //     {
    //         $currentDatetime = \Carbon\Carbon::now();
    //         $roomId = "puja_" . encrypt_to($this->id);
    //         $userid = $userid > 0 ? $userid : $this->user_id;

    //         if ($this->puja_start_datetime <= $currentDatetime && $this->puja_end_datetime >= $currentDatetime) {
    //             if ($returnHtml) {
    //                 // Return the full HTML link
    //                 return '<a href="' . route('broadcast.view', [$roomId, encrypt_to($userid)]) . '" class="puja-brodcast-btn">Join <span class="blinking-text">Live</span></a>';
    //             } else {
    //                 // Return only the URL
    //                 return route('broadcast.view', [$roomId, encrypt_to($userid)]);
    //             }
    //         } elseif ($this->puja_start_datetime > $currentDatetime) {
    //             return 'Link will be available soon';
    //         } else {
    //             return 'Completed';
    //         }
    //     }

    public function Pujabroadcast($userid = 0, $returnHtml = true)
    {
        $currentDatetime = \Carbon\Carbon::now();
        $roomId = "puja_" . encrypt_to($this->id);
        $userid = $userid > 0 ? $userid : $this->user_id;

        $pujaEndDatetime = \Carbon\Carbon::parse($this->puja_end_datetime);
        $pujaEndGraceTime = $pujaEndDatetime->copy()->addHours(2); // 2 hours after puja_end_datetime

        $puja_ended=\App\Models\Puja::where('id',$this->puja_id)->first();
        // First, if puja is manually ended
        if ($puja_ended && $puja_ended->isPujaEnded) {
            return 'Completed';
        }

        // If current time is between start and end + 2 hours
        if ($this->puja_start_datetime <= $currentDatetime && $currentDatetime <= $pujaEndGraceTime) {

            if ($returnHtml) {
                return '<a href="' . route('broadcast.view', [$roomId, encrypt_to($userid)]) . '" class="puja-brodcast-btn">Join <span class="blinking-text">Live</span></a>';
            } else {
                return route('broadcast.view', [$roomId, encrypt_to($userid)]);
            }
        }

        // If puja has not started yet
        if ($this->puja_start_datetime > $currentDatetime) {
            return 'Link will be available soon';
        }

        // Otherwise completed
        return 'Incomplete Puja';
    }

    public function getPujabroadcastAttribute()
    {
        return $this->Pujabroadcast($this->user_id);
    }


     public function getOrderPriceAttribute($val)
    {
          $usdtoinrvalue=convertinrtousd($this->attributes['order_price'],$this->inr_usd_conversion_rate?:1);
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }elseif(Auth::guard('web')->user()){
            $country = strtolower('India') == 'india' ? 'india' : 'international';
        }

       $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['order_price'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['order_price']);
        }
        return $amount;
    }

    public function getOrderTotalPriceAttribute($val)
    {
    $usdtoinrvalue=convertinrtousd($this->attributes['order_total_price'],$this->inr_usd_conversion_rate?:1);
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }elseif(Auth::guard('web')->user()){
            $country = strtolower('India') == 'india' ? 'india' : 'international';
        }

       $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['order_total_price'];
        if (systemflag('walletType') == 'Coin') {
        return ($country!='india' ? $usdtoinrvalue : $this->attributes['order_total_price']);
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['order_total_price']);
        }
         return $amount;
    }
}
