<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralSetting extends Model
{
    use HasFactory;

    protected $table = 'referral_settings';
    protected $fillable = [
        'amount',
        'amount_usd',
        'max_user_limit',
    ];
}
