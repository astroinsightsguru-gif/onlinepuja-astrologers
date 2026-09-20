<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SystemFlag extends Model
{
    use HasFactory;
    protected $table = 'systemflag';
    protected $fillable = [
        'valueType',
        'name',
        'value'
    ];

    // protected $appends = ['value'];

    // Method to determine the currency symbol based on user's country
    // public function getCurrencySymbolAttribute()
    public function getValueAttribute($val)
    {
        /*
        $crsmb = $val;
        if($this->name=='currencySymbol') {
            if (authcheck() || Auth::guard('api')->user()) {
                $authCountry = isset(authcheck()['country'])?authcheck()['country']:Auth::guard('api')->user()->country;
                $crsmb = strtolower($authCountry) == 'india' ? '₹' : '$';
            } elseif (astroauthcheck() || Auth::guard('api')->user()) {
                $astroCountry = isset(astroauthcheck()['country'])?astroauthcheck()['country']:Auth::guard('api')->user()->country;
                $crsmb = strtolower($astroCountry) == 'india' ? '₹' : '$';
            }
        }
        return $crsmb;
        */

        $crsmb = $val;
        if($this->name=='currencySymbol') {
            if (authcheck() || Auth::guard('api')->user()) {
                $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
                $crsmb = $authCountryCode == '+91' ? '₹' : '$';
            } elseif (astroauthcheck() || Auth::guard('api')->user()) {
                $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
                $crsmb = $astroCountryCode == '+91' ? '₹' : '$';
            }
        }
        return $crsmb;
    }

    // // Method to update the currency symbol in the database
    // public function updateCurrencySymbol()
    // {
    //     $currency = '$';

    //     if (authcheck()) {
    //         $authCountry = authcheck()['country'];
    //         $currency = $authCountry == 'india' ? '₹' : '$';
    //     } elseif (astroauthcheck()) {
    //         $astroCountry = astroauthcheck()['country'];
    //         $currency = $astroCountry == 'india' ? '₹' : '$';
    //     }

    //     // self::where('name', 'currencySymbol')->update(['value' => $currency]);
    // }
}
