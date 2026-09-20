<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MstControl extends Model
{
    use HasFactory;

    protected $table = "mst_control";
    protected $fillable = [
        'astro_api_call_type'
    ];
}
