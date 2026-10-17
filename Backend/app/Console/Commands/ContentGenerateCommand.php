<?php

namespace App\Console\Commands;

use App\Brain\ContentPipeline;
use Illuminate\Console\Command;

class ContentGenerateCommand extends Command
{
    protected $signature = 'content:generate {--limit=2} {--publish : Publish immediately instead of queueing for approval}';

    protected $description = 'AI-draft articles from researched keywords (Critic-gated)';

    public function handle(ContentPipeline $pipeline): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $publish = (bool) $this->option('publish');

        $this->info("Drafting up to {$limit} article(s)" . ($publish ? " with IMMEDIATE PUBLISH..." : "..."));

        $out = $pipeline->run($limit, $publish);

        foreach ($out['results'] as $r) {
            $status = $r['ok'] ? '<info>drafted</info>' : '<comment>'.$r['error'].'</comment>';
            $this->line("• {$r['keyword']} — {$status}");
        }

        $this->info("Drafted: {$out['drafted']} · Failed: {$out['failed']}");

        return self::SUCCESS;
    }
}
