<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpSupport extends Model
{
    use HasFactory;
    protected $table = 'help_supports';
    protected $fillable = [
        'name',
        'createdBy',
        'modifiedBy'
    ];
}
