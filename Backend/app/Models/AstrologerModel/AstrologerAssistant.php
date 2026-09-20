<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerAssistant extends Model
{
    use HasFactory;
    protected $table = 'astrologer_assistants';
    protected $fillable = [
        'astrologerId',
        'name',
        'email',
        'contactNo',
        'gender',
        'birthdate',
        'primarySkill',
        'allSkill',
        'languageKnown',
        'experienceInYears',
        'profile',
        'createdBy',
        'modifiedBy',
    ];
}
