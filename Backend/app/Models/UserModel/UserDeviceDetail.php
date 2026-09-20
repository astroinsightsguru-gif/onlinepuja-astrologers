<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceDetail extends Model
{
    use HasFactory;
    protected $table = 'user_device_details';
    protected $fillable = [
        'userId',
        'appId',
        'deviceId',
        'fcmToken',
        'deviceLocation',
        'deviceManufacturer',
        'deviceModel',
        'appVersion',
        'subscription_id',
        'subscription_id_web'
    ];
}
