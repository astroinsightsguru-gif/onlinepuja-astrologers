<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerCategory extends Model
{
    use HasFactory;
    protected $table = 'astrologer_categories';
    protected $fillable = [
        'name',
        'image',
        'displayOrder',
        'createdBy',
        'modifiedBy'
    ];
}
