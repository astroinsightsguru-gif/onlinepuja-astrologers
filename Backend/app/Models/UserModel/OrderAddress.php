<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;
    protected $table = 'order_addresses';
    protected $fillable = [
        'userId',
        'name',
        'phoneNumber',
        'phoneNumber2',
        'flatNo',
        'locality',
        'landmark',
        'city',
        'state',
        'country',
        'pincode',
        'createdBy',
        'modifiedBy',
        'countryCode',
        'alternateCountryCode',
    ];
}
