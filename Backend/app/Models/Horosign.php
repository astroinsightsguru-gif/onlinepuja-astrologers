<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horosign extends Model
{
    use HasFactory;
    protected $table = 'hororscope_signs';
    protected $fillable = [
        'name',
        'displayOrder',
        'image',
        'createdBy',
        'modifiedBy',
    ];
}
