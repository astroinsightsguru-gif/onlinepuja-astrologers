<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use HasFactory;
    protected $table = 'teammember';

    protected $fillable = [
        'name',
        'contactNo',
        'email',
        'password',
        'profile',
        'userId',
        'teamRoleId'
    ];
}