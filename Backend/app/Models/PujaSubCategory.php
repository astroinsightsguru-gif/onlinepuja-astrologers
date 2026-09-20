<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PujaSubCategory extends Model
{
    use HasFactory;
    protected $table = 'puja_subcategories';
    protected $fillable = [
        'category_id',
        'name',
        'image',
    ];


    public function category()
    {
        return $this->belongsTo(PujaCategory::class, 'category_id');
    }
}
