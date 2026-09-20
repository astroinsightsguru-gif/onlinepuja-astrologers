<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $table = 'astrotalk_in_news';
    protected $fillable = [
        'newsDate',
        'channel',
        'link',
        'bannerImage',
        'description',
        'createdBy',
        'modifiedBy',
    ];
}
