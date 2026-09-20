<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionType extends Model
{
    use HasFactory;
    protected $table = 'commission_types';
    protected $fillable = [
        'name',
        'createdBy',
        'modifiedBy'
    ];
}
