<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AstromallProduct extends Model
{
    use HasFactory;
    protected $table = 'astromall_products';
    protected $fillable = [
        'name',
        'features',
        'productImage',
        'productCategoryId',
        'amount',
        'usd_amount',
        'description',
        'createdBy',
        'modifiedBy',
        'slug',
    ];


    public function getAmountAttribute($val)
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
        return ($country=='international'?$this->usd_amount:$val);
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
         $amount = $country=='international' ? $this->usd_amount:$val;
        if(systemflag('walletType') == 'Coin'){
           $amount = $country =='international' ? convertusdtocoin($this->usd_amount):convertinrtocoin($val);
        }
        return $amount;

    }



}

