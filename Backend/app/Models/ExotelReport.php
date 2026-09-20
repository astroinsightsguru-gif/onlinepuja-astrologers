<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExotelReport extends Model
{
    use HasFactory;

    protected $table = 'exotel_reports';
    protected $fillable = [
        'userId',
        'astrologerId',
        'sid',
        'call_from',
        'call_to',
        'start_time',
        'end_time',
        'callerId',
        'duration',
        'status',
        'recording_url',
        'status_url',
        'full_report'
    ];
}
