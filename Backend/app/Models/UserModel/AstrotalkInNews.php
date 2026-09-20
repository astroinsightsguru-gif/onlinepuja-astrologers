<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrotalkInNews extends Model
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
        'modifiedBy'
    ];
}
