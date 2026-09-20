<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaitList extends Model
{
    use HasFactory;
    protected $table = 'waitlist';
    protected $fillable = [
        'userName',
        'profile',
        'time',
        'channelName',
        'requestType',
        'userId',
        'userFcmToken',
        'status',
        'astrologerId'
    ];
}
