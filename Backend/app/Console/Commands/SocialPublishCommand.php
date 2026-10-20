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

        if (empty($webhookUrl)) {
            $this->warn('⚠ No social dispatch webhook configured in Vault (social_webhook_url) or .env (SOCIAL_DISPATCH_WEBHOOK).');
            $this->warn('  Posts remain approved and queued so no content is lost. Configure your webhook in Growth OS -> Social.');
            return self::SUCCESS;
        }

        $dispatched = 0;

        foreach ($plans as $plan) {
            $payload = json_decode((string) $plan->payload, true) ?: [];
            $text = $payload['text'] ?? $plan->title;
            $hashtags = $payload['hashtags'] ?? '';
            $channels = $payload['channels'] ?? ['whatsapp', 'facebook', 'instagram', 'x'];
            $imageUrl = $payload['image_url'] ?? null;
            $videoUrl = $payload['video_url'] ?? null;
            $mediaType = !empty($videoUrl) ? 'video' : (!empty($imageUrl) ? 'image' : 'text');
            $link = $payload['link'] ?? 'https://onlinepuja.live/puja/category/list';

            $this->line("• Dispatching: {$plan->title} to [" . implode(', ', $channels) . "] ({$mediaType})");

            $webhookSuccess = false;
            try {
                $response = Http::timeout(20)->post($webhookUrl, [
                    'id'          => $plan->id,
                    'title'       => $plan->title,
                    'text'        => $text,
                    'hashtags'    => $hashtags,
                    'channels'    => $channels,
                    'media_type'  => $mediaType,
                    'image_url'   => $imageUrl,
                    'video_url'   => $videoUrl,
                    'link'        => $link,
                    'source'      => 'OnlinePuja Varanasi Growth Engine',
                    'timestamp'   => now()->toIso8601String(),
                ]);
                $webhookSuccess = $response->successful();
            } catch (\Throwable $e) {
                Log::warning("Social webhook dispatch error for plan #{$plan->id}: " . $e->getMessage());
                $this->error("  ✗ Dispatch failed: " . $e->getMessage());
            }

            if ($webhookSuccess) {
                // Mark plan as published
                DB::table('content_plans')->where('id', $plan->id)->update([
                    'status'     => 'published',
                    'posted_url' => 'social://' . implode(',', $channels),
                    'updated_at' => now(),
                ]);

                // Track in outcomes table
                if (\Illuminate\Support\Facades\Schema::hasTable('outcomes')) {
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
                }

                $dispatched++;
                $this->info("  ✓ Dispatched and confirmed.");
            } else {
                $this->warn("  ⚠ Webhook did not return 200 OK. Plan #{$plan->id} kept in approved queue for retry.");
            }
        }

        $this->info("Successfully published {$dispatched} social media posts.");

        // Record in brain_run_log for Growth OS dashboard
        if (\Illuminate\Support\Facades\Schema::hasTable('brain_run_log')) {
            DB::table('brain_run_log')->insert([
                'task'         => 'social:publish',
                'category'     => 'social',
                'items_ok'     => $dispatched,
                'items_failed' => $plans->count() - $dispatched,
                'detail'       => json_encode(['dispatched' => $dispatched, 'webhook' => substr($webhookUrl, 0, 30) . '...']),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        return self::SUCCESS;
    }
}
