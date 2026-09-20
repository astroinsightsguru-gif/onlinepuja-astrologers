<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallRequestApoinment extends Model
{
    use HasFactory;

    // Table name
    protected $table = 'call_request_apoinments';

    // Primary key
    protected $primaryKey = 'id';

    // Mass assignable fields
    protected $fillable = [
        'callId',
        'astrologerId',
        'userId',
        'amount',
        'call_duration',
        'call_method',
        'status',
        'IsActive',
        'created_at',
        'updated_at',
    ];

    // Dates to be treated as Carbon instances
    protected $dates = ['created_at', 'updated_at'];

    // Default values
    protected $attributes = [
        'IsActive' => 1,
        'status'   => 'Pending',
    ];

    /**
     * Relationships
     */

    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    // Relation with Astrologer
    public function astrologer()
    {
        return $this->belongsTo(Astrologer::class, 'astrologerId');
    }

    // Relation with CallRequest
    public function callRequest()
    {
        return $this->belongsTo(CallRequest::class, 'callId');
    }
}
