<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DegreeOrDiploma extends Model
{
    use HasFactory;
    protected $table = 'degree_or_diplomas';
    protected $fillable = [
        'degreeName',
        'createdBy',
        'modifiedBy'
    ];
}
