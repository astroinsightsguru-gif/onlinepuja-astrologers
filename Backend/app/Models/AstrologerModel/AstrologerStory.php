<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerStory extends Model
{
    use HasFactory;
    protected $table = 'astrologer_stories';
    protected $fillable = [
        'astrologerId',
        'userId',
        'mediaType',
        'media',
        'created_at',
        'updated_at',

    ];


}
