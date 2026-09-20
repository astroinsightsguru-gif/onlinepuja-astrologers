<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologyVideo extends Model
{
    use HasFactory;
    protected $table = 'astrology_videos';
    protected $fillable = [
        'youtubeLink',
        'coverImage',
        'videoTitle',
        'createdBy',
        'description',
        'isActive',
        'modifiedBy'
    ];
}
