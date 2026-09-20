<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ChatRequest extends Model
{
    use HasFactory;
    protected $table = 'chatrequest';
    public $timestamps = false;
    protected $fillable = [
        'astrologerId',
        'chatStatus',
        'userId',
        'chatRate',
        'totalMin',
        'deductionFromAstrologer',
        'deduction',
        'isFreeSession',
        'is_emergency',
        'chat_duration'
    ];

    public $appends = ['deduction', 'chatRate'];
    public function getDeductionAttribute($val)
    {
        /*
        $usdtoinrvalue=convertusdtoinr($this->attributes['deduction'],$this->inr_usd_conversion_rate?:1);
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountry = isset(authcheck()['country'])?authcheck()['country']:Auth::guard('api')->user()->country;
            $country = strtolower($authCountry) == 'india' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountry = isset(astroauthcheck()['country'])?astroauthcheck()['country']:Auth::guard('api')->user()->country;
            $country = strtolower($astroCountry) == 'india' ? 'india' : 'international';
        }elseif(Auth::guard('web')->user()){
            $country = strtolower('India') == 'india' ? 'india' : 'international';
        }
        return ($country=='india'?$usdtoinrvalue:$this->attributes['deduction']);
        */

        $usdtoinrvalue = convertinrtousd($this->attributes['deduction'], $this->inr_usd_conversion_rate ?: 1);
        $country = '';
        if (authcheck() || Auth::guard('api')->user()) {
            $authCountryCode = isset(authcheck()['countryCode']) ? authcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        } elseif (astroauthcheck()) {
            $astroCountryCode = isset(astroauthcheck()['countryCode']) ? astroauthcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        } elseif (Auth::guard('web')->user()) {
            $country = strtolower('India') == 'india' ? 'india' : 'international';
        }
        $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['deduction'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['deduction']);
        }
        return $amount;
    }

    public function getChatRateAttribute($val)
    {
        /*
        $usdtoinrvalue=convertusdtoinr($this->attributes['chatRate'],$this->inr_usd_conversion_rate?:1);
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountry = isset(authcheck()['country'])?authcheck()['country']:Auth::guard('api')->user()->country;
            $country = strtolower($authCountry) == 'india' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountry = isset(astroauthcheck()['country'])?astroauthcheck()['country']:Auth::guard('api')->user()->country;
            $country = strtolower($astroCountry) == 'india' ? 'india' : 'international';
        }elseif(Auth::guard('web')->user()){
            $country = strtolower('India') == 'india' ? 'india' : 'international';
        }
        return ($country=='india'?$usdtoinrvalue:$this->attributes['chatRate']);
        */

        $usdtoinrvalue = convertinrtousd($this->attributes['chatRate'], $this->inr_usd_conversion_rate ?: 1);
        $country = '';
        if (authcheck() || Auth::guard('api')->user()) {
            $authCountryCode = isset(authcheck()['countryCode']) ? authcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        } elseif (astroauthcheck()) {
            $astroCountryCode = isset(astroauthcheck()['countryCode']) ? astroauthcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        } elseif (Auth::guard('web')->user()) {
            $country = strtolower('India') == 'india' ? 'india' : 'international';
        }
          $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['chatRate'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['chatRate']);
        }
        return $amount;
    }
}
