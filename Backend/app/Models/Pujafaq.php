<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pujafaq extends Model
{
    use HasFactory;

    protected $table = 'puja_faqs';
    protected $fillable = [
        'title',
        'description',
    ];
}
