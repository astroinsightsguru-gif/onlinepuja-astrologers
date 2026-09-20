<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kundali extends Model
{
    use HasFactory;
    protected $table = 'kundalis';
    protected $fillable = [
        'name',
        'gender',
        'birthDate',
        'birthTime',
        'birthPlace',
        'createdBy',
        'modifiedBy',
        'latitude',
        'longitude',
        'timezone',
        'pdf_type',
        'match_type',
        'pdf_link',
        'forMatch'
    ];
}
