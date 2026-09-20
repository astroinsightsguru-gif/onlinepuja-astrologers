<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FulltimeJob extends Model
{
    use HasFactory;
    protected $table = 'fulltime_jobs';
    protected $fillable = [
        'workName',
        'createdBy',
        'modifiedBy'
    ];
}
