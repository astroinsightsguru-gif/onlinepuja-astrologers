<?php

namespace App\Console\Commands;

use App\AI\ProviderManager;
use Illuminate\Console\Command;

class AiSyncModelsCommand extends Command
{
    protected $signature = 'ai:sync-models';

    protected $description = 'Pull live model catalogues from every configured AI provider';

    public function handle(ProviderManager $manager): int
    {
        $report = $manager->syncModels();

        foreach ($report['providers'] as $slug => $count) {
            $this->line("$slug: $count models");
        }

        foreach ($report['errors'] as $slug => $err) {
            $this->warn("$slug ERROR: $err");
        }

        $this->info('TOTAL synced: '.$report['total']);

        return self::SUCCESS;
    }
}
