<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAgentOutput extends Model
{
    protected $table = 'ai_agent_outputs';

    protected $fillable = [
        'agent_id',
        'title',
        'content_payload',
        'media_type',
        'media_url',
        'status',
        'published_at',
        'distribution_channels',
    ];

    protected $casts = [
        'distribution_channels' => 'array',
        'published_at' => 'datetime',
    ];

    public function agent()
    {
        return $this->belongsTo(AiAgent::class, 'agent_id');
    }
}
