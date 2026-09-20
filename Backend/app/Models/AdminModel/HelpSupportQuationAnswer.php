<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpSupportQuationAnswer extends Model
{
    use HasFactory;

    protected $table = 'help_support_quation_answers';

    protected $fillable = [
        'helpSupportQuationId',
        'title',
        'helpSupportId',
        'description',
        'isChatWithus',
        'isActive',
        'isDeleted',
        'createdBy',
        'modifiedBy',
    ];

    protected $casts = [
        'id' => 'integer',
        'helpSupportQuationId' => 'integer',
        'helpSupportId' => 'integer',
        'isChatWithus' => 'integer',
        'isActive' => 'integer',
        'isDeleted' => 'integer',
        'createdBy' => 'integer',
        'modifiedBy' => 'integer',
    ];
}
