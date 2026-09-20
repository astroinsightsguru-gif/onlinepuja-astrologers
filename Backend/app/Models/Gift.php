<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    use HasFactory;
    protected $table = 'gifts';
    protected $fillable = [
        'name',
        'image',
        'amount',
        'amount_usd',
        'displayOrder',
        'createdBy',
        'modifiedBy'
    ];
}
