<?php

namespace App\Console\Commands;

use App\Models\B2bLead;
use App\Services\B2bLeadService;
use App\Services\FestivalCampaignEngine;
use Illuminate\Console\Command;

class B2bCampaignDispatchCommand extends Command
{
    protected $signature = 'b2b:campaign-dispatch
        {--city=all : Filter B2B partners by city (e.g. Mumbai, Surat, Delhi, all)}
        {--category=all : Filter by category (travel_agency, astrologer, samagri_store, all)}
        {--dispatch : Actually send out messages (default: preview / dry-run)}
        {--seed : Seed premier B2B directory before running}
        {--channels=whatsapp,email : Comma-separated channels}
        {--limit=50 : Maximum partners to contact}';

    protected $description = 'Orchestrate B2B Partnership Campaigns for Dev Deepawali Varanasi 2026 across Indian Travel Agents & Astrologers';

    public function handle(FestivalCampaignEngine $engine, B2bLeadService $leadService): int
    {
        $city = $this->option('city');
        $category = $this->option('category');
        $dispatch = (bool) $this->option('dispatch');
        $channels = array_filter(array_map('trim', explode(',', (string) $this->option('channels'))));
        $limit = (int) $this->option('limit');

        if ($this->option('seed')) {
            $n = $leadService->seedPremierB2bDirectory();
            $this->info("Seeded {$n} premier B2B partners (Mumbai, Surat, Ahmedabad, Delhi, Kolkata, Varanasi).");
        }

        $query = B2bLead::query();
        if ($city !== 'all') {
            $query->where('city', 'like', "%{$city}%");
        }
        if ($category !== 'all') {
            $query->where('category', $category);
        }

        $totalAvailable = $query->count();
        if ($totalAvailable === 0) {
            $this->warn("No B2B leads found. Running initial directory seeding...");
            $leadService->seedPremierB2bDirectory();
            $leads = B2bLead::take($limit)->get();
        } else {
            $leads = $query->take($limit)->get();
        }

        $this->info("=== B2B Dev Deepawali Varanasi 2026 Campaign Orchestrator ===");
        $this->line("Found {$leads->count()} target B2B partner(s) across India.");

        $this->table(
            ['ID', 'Business Name', 'Category', 'City', 'Phone', 'Rating', 'Partner Code', 'Status'],
            $leads->map(function ($l) {
                return [
                    $l->id,
                    $l->business_name,
                    $l->category,
                    $l->city,
                    $l->phone,
                    $l->google_rating ? "★ {$l->google_rating}" : '—',
                    $l->partner_code,
                    strtoupper($l->status),
                ];
            })
        );

        // Preview sample pitch
        $firstLead = $leads->first();
        if ($firstLead) {
            $samplePitch = $engine->buildB2bDevDeepawaliPitch($firstLead);
            $this->line('');
            $this->info(">>> Sample B2B WhatsApp Pitch for [{$firstLead->business_name}]:");
            $this->comment(strtok($samplePitch['whatsapp_copy'], "\n"));
            $this->line("• Video Reel: " . $samplePitch['video_url']);
            $this->line("• Tracking Booking URL: " . $samplePitch['booking_url']);
        }

        if (!$dispatch) {
            $this->warn('');
            $this->comment("  [DRY-RUN] Pass --dispatch to broadcast across " . implode(', ', $channels));
            return self::SUCCESS;
        }

        $this->line('');
        $this->warn("  [DISPATCHING] Broadcasting B2B partnership proposal across " . implode(', ', $channels) . "...");

        $res = $engine->dispatchB2bCampaign($leads->pluck('id')->toArray(), $channels, ['dry_run' => false]);

        $this->info("  ✓ B2B Campaign dispatched successfully:");
        $this->line("    - Total Targeted: {$res['attempted']}");
        $this->line("    - WhatsApp Sent: {$res['whatsapp']['sent']} / Failed: {$res['whatsapp']['failed']}");
        $this->line("    - Brevo Email Sent: {$res['email']['sent']} / Failed: {$res['email']['failed']}");

        return self::SUCCESS;
    }
}
