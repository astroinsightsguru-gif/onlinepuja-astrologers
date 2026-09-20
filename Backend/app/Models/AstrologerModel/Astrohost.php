<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Astrohost extends Model
{
    use HasFactory;
    protected $table = 'astrohost';
    protected $fillable = [
        'astrologerId',
        'hostId'
    ];
}
