<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pujapackage extends Model
{
    use HasFactory;

    protected $table = 'puja_package';
    protected $fillable = [
        'title',
        'person',
        'package_price',
        'description',
        'package_price_usd',

    ];

    protected $casts = [
        'description'=>'array'
    ];


    public function getPackagePriceAttribute($val)
    {
        /*
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountry = isset(authcheck()['country'])?authcheck()['country']:Auth::guard('api')->user()->country;
            $country = strtolower($authCountry) == 'india' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountry = isset(astroauthcheck()['country'])?astroauthcheck()['country']:Auth::guard('api')->user()->country;
            $country = strtolower($astroCountry) == 'india' ? 'india' : 'international';
        }
        return ($country=='international'?$this->package_price_usd:$val);
        */

        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }

        $amount = $country=='international' ? $this->package_price_usd:$val;
        if(systemflag('walletType') == 'Coin'){
           $amount = $country =='international' ? convertusdtocoin($this->package_price_usd):convertinrtocoin($val);
        }
        return $amount;

    }
}
