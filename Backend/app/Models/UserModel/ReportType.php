<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    use HasFactory;
    protected $table = 'report_types';
    protected $fillable = [
        'reportImage',
        'title',
        'description',
    ];
}
