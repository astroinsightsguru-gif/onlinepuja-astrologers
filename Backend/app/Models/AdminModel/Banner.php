<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $table = 'banners';
    protected $fillable = [
        'bannerImage',
        'fromDate',
        'toDate',
        'bannerTypeId',
        'createdBy',
        'modifiedBy'
    ];
}
