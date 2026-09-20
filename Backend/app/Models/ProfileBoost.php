<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileBoost extends Model
{
    use HasFactory;

    protected $table = 'astrologer_profile_boosts';

    protected $fillable = [
        'chat_commission',
        'call_commission',
        'video_call_commission',
        'profile_boost_benefits',
        'profile_boost',
    ];

    protected $casts = [
        'profile_boost_benefits' => 'array'
    ];
}
