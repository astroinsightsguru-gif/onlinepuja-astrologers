<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveAstro extends Model
{
    use HasFactory;
    protected $table = 'liveastro';
    protected $fillable = [
        'astrologerId',
        'channelName',
        'token',
        'isActive',
        'liveChatToken',
		'schedule_live_date',
        'schedule_live_time',
		'stream_method'
    ];
}
