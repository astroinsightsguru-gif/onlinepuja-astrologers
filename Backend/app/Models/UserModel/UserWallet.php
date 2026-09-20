<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class UserWallet extends Model
{
    use HasFactory;
    protected $table = 'user_wallets';
    protected $fillable = [
        'userId',
        'amount',
        'coins',
        'createdBy',
        'modifiedBy'
    ];

      public function user()
    {
        return $this->belongsTo(User::class, 'userId');

    }

        public $appends = ['amount'];

    public function getAmountAttribute($val)
    {
        $usdtoinrvalue = convertinrtousd($this->attributes['amount']);
        $country = '';
        if (authcheck() || Auth::guard('api')->user()) {
            $authCountryCode = isset(authcheck()['countryCode']) ? authcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = $authCountryCode == '+91' ? 'india' : 'international';
        } elseif (astroauthcheck()) {
            $astroCountryCode = isset(astroauthcheck()['countryCode']) ? astroauthcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = $astroCountryCode == '+91' ? 'india' : 'international';
        } elseif (Auth::guard('web')->user()) {
            $country = strtolower('India') == 'india' ? 'india' : 'international';
        }
         $amount = $country != 'india' ? $usdtoinrvalue : $this->attributes['amount'];
        if (systemflag('walletType') == 'Coin') {
            $amount = $country != 'india' ? convertusdtocoin($usdtoinrvalue)  : convertinrtocoin($this->attributes['amount']);
        }
        return $amount;
    }
}
