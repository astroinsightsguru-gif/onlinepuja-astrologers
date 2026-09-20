<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserOrder extends Model
{
    use HasFactory;
    protected $table = 'order_request';
    public $timestamps = false;
    protected $fillable = [
        'productCategoryId',
        'productId',
        'orderAddressId',
        'payableAmount',
        'walletBalanceDeducted',
        'totalPayable',
        'paymentMethod',
        'orderStatus',
        'userId',
        'gstPercent',
        'orderType',
        'inr_usd_conversion_rate',
        'pro_recommend_id'
    ];

    public $appends=['payableAmount','totalPayable'];

    public function getPayableAmountAttribute($val)
    {
        /*
        $usdtoinrvalue=convertusdtoinr($this->attributes['payableAmount'],$this->inr_usd_conversion_rate?:1);
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
        }else{
            $country='india';
        }
        return ($country=='india'?$usdtoinrvalue:$this->attributes['payableAmount']);
        */

        $usdtoinrvalue=convertinrtousd($this->attributes['payableAmount'],$this->inr_usd_conversion_rate?:1);
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }elseif(Auth::guard('web')->user()){
            $country = 'india';
        }
        $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['payableAmount'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['payableAmount']);
        }
        return $amount;
    }

    public function getTotalPayableAttribute($val)
    {
        /*
        $usdtoinrvalue=convertusdtoinr($this->attributes['totalPayable'],$this->inr_usd_conversion_rate?:1);
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
        }else{
            $country='india';
        }
        return ($country=='india'?$usdtoinrvalue:$this->attributes['totalPayable']);
        */

        $usdtoinrvalue=convertinrtousd($this->attributes['totalPayable'],$this->inr_usd_conversion_rate?:1);
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }elseif(Auth::guard('web')->user()){
            $country = 'india';
        }

        $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['totalPayable'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['totalPayable']);
        }
        return $amount;
    }
}
