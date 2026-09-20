<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpSupportQuation extends Model
{
    use HasFactory;

    protected $table = 'help_support_quations';

    protected $fillable = [
        'helpSupportId',
        'question',
        'answer',
        'createdBy',
        'modifiedBy',
        'isChatWithus'
    ];

    protected $casts = [
        'id' => 'integer',
        'helpSupportId' => 'integer',
    ];
}
