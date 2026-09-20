<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TravelCountry extends Model
{
    use HasFactory;
    protected $table = 'travel_countries';
    protected $fillable = [
        'NoOfCountriesTravell',
        'createdBy',
        'modifiedBy'
    ];
}
