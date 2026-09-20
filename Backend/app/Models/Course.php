<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Course extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'image',
        'description',
        'course_category_id',
        'isActive',
        'created_at',
        'updated_at',
        'course_badge',
        'course_price',
        'course_price_usd',

    ];

    public function category()
    {
        return $this->hasOne(CourseCategory::class,'id',localKey: 'course_category_id');
    }

     public function chapters()
    {
        return $this->hasMany(CourseChapter::class, 'course_id', 'id');
    }


    public function getCoursePriceAttribute($val)
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
        return ($country=='international'?$this->course_price_usd:$val);
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
         $amount = $country=='international' ? $this->course_price_usd:$val;
        if(systemflag('walletType') == 'Coin'){
           $amount = $country =='international' ? convertusdtocoin($this->course_price_usd):convertinrtocoin($val);
        }
        return $amount;
    }
}
