<?php

namespace App\Console\Commands;

use App\Brain\KeywordResearch;
use Illuminate\Console\Command;

class SeoResearchCommand extends Command
{
    protected $signature = 'seo:research';

    protected $description = 'Tri-source keyword research: Google autocomplete + AI expansion (+GSC later)';

    public function handle(KeywordResearch $research): int
    {
        $this->info('Running tri-source keyword research…');

        $report = $research->run();

        foreach ($report as $source => $count) {
            $this->line(sprintf('%-14s %d new phrases', $source, $count));
        }

        $total = \DB::table('keywords')->count();
        $easy = \DB::table('keywords')->where('difficulty', '<=', 45)->count();

        $this->info("Catalogue now: {$total} keywords ({$easy} easy-win candidates)");

        return self::SUCCESS;
    }
}
