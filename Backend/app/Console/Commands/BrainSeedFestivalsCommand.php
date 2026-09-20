<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BrainSeedFestivalsCommand extends Command
{
    protected $signature = 'brain:seed-festivals';

    protected $description = 'Seed major Hindu festivals (2026–2027) for automated wish campaigns';

    // Dates are editable in admin/DB — regional variations exist.
    protected const FESTIVALS = [
        ['2026-09-04', 'Janmashtami', 'Krishna'],
        ['2026-09-14', 'Ganesh Chaturthi', 'Ganesha'],
        ['2026-09-21', 'Pitru Paksha Begins', 'Ancestors'],
        ['2026-10-11', 'Navratri Begins', 'Durga'],
        ['2026-10-17', 'Durga Ashtami', 'Durga'],
        ['2026-10-20', 'Dussehra (Vijayadashami)', 'Durga'],
        ['2026-10-29', 'Karwa Chauth', 'Shiva-Parvati'],
        ['2026-11-06', 'Dhanteras', 'Lakshmi-Kubera'],
        ['2026-11-08', 'Diwali', 'Lakshmi-Ganesha'],
        ['2026-11-09', 'Govardhan Puja', 'Krishna'],
        ['2026-11-10', 'Bhai Dooj', 'Yamuna'],
        ['2026-12-25', 'Tulsi Vivah Ends / Purnima', 'Vishnu'],
        ['2027-01-14', 'Makar Sankranti / Pongal', 'Surya'],
        ['2027-01-23', 'Vasant Panchami', 'Saraswati'],
        ['2027-02-20', 'Maha Shivratri', 'Shiva'],
        ['2027-03-22', 'Holika Dahan', 'Prahlada-Vishnu'],
        ['2027-03-23', 'Holi', 'Krishna-Radha'],
        ['2027-04-16', 'Ram Navami', 'Rama'],
    ];

    public function handle(): int
    {
        $existing = DB::table('hindu_festivals')->count();
        $inserted = 0;

        foreach (self::FESTIVALS as [$date, $name, $deity]) {
            $exists = DB::table('hindu_festivals')->whereDate('date', $date)->where('name', $name)->exists();

            if ($exists) {
                continue;
            }

            DB::table('hindu_festivals')->insert([
                'name' => $name,
                'date' => $date,
                'deity' => $deity,
                'channels' => json_encode(['facebook', 'instagram', 'x']),
                'scheduled' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
        }

        $this->info("Seeded {$inserted} new festivals ({$existing} already present, ".count(self::FESTIVALS).' total expected).');

        return self::SUCCESS;
    }
}
