<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;
    protected $table = 'coupons';
    protected $fillable = [
        'name',
        'couponCode',
        'validFrom',
        'validTo',
        'minAmount',
        'maxAmount',
        'description',
        'createdBy',
        'modifiedBy'
    ];
}
