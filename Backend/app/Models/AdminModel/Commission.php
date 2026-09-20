<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;
    protected $table = 'commissions';
    protected $fillable = [
        'commissionTypeId',
        'commission',
        'createdBy',
        'modifiedBy',
        'astrologerId'
    ];
}
