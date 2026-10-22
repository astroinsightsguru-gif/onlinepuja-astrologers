<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiAgent extends Model
{
    protected $table = 'ai_agents';

    protected $fillable = [
        'agent_key',
        'name',
        'icon',
        'role_description',
        'system_prompt',
        'language',
        'tone',
        'model_preference',
        'cron_cadence',
        'mode',
        'is_active',
        'last_run_at',
        'last_status',
        'extra_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra_config' => 'array',
        'last_run_at' => 'datetime',
    ];

    public function outputs()
    {
        return $this->hasMany(AiAgentOutput::class, 'agent_id')->orderByDesc('id');
    }
}
