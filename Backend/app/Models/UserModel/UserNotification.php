<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    use HasFactory;
    protected $table = 'user_notifications';
    protected $fillable = [
        'userId',
        'title',
        'description',
        'notificationId',
        'createdBy',
        'modifiedBy',
        'notification_type'
    ];
}
