<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horoscope extends Model
{
    use HasFactory;
    protected $fillable = [
        'zodiac',
        'total_score',
        'lucky_color',
        'lucky_color_code',
        'lucky_number',
        'physique',
        'status',
        'finances',
        'relationship',
        'career',
        'travel',
        'family',
        'friends',
        'health',
        'bot_response',
        'date',
        'type',
        'start_date',
        'end_date', 
        'month_range', 
        'health_remark',
        'career_remark',
        'relationship_remark',
        'travel_remark',
        'family_remark',
        'friends_remark',
        'finances_remark',
        'status_remark',
        'langcode'
    ];
}
