<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SeedFestivePujasCommand extends Command
{
    protected $signature = 'puja:seed-festive';

    protected $description = 'Seed authentic seasonal Diwali, Dev Deepawali, and Varanasi Holy Sanctum Pujas with packages';

    public function handle(): int
    {
        if (!Schema::hasTable('pujas')) {
            $this->error('Table "pujas" does not exist.');
            return self::FAILURE;
        }

        $pujas = [
            [
                'title' => 'Varanasi Dev Deepawali 108 Deepdaan & Ganga Maha-Aarti Sankalp',
                'subtitle' => 'Sacred 108 clay deepams offered in your Gotra on Dashashwamedh Ghat with Prasad courier to your home',
                'place' => 'Dashashwamedh & Assi Ghat, Kashi (Varanasi)',
                'price' => 1500,
                'slug' => 'varanasi-dev-deepawali-108-deepdaan-sankalp-2026',
                'start' => '2026-11-23 17:30:00',
                'end' => '2026-11-23 21:30:00',
                'benefits' => [
                    'Destroys deep ancestral karmic blocks and brings pitru ashirwad',
                    'Dev Deepawali in Kashi is celebrated by devatas on the 84 ghats',
                    'Personalized Sankalp chanting with Devotee Name and Gotra',
                    'High-definition video recording of your 108 diyas being offered',
                    'Sacred Ganga Jal, Kashi Chandan, and dry prasad dispatched to your doorstep'
                ],
                'description' => 'On Kartik Purnima (Dev Deepawali), the devatas descend upon the sacred ghats of Kashi. Experience the supreme spiritual merit of offering 108 glowing earthen deepams on the sacred river Ganga in your family Gotra, performed by authentic Vedic pandits of Varanasi.',
                'packages' => [
                    ['name' => 'Single Devotee 108 Deepdaan', 'price' => 1500, 'desc' => '108 Diyas + Gotra Sankalp + Video + Prasad Dispatch'],
                    ['name' => 'Dampati / Couple Sanctum', 'price' => 2500, 'desc' => '216 Diyas + Husband & Wife Sankalp + Ganga Aarti + Prasad Box'],
                    ['name' => 'Sampurna Family Gotra Sanctum', 'price' => 5100, 'desc' => '501 Diyas + Full Family Sankalp + Special Brahmin Bhojan + Silver Coin Prasad']
                ]
            ],
            [
                'title' => 'Diwali Maha Lakshmi & Kuber Yagya at Business/Home',
                'subtitle' => 'Complete Vedic 2-hour Lakshmi-Ganesh-Saraswati Havan & Shubh Labh sanctification',
                'place' => 'Vedic Mandap / At Devotee Premises & Live Stream',
                'price' => 2100,
                'slug' => 'diwali-maha-lakshmi-kuber-yagya-2026',
                'start' => '2026-11-08 18:00:00',
                'end' => '2026-11-08 22:00:00',
                'benefits' => [
                    'Invokes eternal prosperity, cash-flow stability, and business growth',
                    'Ashtalakshmi Stotram and Shri Suktam 16x Samput havan',
                    'Kuber Yantra and Bahi-Khata / Ledger book sanctification',
                    'Conducted by certified Shastriji with pure cow ghee & authentic samagri'
                ],
                'description' => 'Deepawali is the supreme night for welcoming Goddess Maha Lakshmi and Lord Kuber. Whether performed at your office, factory, or home premises, or through live digital Sankalp at Kashi, this authentic Vedic havan removes financial stagnation and ushers in abundance for the new financial year.',
                'packages' => [
                    ['name' => 'E-Puja Live Sankalp', 'price' => 2100, 'desc' => 'Live digital havan + Gotra chanting + Energized Kuber Yantra mailed'],
                    ['name' => 'At-Home / Shop Verified Pandit', 'price' => 5100, 'desc' => 'Certified Pandit Ji visits premises with samagri checklist'],
                    ['name' => 'Maha Vyapar Sampanna Yagya', 'price' => 11000, 'desc' => '2 Acharyas, 1008 Ahutis, Shri Suktam + Complete Samagri Included']
                ]
            ],
            [
                'title' => 'Karwa Chauth Akhand Saubhagya Vrat Katha & Puja',
                'subtitle' => '45-minute live guided Vrat Katha, Thali rotation & Shiva-Parvati blessings',
                'place' => 'Holy Sanctum / Live Online Interactive Session',
                'price' => 851,
                'slug' => 'karwa-chauth-akhand-saubhagya-puja-2026',
                'start' => '2026-10-29 16:30:00',
                'end' => '2026-10-29 18:30:00',
                'benefits' => [
                    'Blesses long life, health, and harmony for husband and marital bliss',
                    'Authentic Karwa Chauth Vrat Katha recitation by scholarly Shastri',
                    'Guided step-by-step Karwa exchange and evening Arghya vidhi',
                    'Conducted in morning and evening batches for family convenience'
                ],
                'description' => 'Karwa Chauth celebrates the steadfast love and devotion of married women. Join our verified Vedic pandits for an authentic, warm, and uplifting Vrat Katha and Shiva-Parvati puja ceremony with your friends, family, or society group.',
                'packages' => [
                    ['name' => 'Individual Devotee Katha', 'price' => 851, 'desc' => 'Live interactive session + digital sankalp certificate'],
                    ['name' => 'Society / Group Mandali Katha', 'price' => 3100, 'desc' => 'Up to 15 women participating together with Pandit Ji on large screen']
                ]
            ],
            [
                'title' => 'Shri Kashi Vishwanath Special Somwar Rudrabhishek',
                'subtitle' => 'Sacred Panchamrit abhishek of the Jyotirlingam with 108 Bilva leaves and Mahamrityunjaya chants',
                'place' => 'Shri Kashi Vishwanath Dham, Varanasi',
                'price' => 2100,
                'slug' => 'shri-kashi-vishwanath-somwar-rudrabhishek',
                'start' => '2026-10-19 07:00:00',
                'end' => '2026-10-19 09:30:00',
                'benefits' => [
                    'Destroys all planetary afflictions, health crises, and Kaal Sarp dosha',
                    'Direct sacred touch at Kashi Vishwanath Dham sanctum',
                    '108 Bilvapatra offered with devotee family Gotra names recited aloud',
                    'Holy Gangajal and Bhasma prasad dispatched to devotee residence'
                ],
                'description' => 'Performing Rudrabhishek at Kashi Vishwanath on Somwar is the highest Vedic ritual in Sanatan Dharma. The powerful vibration of the Rudram shlokas liberates devotees from chronic difficulties, anxiety, and delays in marriage or career.',
                'packages' => [
                    ['name' => 'Gotra Abhishek Sankalp', 'price' => 2100, 'desc' => 'Pandit chanting in your name + Video + Prasad'],
                    ['name' => 'Laghu Rudra Maha Abhishek', 'price' => 7500, 'desc' => '5 Vedic Pandits, 121 Rudra avartans + Full Prasad Box']
                ]
            ]
        ];

        $seededCount = 0;

        foreach ($pujas as $item) {
            $exists = DB::table('pujas')->where('slug', $item['slug'])->exists();
            if ($exists) {
                $this->line("• Already exists: {$item['title']}");
                continue;
            }

            // Create or find packages
            $packageIds = [];
            if (Schema::hasTable('pujapackages')) {
                foreach ($item['packages'] as $pkg) {
                    $pkgId = DB::table('pujapackages')->insertGetId([
                        'package_name' => $pkg['name'],
                        'package_price' => $pkg['price'],
                        'package_description' => $pkg['desc'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $packageIds[] = (string)$pkgId;
                }
            }

            DB::table('pujas')->insert([
                'category_id' => 1,
                'puja_title' => $item['title'],
                'puja_subtitle' => $item['subtitle'],
                'puja_place' => $item['place'],
                'long_description' => $item['description'],
                'puja_benefits' => json_encode($item['benefits']),
                'puja_images' => json_encode(['https://images.unsplash.com/photo-1544816155-12df9643f363?auto=format&fit=crop&w=800&q=80']),
                'package_id' => json_encode($packageIds),
                'puja_start_datetime' => $item['start'],
                'puja_end_datetime' => $item['end'],
                'slug' => $item['slug'],
                'created_by' => 'admin',
                'puja_price' => $item['price'],
                'isAdminApproved' => 1,
                'puja_duration' => '2 Hours',
                'isPujaEnded' => 0,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $this->info("✓ Seeded: {$item['title']}");
            $seededCount++;
        }

        $this->info("Successfully seeded {$seededCount} seasonal & Varanasi holy pujas.");
        return self::SUCCESS;
    }
}
