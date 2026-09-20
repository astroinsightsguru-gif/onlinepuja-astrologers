<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LiveChat extends Model
{
    use HasFactory;
    protected $table = 'livechat';
    protected $fillable = [
     'userId',
     'partnerId',
     'chatId'
    ];
}
