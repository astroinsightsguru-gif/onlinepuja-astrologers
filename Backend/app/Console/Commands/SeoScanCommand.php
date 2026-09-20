<?php

namespace App\Console\Commands;

use App\Brain\SiteAuditor;
use Illuminate\Console\Command;

class SeoScanCommand extends Command
{
    protected $signature = 'seo:scan {--max=40}';

    protected $description = 'Audit all sitemap URLs for on-page/technical SEO issues';

    public function handle(SiteAuditor $auditor): int
    {
        $this->info('Scanning sitemap URLs…');

        $out = $auditor->scan(max(5, (int) $this->option('max')));

        $this->info("Pages scanned: {$out['pages_scanned']} · Issues found: {$out['issues']}");

        $top = \DB::table('seo_issues')->selectRaw('type, COUNT(*) c')
            ->where('status', 'open')->groupBy('type')->orderByDesc('c')->limit(8)->get();

        foreach ($top as $t) {
            $this->line(sprintf('  %-28s %d open', $t->type, $t->c));
        }

        return self::SUCCESS;
    }
}
