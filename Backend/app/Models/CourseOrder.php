<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CourseOrder extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'astrologerId',
        'course_id',
        'course_price',
        'course_gst_amount',
        'course_total_price',
        'payment_type',
        'course_order_status',
        'course_completion_status',
        'inr_usd_conversion_rate'
    ];

    // In CourseOrder model
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function courseChapters()
    {
        return $this->hasManyThrough(CourseChapter::class, Course::class, 'id', 'course_id', 'course_id', 'id');
    }

    public function astrologer()
    {
        return $this->belongsTo(Astrologer::class, 'astrologerId');
    }


    public function getCoursePriceAttribute($val)
    {
        /*
        $usdtoinrvalue=convertusdtoinr($this->attributes['course_price'],$this->inr_usd_conversion_rate?:1);
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
        return ($country=='india'?$usdtoinrvalue:$this->attributes['course_price']);
        */

        $convertedValue=convertinrtousd($this->attributes['course_price'],$this->inr_usd_conversion_rate?:1);
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
         $amount = $country != 'india' ? $convertedValue : $this->attributes['course_price'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($convertedValue)  : convertinrtocoin($this->attributes['course_price']);
        }
        return $amount;

    }

    public function getCourseTotalPriceAttribute($val)
    {
        /*
        $usdtoinrvalue=convertusdtoinr($this->attributes['course_total_price'],$this->inr_usd_conversion_rate?:1);
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
        return ($country=='india'?$usdtoinrvalue:$this->attributes['course_total_price']);
        */

        $convertedValue=convertinrtousd($this->attributes['course_total_price'],$this->inr_usd_conversion_rate?:1);
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
         $amount = $country != 'india' ? $convertedValue : $this->attributes['course_total_price'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($convertedValue)  : convertinrtocoin($this->attributes['course_total_price']);
        }
        return $amount;
    }


}
