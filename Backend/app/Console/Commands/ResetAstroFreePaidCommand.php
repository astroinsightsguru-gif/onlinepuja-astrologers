<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetAstroFreePaidCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:astro-free-paid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            DB::table('astrologers')->update([
                'AstroFreePaid' => 0,
                'updated_at' => Carbon::now(),
            ]);

            Log::info('All astrologers\' AstroFreePaid counts have been reset to 0 successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
