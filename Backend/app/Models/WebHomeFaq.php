<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebHomeFaq extends Model
{
    use HasFactory;
    protected $table = 'web_home_faqs';
    protected $fillable = [
        'title',
        'description',
    ];
}
