<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DefaultProfile extends Model
{
    use HasFactory;
    protected $table = 'defaultprofile';
    protected $fillable = [
        'name',
        'profile',
    ];
}
