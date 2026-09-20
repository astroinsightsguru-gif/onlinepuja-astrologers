<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PujaCategory extends Model
{
    use HasFactory;

    protected $table = 'puja_categories';
    protected $fillable = [
        'name',
        'image',

    ];

    public function pujas()
    {
        return $this->hasMany(Puja::class);
    }
}
