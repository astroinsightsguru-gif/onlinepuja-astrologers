<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KundaliMatching extends Model
{
    use HasFactory;
    protected $table = 'kundali_matchings';
    protected $fillable = [
        'boyName',
        'boyBirthDate',
        'boyBirthTime',
        'boyBirthPlace',
        'girlName',
        'girlBirthDate',
        'girlBirthTime',
        'girlBirthPlace',
        'createdBy',
        'modifiedBy',
    ];
}
