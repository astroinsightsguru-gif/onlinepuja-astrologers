<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contactus extends Model
{
    use HasFactory;
    protected $table = 'contact_us';
    protected $fillable = [
        'contact_email',
        'contact_name',
        'contact_message'
    ];
}
