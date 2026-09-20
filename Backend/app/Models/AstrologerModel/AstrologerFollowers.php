<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerFollowers extends Model
{
    use HasFactory;
    protected $table = 'astrologer_followers';
    protected $fillable = [
        'astrologerId',
        'userId',
        'createdBy',
        'modifiedBy'
    ];
}
