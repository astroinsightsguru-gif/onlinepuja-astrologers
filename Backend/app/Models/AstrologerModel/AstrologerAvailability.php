<?php

namespace App\Models\AstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AstrologerAvailability extends Model
{
    use HasFactory;
    protected $table = 'astrologer_availabilities';
    protected $fillable = [
        'astrologerId',
        'fromTime',
        'toTime',
        'day',
        'createdBy',
        'modifiedBy'
    ];
}
