<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;
    protected $table = 'tickets';
    protected $fillable = [
        'helpSupportId',
        'subject',
        'description',
        'ticketNumber',
        'userId',
        'createdBy',
        'modifiedBy',
        'sender_type',
        'ticketStatus',
        'chatId'
    ];
}
