<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerProduct extends Model
{
    use HasFactory;
    protected $table = 'astrologer_product';
    protected $fillable = [
        'productId',
        'astrologerId',
        'productPrice',
        'createdBy',
        'modifiedBy'
    ];
}
