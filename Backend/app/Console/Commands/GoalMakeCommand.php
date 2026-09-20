<?php

namespace App\Console\Commands;

use App\Brain\GoalPlanner;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GoalMakeCommand extends Command
{
    protected $signature = 'goal:make
        {target : Total target metric (e.g. 100000 pageviews)}
        {--days=50 : Horizon in days}
        {--baseline=0 : Current avg daily traffic}
        {--title= : Goal title}';

    protected $description = 'Create a growth goal and print the honest arithmetic';

    public function handle(): int
    {
        $target = (int) $this->argument('target');
        $days = max(1, (int) $this->option('days'));
        $baseline = max(0, (int) $this->option('baseline'));

        $arithmetic = GoalPlanner::plan([
            'title' => $this->option('title') ?: "Reach {$target} in {$days} days",
            'target' => $target,
            'days' => $days,
            'baseline_daily' => $baseline,
        ]);

        $title = $this->option('title') ?: "Reach {$target} in {$days} days";

        $id = DB::table('goals')->insertGetId([
            'title' => $title,
            'metric' => 'pageviews',
            'target' => $target,
            'start_date' => now()->toDateString(),
            'deadline' => now()->addDays($days)->toDateString(),
            'baseline_daily' => $baseline,
            'status' => 'active',
            'plan_arithmetic' => json_encode($arithmetic),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("GOAL #$id CREATED — {$arithmetic['verdict']}");

        $this->table(['Metric', 'Value'], [
            ['Target', number_format($target)],
            ['Horizon', $days.' days'],
            ['Baseline daily', number_format($baseline)],
            ['Gap', number_format($arithmetic['gap'])],
            ['Required avg daily', number_format($arithmetic['required_avg_daily'])],
            ['Content posts needed', $arithmetic['implied']['content_posts_needed'].' ('.$arithmetic['implied']['content_posts_per_day'].'/day)'],
            ['Social posts needed', $arithmetic['implied']['social_posts_needed'].' ('.$arithmetic['implied']['social_posts_per_day'].'/day)'],
        ]);

        if ($arithmetic['levers']) {
            $this->warn('LEVERS:');
            foreach ($arithmetic['levers'] as $l) {
                $this->line(' • '.$l);
            }
        }

        return self::SUCCESS;
    }
}
