<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileBoosted extends Model
{
    use HasFactory;

    protected $table = 'astrologer_boosted_profiles';

    protected $fillable = [
        'astrologer_id',
        'chat_commission',
        'call_commission',
        'video_call_commission',
        'boosted_datetime',
    ];

}
