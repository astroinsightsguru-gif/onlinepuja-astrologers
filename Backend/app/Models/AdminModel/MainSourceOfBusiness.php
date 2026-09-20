<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainSourceOfBusiness extends Model
{
    use HasFactory;
    protected $table = 'main_source_of_businesses';
    protected $fillable = [
        'jobName',
        'createdBy',
        'modifiedBy'
    ];
}
