<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payment';

    protected $fillable = [
        'paymentMode',
        'paymentReference',
        'paymentStatus',
        'inr_usd_conversion_rate',
        'amount',
        'createdBy',
        'modifiedBy',
        'userId',
        'signature',
        'orderId',
        'cashback_amount',
        'gst_amount',
        'payment_order_info',
        'payment_for',
        'durationchat',
        'chatId',
        'durationcall',
        'callId',
    ];

    protected $casts = [
        'payment_order_info' => 'array'
    ];

    // public $appends=['amount','cashback_amount'];

    // public function getAmountAttribute($val)
    // {
    //     $usdtoinrvalue=convertinrtousd($this->attributes['amount'],$this->inr_usd_conversion_rate?:1);
    //     $country = '';
    //     if(authcheck() || Auth::guard('api')->user()){
    //         $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
    //         $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
    //     }
    //     elseif(astroauthcheck()){
    //         $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
    //         $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
    //     }elseif(Auth::guard('web')->user()){
    //         $country = strtolower('India') == 'india' ? 'india' : 'international';
    //     }
    //     return ($country!='india' ? $usdtoinrvalue : $this->attributes['amount']);

    // }

    // public function getCashbackAmountAttribute($val)
    // {
    //     $usdtoinrvalue=convertinrtousd($this->attributes['cashback_amount'],$this->inr_usd_conversion_rate?:1);
    //     $country = '';
    //     if(authcheck() || Auth::guard('api')->user()){
    //         $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
    //         $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
    //     }
    //     elseif(astroauthcheck()){
    //         $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
    //         $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
    //     }elseif(Auth::guard('web')->user()){
    //         $country = strtolower('India') == 'india' ? 'india' : 'international';
    //     }
    //     return ($country!='india' ? $usdtoinrvalue : $this->attributes['cashback_amount']);
    // }
}
