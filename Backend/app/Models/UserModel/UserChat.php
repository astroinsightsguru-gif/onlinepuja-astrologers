<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChat extends Model
{
    use HasFactory;
    protected $table = 'chat_request';
    protected $fillable = [
     'userId',
     'partnerId',
     'chatId'
    ];
}
