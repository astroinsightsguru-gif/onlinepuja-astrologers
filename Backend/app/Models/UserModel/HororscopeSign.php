<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HororscopeSign extends Model
{
    use HasFactory;
    protected $table = 'hororscope_signs';
    protected $fillable = [
        'name',
        'displayOrder',
        'image',
        'createdBy',
        'modifiedBy'
    ];
}
