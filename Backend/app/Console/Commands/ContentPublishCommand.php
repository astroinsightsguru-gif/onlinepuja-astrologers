<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ContentPublishCommand extends Command
{
    protected $signature = 'content:publish {id : content_plans id}';

    protected $description = 'Publish an approved draft into the live blogs table';

    public function handle(): int
    {
        $plan = DB::table('content_plans')->where('id', $this->argument('id'))->first();

        if (! $plan || ! in_array($plan->status, ['drafted', 'approved'])) {
            $this->error('Plan not found or not in a publishable state.');

            return self::FAILURE;
        }

        $payload = json_decode((string) $plan->payload, true);
        $slug = trim((string) ($payload['slug'] ?? ''));
        $html = (string) ($payload['html'] ?? '');

        if ($slug === '' || $html === '') {
            $this->error('Payload missing slug/html.');

            return self::FAILURE;
        }

        // Discover real blogs columns at runtime — no guessing.
        $cols = collect(DB::select('SHOW COLUMNS FROM blogs'))->pluck('Field')->flip();
        $row = [];

        $map = [
            'title' => $plan->title,
            'slug' => $slug,
            'meta_description' => $payload['meta_description'] ?? null,
            'status' => 'published',
            'user_id' => 1,
        ];

        // Find the main content column.
        $contentCol = ['content', 'description', 'detail', 'body', 'post_content']
            ->first(fn ($c) => isset($cols[$c]));

        if (! $contentCol) {
            $this->error('No content-like column found in blogs table.');

            return self::FAILURE;
        }

        $row[$contentCol] = $html;

        foreach ($map as $col => $val) {
            if ($val !== null && isset($cols[$col])) {
                $row[$col] = $val;
            }
        }

        $row['created_at'] = $row['updated_at'] = now();

        try {
            DB::table('blogs')->insert($row);
        } catch (\Throwable $e) {
            $this->error('Insert failed: '.$e->getMessage());

            return self::FAILURE;
        }

        DB::table('content_plans')->where('id', $plan->id)->update([
            'status' => 'published',
            'posted_url' => url('blog/'.$slug),
            'updated_at' => now(),
        ]);

        $url = url('blog/'.$slug);

        DB::table('outcomes')->insert([
            'channel' => 'web', 'entity_type' => 'blog', 'entity_id' => $plan->id,
            'metric' => 'published', 'value' => 1, 'at' => now(),
            'meta' => json_encode(['url' => $url]),
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->info("PUBLISHED: {$url}");

        return self::SUCCESS;
    }
}
