<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WalletTransaction extends Model
{
    use HasFactory;
    protected $table = 'wallettransaction';
    public $timestamps = false;

    public $appends = ['amount'];

    protected $fillable = [
        'callId',
        'inr_usd_conversion_rate',
        'amount',
        'coin',
        'userId',
        'transactionType',
        'orderId',
        'puja_recommend_id',
        'created_at',
        'updated_at',
        'createdBy',
        'modifiedBy',
        'isCredit',
        'walletType',
        'astrologerId',
        'aiAstrologerId',
    ];


    public function getAmountAttribute($val)
    {
        $usdtoinrvalue = convertinrtousd($this->attributes['amount'], $this->inr_usd_conversion_rate ?: 1);
        $country = '';
        if (authcheck() || Auth::guard('api')->user()) {
            $authCountryCode = isset(authcheck()['countryCode']) ? authcheck()['countryCode'] : Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
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
