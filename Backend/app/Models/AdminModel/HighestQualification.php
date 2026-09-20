<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HighestQualification extends Model
{
    use HasFactory;
    protected $table = 'highest_qualifications';
    protected $fillable = [
        'qualificationName',
        'createdBy',
        'modifiedBy'
    ];
}
