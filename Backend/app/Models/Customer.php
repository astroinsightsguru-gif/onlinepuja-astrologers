<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $table = 'users';
    protected $fillable = [
        'name',
        'password',
        'contactNo',
        'email',
        'birthDate',
        'birthTime',
        'profile',
        'birthPlace',
        'addressLine1',
        'location',
        'pincode',
        'gender',
        'countryCode',
        'country',
        'referral_token'
    ];
}
