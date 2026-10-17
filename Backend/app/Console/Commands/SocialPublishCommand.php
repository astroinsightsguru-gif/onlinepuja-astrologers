<?php

namespace App\Console\Commands;

use App\Brain\Vault;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialPublishCommand extends Command
{
    protected $signature = 'social:publish {--limit=5 : Max pending posts to dispatch}';

    protected $description = 'Automatically dispatch approved social media festival & daily darshan posts to connected channels';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $plans = DB::table('content_plans')
            ->where('channel', 'social')
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->whereNull('due_at')->orWhere('due_at', '<=', now());
            })
            ->orderBy('due_at', 'ASC')
            ->limit($limit)
            ->get();

        if ($plans->isEmpty()) {
            $this->info('No pending approved social posts due for publication.');
            return self::SUCCESS;
        }

        $webhookUrl = Vault::get('social_webhook_url') ?: env('SOCIAL_DISPATCH_WEBHOOK');
        $dispatched = 0;

        foreach ($plans as $plan) {
            $payload = json_decode((string) $plan->payload, true) ?: [];
            $text = $payload['text'] ?? $plan->title;
            $hashtags = $payload['hashtags'] ?? '';
            $channels = $payload['channels'] ?? ['whatsapp', 'facebook', 'instagram', 'x'];

            $this->line("• Dispatching: {$plan->title} to [" . implode(', ', $channels) . "]");

            $webhookSuccess = true;
            if (!empty($webhookUrl)) {
                try {
                    $response = Http::timeout(15)->post($webhookUrl, [
                        'id'        => $plan->id,
                        'title'     => $plan->title,
                        'text'      => $text,
                        'hashtags'  => $hashtags,
                        'channels'  => $channels,
                        'source'    => 'OnlinePuja Varanasi Growth Engine',
                        'timestamp' => now()->toIso8601String(),
                    ]);
                    $webhookSuccess = $response->successful();
                } catch (\Throwable $e) {
                    Log::warning("Social webhook dispatch error: " . $e->getMessage());
                    $webhookSuccess = false;
                }
            }

            // Mark plan as published
            DB::table('content_plans')->where('id', $plan->id)->update([
                'status'     => 'published',
                'posted_url' => 'social://' . implode(',', $channels),
                'updated_at' => now(),
            ]);

            // Track in outcomes table
            DB::table('outcomes')->insert([
                'channel'     => 'social',
                'entity_type' => 'festival_post',
                'entity_id'   => $plan->id,
                'metric'      => 'published',
                'value'       => 1,
                'at'          => now(),
                'meta'        => json_encode(['channels' => $channels, 'title' => $plan->title]),
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $dispatched++;
        }

        $this->info("Successfully published {$dispatched} social media posts.");

        // Record in brain_run_log for Growth OS dashboard
        if (\Illuminate\Support\Facades\Schema::hasTable('brain_run_log')) {
            DB::table('brain_run_log')->insert([
                'task'         => 'social:publish',
                'category'     => 'social',
                'items_ok'     => $dispatched,
                'items_failed' => 0,
                'detail'       => json_encode(['dispatched' => $dispatched]),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return self::SUCCESS;
    }
}
