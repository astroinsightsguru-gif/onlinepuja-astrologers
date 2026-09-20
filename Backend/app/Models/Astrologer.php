<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Astrologer extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'astrologers';
    protected $fillable = [
        'userId',
        'name',
        'email',
        'contactNo',
        'gender',
        'birthDate',
        'primarySkill',
        'allSkill',
        'languageKnown',
        'profileImage',
        'charge',
        'experienceInYears',
        'dailyContribution',
        'hearAboutAstroguru',
        'isWorkingOnAnotherPlatform',
        'whyOnBoard',
        'interviewSuitableTime',
        'currentCity',
        'mainSourceOfBusiness',
        'highestQualification',
        'degree',
        'college',
        'learnAstrology',
        'astrologerCategoryId',
        'instaProfileLink',
        'facebookProfileLink',
        'linkedInProfileLink',
        'youtubeChannelLink',
        'websiteProfileLink',
        'isAnyBodyRefer',
        'minimumEarning',
        'maximumEarning',
        'loginBio',
        'NoofforeignCountriesTravel',
        'currentlyworkingfulltimejob',
        'goodQuality',
        'biggestChallenge',
        'whatwillDo',
        'isVerified',
        'videoCallRate',
        'reportRate',
        'nameofplateform',
        'monthlyEarning',
        'referedPerson',
        'charge_usd',
        'videoCallRate_usd',
        'reportRate_usd',
        'country',
        'slug',
        'whatsappNo',
        'pancardNo',
        'aadharNo',
        'ifscCode',
        'bankBranch',
        'bankName',
        'accountType',
        'accountNumber',
        'upi',
        'emergency_chat_charge',
        'emergency_audio_charge',
        'emergency_video_charge',
        'emergencyCallStatus',
        'emergencyChatStatus',
        'accountHolderName'
    ];

    public $appends = ['charge', 'videoCallRate', 'reportRate'];

    public function getChargeAttribute($val)
    {
        /*
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountry = isset(authcheck()['country'])?authcheck()['country']:Auth::guard('api')->user()->country;
            $country = strtolower($authCountry) == 'india' ? 'india' : 'international';
        }
        elseif(astroauthcheck() ){
            $astroCountry = isset(astroauthcheck()['country'])?astroauthcheck()['country']:Auth::guard('api')->user()->country;
            $country = strtolower($astroCountry) == 'india' ? 'india' : 'international';
        }
        return ($country=='international'?$this->charge_usd:$this->attributes['charge']);
        */

        $country = '';
        if (authcheck() || Auth::guard('api')->user()) {
            $authCountryCode = isset(authcheck()['countryCode']) ? authcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        } elseif (astroauthcheck()) {
            $astroCountryCode = isset(astroauthcheck()['countryCode']) ? astroauthcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }
        $amount = $country == 'international' ? $this->charge_usd : $this->attributes['charge'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country == 'international' ? convertusdtocoin($this->charge_usd) : convertinrtocoin($this->attributes['charge']);
        }
        return $amount;
    }



    public function getVideoCallRateAttribute($val)
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
        return ($country=='international'?$this->videoCallRate_usd:$this->attributes['videoCallRate']);
        */

        $country = '';
        if (authcheck() || Auth::guard('api')->user()) {
            $authCountryCode = isset(authcheck()['countryCode']) ? authcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        } elseif (astroauthcheck()) {
            $astroCountryCode = isset(astroauthcheck()['countryCode']) ? astroauthcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }

        $amount = $country == 'international' ? $this->videoCallRate_usd : $this->attributes['videoCallRate'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country == 'international' ? convertusdtocoin($this->videoCallRate_usd) : convertinrtocoin($this->attributes['videoCallRate']);
        }
        return $amount;
    }

    public function getReportRateAttribute($val)
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
        return ($country=='international'?$this->reportRate_usd:$this->attributes['reportRate']);
        */

        $country = '';
        if (authcheck() || Auth::guard('api')->user()) {
            $authCountryCode = isset(authcheck()['countryCode']) ? authcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        } elseif (astroauthcheck()) {
            $astroCountryCode = isset(astroauthcheck()['countryCode']) ? astroauthcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }
        $amount = $country == 'international' ? $this->reportRate_usd : $this->attributes['reportRate'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country == 'international' ? convertusdtocoin($this->reportRate_usd) : convertinrtocoin($this->attributes['reportRate']);
        }
        return $amount;
    }

}
