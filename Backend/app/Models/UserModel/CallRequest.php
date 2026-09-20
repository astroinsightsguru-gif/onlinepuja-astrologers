<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CallRequest extends Model
{
    use HasFactory;
    protected $table = 'callrequest';
    public $timestamps = false;
    protected $fillable = [
        'astrologerId',
        'callStatus',
        'userId',
        'totalMin',
        'callRate',
        'deductionFromAstrologer',
        'deduction',
        'sId',
        'channelName',
        'chatId',
        'created_at',
        'sId1',
		'isFreeSession',
        'call_type',
		'call_duration',
		'call_method',
		'is_emergency',
		'IsSchedule',
		'schedule_date',
		'schedule_time',
        'inr_usd_conversion_rate',
    ];

    public $appends=['deduction','callRate'];

    public function getDeductionAttribute($val)
    {
        $usdtoinrvalue=convertinrtousd($this->attributes['deduction'],$this->inr_usd_conversion_rate?:1);
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
          $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['deduction'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['deduction']);
        }
        return $amount;
    }

    public function getCallRateAttribute($val)
    {

        $usdtoinrvalue=convertinrtousd($this->attributes['callRate'],$this->inr_usd_conversion_rate?:1);
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
         $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['callRate'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['callRate']);
        }
        return $amount;
    }
}
