<?php

namespace App\Services;

use App\Brain\RunLog;
use App\Brain\Vault;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Autonomous Multi-Channel Festival Campaign Engine
 *
 * Orchestrates world-class marketing campaigns for major upcoming Indian festivals
 * (e.g. Dev Deepawali Varanasi 2026, Chhath Puja, Dhanteras, Diwali, Maha Shivratri) across:
 *   1. Evolution WhatsApp (1080x1920 MP4 Video Reels, rich Sanskrit copy, direct booking CTAs)
 *   2. Brevo Email Newsletter (Responsive devotional HTML, personal sankalp invitations)
 *   3. OneSignal / Web Push Notifications
 *   4. Programmatic Content & Blog Pipeline Integration
 */
class FestivalCampaignEngine
{
    protected EvolutionWaService $wa;
    protected BrevoEmailService $brevo;
    protected OneSignalService $push;
    protected AiEngineService $ai;

    public function __construct(
        EvolutionWaService $wa,
        BrevoEmailService $brevo,
        OneSignalService $push,
        AiEngineService $ai
    ) {
        $this->wa = $wa;
        $this->brevo = $brevo;
        $this->push = $push;
        $this->ai = $ai;
    }

    /**
     * Scan upcoming Hindu festivals within given horizon (in days)
     * and determine campaign readiness and milestone stage.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getUpcomingFestivals(int $daysHorizon = 60): array
    {
        $today = Carbon::today('Asia/Kolkata');
        $endDate = $today->copy()->addDays($daysHorizon);

        $festivals = DB::table('hindu_festivals')
            ->whereDate('date', '>=', $today->toDateString())
            ->whereDate('date', '<=', $endDate->toDateString())
            ->orderBy('date', 'asc')
            ->get();

        $results = [];

        foreach ($festivals as $f) {
            $fDate = Carbon::parse($f->date, 'Asia/Kolkata')->startOfDay();
            $diffDays = $today->diffInDays($fDate, false);

            $milestone = match (true) {
                $diffDays < 0 => 'passed',
                $diffDays === 0 => 'day_0',
                $diffDays === 1 => 't_minus_1',
                $diffDays <= 3 => 't_minus_3',
                $diffDays <= 7 => 't_minus_7',
                $diffDays <= 14 => 't_minus_14',
                default => 'upcoming_radar',
            };

            // Locate matching live or seeded puja
            $nameTokens = explode(' ', trim(preg_replace('/\s*\(.*?\)\s*/', ' ', $f->name)));
            $searchTerm = count($nameTokens) >= 2 ? ($nameTokens[0] . ' ' . $nameTokens[1]) : $nameTokens[0];

            $puja = DB::table('pujas')
                ->where(function ($q) use ($f, $searchTerm) {
                    $q->where('puja_title', 'like', "%{$searchTerm}%")
                      ->orWhere('slug', 'like', '%' . Str::slug($searchTerm) . '%')
                      ->orWhere('long_description', 'like', "%{$searchTerm}%");
                })
                ->select(['id', 'puja_title', 'slug', 'puja_price', 'puja_place', 'puja_status'])
                ->first();

            $results[] = [
                'id'         => $f->id,
                'name'       => $f->name,
                'date'       => $f->date,
                'formatted_date' => $fDate->format('D, d M Y'),
                'deity'      => $f->deity,
                'days_left'  => $diffDays,
                'milestone'  => $milestone,
                'puja'       => $puja ? [
                    'id'     => $puja->id,
                    'title'  => $puja->puja_title,
                    'slug'   => $puja->slug,
                    'place'  => $puja->puja_place,
                    'url'    => 'https://onlinepuja.live/pujas/' . $puja->slug,
                    'status' => $puja->puja_status == 1 ? 'Live' : 'Draft',
                ] : null,
                'channels'   => json_decode($f->channels ?? '["whatsapp","email","push"]', true) ?: ['whatsapp', 'email', 'push'],
            ];
        }

        return $results;
    }

    /**
     * Build rich, high-converting devotional copy and visual briefs tailored to the festival.
     */
    public function buildCampaignContent(string|object $festivalInput, ?string $explicitDate = null): array
    {
        if (is_object($festivalInput)) {
            $name = $festivalInput->name;
            $dateStr = $festivalInput->date;
        } else {
            $name = (string) $festivalInput;
            $festRow = DB::table('hindu_festivals')->where('name', 'like', "%{$name}%")->first();
            $dateStr = $explicitDate ?: ($festRow->date ?? now()->addDays(14)->toDateString());
        }
        $dateFormatted = Carbon::parse($dateStr)->format('l, d F Y');

        $isVaranasiDevDeepawali = str_contains(strtolower($name), 'dev deepawali') || str_contains(strtolower($name), 'dev diwali');
        $isChhath = str_contains(strtolower($name), 'chhath');
        $isDiwali = str_contains(strtolower($name), 'diwali') && !$isVaranasiDevDeepawali;

        if ($isVaranasiDevDeepawali) {
            return $this->buildDevDeepawaliPackage($dateFormatted);
        } elseif ($isChhath) {
            return $this->buildChhathPackage($dateFormatted);
        } else {
            return $this->buildGenericFestivalPackage($name, $dateFormatted);
        }
    }

    /**
     * Dev Deepawali Varanasi 2026 signature multi-channel package.
     */
    protected function buildDevDeepawaliPackage(string $dateFormatted): array
    {
        $pujaUrl = 'https://onlinepuja.live/pujas/dev-deepawali-maha-deepdaan-kashi-vishwanath-sankalp';

        $shloka = "त्रिपुरस्य वधे जाता देवानामपि हर्षिताः।\nदीपदानेन मुच्यन्ते सर्वपापैर्न संशयः॥";

        // 1. WhatsApp Copy
        $whatsappCopy = "🪔 *DEV DEEPAWALI VARANASI 2026: MAHA DEEPDAAN SANKALP* 🪔\n\n"
            . "_{$shloka}_\n\n"
            . "Hari Om! On the sacred night of *Kartik Purnima ({$dateFormatted})*, the 84 holy Ghats of Varanasi illuminate with over 10 Lakh earthen diyas as the 33 Koti Gods descend to celebrate Mahadev's victory over Tripurasura.\n\n"
            . "✨ *Why Devotees Offer Deepdaan at Kashi:*\n"
            . "• Deepdaan at Dashashwamedh & Manikarnika Ghats dispels karmic obstacles & Pitru dosha.\n"
            . "• Verified Kashi Purohits perform the Sankalp with *Your Family Name & Gotra*.\n"
            . "• 108 Sacred Diya lamps are released directly onto the holy Ganga in your name.\n"
            . "• Receive personalized HD video reel of your ritual on WhatsApp.\n"
            . "• Sanctified Kashi Vishwanath Mahaprasad & Ganga Jal couriered to your doorstep.\n\n"
            . "🎟️ *Dakshina Packages:* Starting from ₹501 (Individual) to ₹2,100 (Joint Family with Bhog).\n"
            . "⏳ *Limited Sankalp Slots:* Kashi Ghat permits are limited to maintain Vedic purity.\n\n"
            . "👉 *Book Your Sacred Sankalp Online:*\n"
            . "{$pujaUrl}\n\n"
            . "📞 *Pandit Helpline:* +91 70071 58014\n"
            . "_Har Har Mahadev · OnlinePuja.live Varanasi Sewa_";

        // 2. Brevo Responsive Email HTML
        $emailSubject = "🪔 Join Dev Deepawali Maha Deepdaan on 84 Varanasi Ghats ({$dateFormatted})";
        $emailBody = "
            <div style='text-align: center; margin-bottom: 24px;'>
                <span style='background: #fef3c7; color: #b45309; font-size: 13px; font-weight: bold; padding: 6px 14px; border-radius: 9999px; text-transform: uppercase;'>
                    Sacred Kartik Purnima · {$dateFormatted}
                </span>
                <p style='font-style: italic; color: #991b1b; font-size: 16px; margin: 16px 0 8px; font-weight: 600;'>
                    “{$shloka}”
                </p>
                <p style='color: #475569; font-size: 13px; margin: 0;'>— Skanda Purana, Kashi Khanda</p>
            </div>

            <p>Pranam <strong>{{ devotee_name }}</strong>,</p>

            <p>On the auspicious full moon night of <strong>Kartik Purnima</strong>, the celestial Gods descend to Earth to bathe in the holy Ganges and celebrate <strong>Dev Deepawali in Kashi (Varanasi)</strong>. Over 10 Lakh clay lamps illuminate the crescent banks of the 84 sacred ghats in an ocean of golden divine light.</p>

            <div style='background: #fffbeb; border: 1px solid #fde68a; border-radius: 12px; padding: 20px; margin: 24px 0;'>
                <h3 style='margin: 0 0 12px; color: #991b1b; font-size: 17px;'>🌺 What Is Included in Your Sacred Sankalp:</h3>
                <ul style='margin: 0; padding-left: 20px; color: #334155; line-height: 1.8;'>
                    <li><strong>Individual Gotra Sankalp:</strong> Verified Kashi Vedic Purohits chant your family Gotra and names on Manikarnika & Dashashwamedh Ghats.</li>
                    <li><strong>108 Floating Earthen Lamps:</strong> Lit with pure sesame oil and released onto Maa Ganga with Vedic mantras.</li>
                    <li><strong>Ganga Maha Aarti & Rudrashtakam:</strong> Recited exclusively on your behalf for protection, health, and prosperity.</li>
                    <li><strong>HD Video on WhatsApp:</strong> A personal video recording of your deepdaan and sankalp is sent within 24 hours.</li>
                    <li><strong>Kashi Vishwanath Prasad Couriered:</strong> Blessed Bhasma, Raksha Sutra, and sanctified Ganga Jal delivered to your home.</li>
                </ul>
            </div>

            <div style='text-align: center; margin: 28px 0 16px;'>
                <p style='color: #b91c1c; font-weight: bold; font-size: 14px; margin-bottom: 8px;'>⚠️ Strict Pandit Availability: Limited to First 108 Yajamans</p>
            </div>
        ";

        $emailHtml = $this->brevo->renderDevotionalHtml(
            '🪔 Dev Deepawali Varanasi 2026: 108 Maha Deepdaan Sankalp',
            $emailBody,
            'Book My Dev Deepawali Sankalp (From ₹501)',
            $pujaUrl
        );

        // 3. Push Notification Copy
        $pushNotification = [
            'title' => '🪔 Dev Deepawali in Varanasi: 10 Lakh Lamps on 84 Ghats',
            'body'  => [
                'description' => 'Light 108 holy diyas in your family Gotra at Kashi Manikarnika Ghat on Kartik Purnima. Book your sacred Sankalp now.',
                'action_url'  => $pujaUrl,
            ],
            'image' => 'https://onlinepuja.live/assets/images/festivals/dev-deepawali-varanasi.jpg',
        ];

        // 4. AI Video Reel Brief (1080x1920 MP4)
        $videoPrompt = 'Cinematic vertical 9:16 footage of Dev Deepawali in Varanasi, 84 ancient stone ghats illuminated by millions of glowing earthen diyas along the sacred Ganges river, majestic evening Dashashwamedh Ghat Maha Aarti with soaring brass fire lamps, golden flame reflections floating on sacred water, smoke, temple bells atmosphere, 4k ultra realistic photographic';

        return [
            'festival'           => 'Dev Deepawali Varanasi',
            'date_formatted'     => $dateFormatted,
            'whatsapp_copy'      => $whatsappCopy,
            'email_subject'      => $emailSubject,
            'email_html'         => $emailHtml,
            'push_notification'  => $pushNotification,
            'video_prompt'       => $videoPrompt,
            'booking_url'        => $pujaUrl,
            'branding'           => [
                'title'    => 'Dev Deepawali Varanasi 2026',
                'subtitle' => '108 Sacred Deepdaan & Kashi Sankalp',
                'cta'      => '+91 70071 58014 | onlinepuja.live',
            ],
        ];
    }

    /**
     * Chhath Puja signature multi-channel package.
     */
    protected function buildChhathPackage(string $dateFormatted): array
    {
        $pujaUrl = 'https://onlinepuja.live/pujas/chhath-puja-surya-mahaparv-sandhya-usha-arghya';

        $whatsappCopy = "☀️ *CHHATH PUJA SURYA MAHAPARV 2026: SANDHYA & USHA ARGHYA* ☀️\n\n"
            . "Hari Om! The most sacred 4-day Mahaparv dedicated to Surya Dev and Chhathi Maiya arrives on *{$dateFormatted}*.\n\n"
            . "✨ *Can't do the 36-Hour Nirjala Vrat in person?*\n"
            . "Our certified Vedic pandits offer complete Shodashopachara rituals on your family's behalf at Assi Ghat, Varanasi:\n"
            . "• Sandhya Arghya (sunset) & Usha Arghya (sunrise) offered in your exact Gotra.\n"
            . "• Traditional Bamboo Soop decorated with Thekua, Sugarcane, Coconut & Seasonal Fruits.\n"
            . "• Aditya Hridaya Stotram recitation for radiant health, progeny & family prosperity.\n"
            . "• Personalized HD video of Arghya shared on WhatsApp.\n"
            . "• Authentic sanctified Thekua prasad couriered to your doorstep.\n\n"
            . "👉 *Book Your Family Chhath Arghya Sankalp:*\n"
            . "{$pujaUrl}\n\n"
            . "📞 *Pandit Helpline:* +91 70071 58014\n"
            . "_Jai Chhathi Maiya · OnlinePuja.live_";

        $emailSubject = "☀️ Chhath Puja Surya Mahaparv: Offer Sacred Arghya at Varanasi Ghats ({$dateFormatted})";
        $emailBody = "
            <p>Pranam <strong>{{ devotee_name }}</strong>,</p>
            <p>Chhath Puja is India's most pious and rigorous Mahaparv, where cosmic life-energy is honored through deep reverence to Lord Surya and Chhathi Maiya. On <strong>{$dateFormatted}</strong>, offer sacred Arghya at Assi Ghat, Varanasi in your family's name.</p>
            <div style='background: #fff7ed; border-left: 4px solid #ea580c; padding: 14px 18px; margin: 20px 0;'>
                <p style='margin: 0; color: #9a3412; font-weight: bold;'>☀️ What Your Family Receives:</p>
                <p style='margin: 4px 0 0; color: #7c2d12; font-size: 14px;'>
                    Both Sandhya (sunset) and Usha (sunrise) milk and Ganga Jal Arghya with traditional bamboo soop, Aditya Hridaya Stotram path, personalized WhatsApp HD video, and sanctified Thekua prasad couriered home.
                </p>
            </div>
        ";

        $emailHtml = $this->brevo->renderDevotionalHtml(
            '☀️ Chhath Puja Surya Mahaparv Arghya Sankalp',
            $emailBody,
            'Book Sacred Chhath Arghya Sankalp',
            $pujaUrl
        );

        return [
            'festival'          => 'Chhath Puja',
            'date_formatted'    => $dateFormatted,
            'whatsapp_copy'     => $whatsappCopy,
            'email_subject'     => $emailSubject,
            'email_html'        => $emailHtml,
            'push_notification' => [
                'title' => '☀️ Chhath Puja: Offer Holy Arghya at Varanasi Ghats',
                'body'  => [
                    'description' => 'Book your family Gotra Sankalp for Sunset & Sunrise Arghya at Assi Ghat. Prasad delivered home.',
                    'action_url'  => $pujaUrl,
                ],
            ],
            'video_prompt'      => 'Chhath Puja holy sunrise ritual at river Ganga ghat in Varanasi, devotee offering sacred milk arghya to golden rising sun, brass pot, bamboo soop with fruits and thekua sweets, warm morning mist, 9:16 vertical 4k photographic',
            'booking_url'       => $pujaUrl,
            'branding'          => [
                'title'    => 'Chhath Puja Surya Mahaparv',
                'subtitle' => 'Sandhya & Usha Arghya at Varanasi',
                'cta'      => '+91 70071 58014 | onlinepuja.live',
            ],
        ];
    }

    /**
     * Generic high-conversion temple package generator for any festival.
     */
    protected function buildGenericFestivalPackage(string $name, string $dateFormatted): array
    {
        $pujaUrl = 'https://onlinepuja.live/pujas';

        $whatsappCopy = "🕉️ *{$name} 2026: SPECIAL VEDIC SANKALP PUJA* 🕉️\n\n"
            . "Hari Om! On the auspicious occasion of *{$name} ({$dateFormatted})*, invite the divine blessings of peace, health, and prosperity into your home.\n\n"
            . "Our verified Vedic Purohits perform the complete traditional rituals with your Family Gotra & Names on the sacred Ghats.\n\n"
            . "✨ *Devotee Blessings Included:*\n"
            . "• Complete Shodashopachara Puja with Vedic Mantras\n"
            . "• Personalized HD Video of the ritual on WhatsApp\n"
            . "• Sanctified Temple Prasad couriered to your home\n\n"
            . "👉 *Book Your Auspicious Puja Today:*\n"
            . "{$pujaUrl}\n\n"
            . "📞 *Pandit Helpline:* +91 70071 58014\n"
            . "_OnlinePuja.live · Authentic Vedic Services_";

        $emailSubject = "🕉️ {$name} Auspicious Muhurat & Special Puja Invitation ({$dateFormatted})";
        $emailBody = "
            <p>Pranam <strong>{{ devotee_name }}</strong>,</p>
            <p>The sacred festival of <strong>{$name}</strong> arrives on <strong>{$dateFormatted}</strong>. In the Vedic tradition, performing a ritual on this day with your name and gotra invokes lasting protection and abundance for the entire household.</p>
            <p>Our verified Vedic Purohits from Varanasi will perform the complete Shodashopachara rituals in your family's name and deliver sanctified Mahaprasad to your door.</p>
        ";

        $emailHtml = $this->brevo->renderDevotionalHtml(
            "🕉️ {$name} 2026 Special Vedic Sankalp",
            $emailBody,
            'Explore Bookable Pujas',
            $pujaUrl
        );

        return [
            'festival'          => $name,
            'date_formatted'    => $dateFormatted,
            'whatsapp_copy'     => $whatsappCopy,
            'email_subject'     => $emailSubject,
            'email_html'        => $emailHtml,
            'push_notification' => [
                'title' => "🕉️ {$name}: Special Vedic Puja Booking Open",
                'body'  => [
                    'description' => "Perform auspicious {$name} puja in your family Gotra with verified pandits. Prasad couriered home.",
                    'action_url'  => $pujaUrl,
                ],
            ],
            'video_prompt'      => "Devotional Hindu ritual for {$name}, brass diya lamps, marigold garlands, sacred incense, golden temple light, 9:16 vertical 4k photographic",
            'booking_url'       => $pujaUrl,
            'branding'          => [
                'title'    => "{$name} 2026",
                'subtitle' => 'Special Vedic Sankalp Puja',
                'cta'      => '+91 70071 58014 | onlinepuja.live',
            ],
        ];
    }

    /**
     * Synthesize a genuine 1080x1920 MP4 Video Reel for this festival.
     */
    public function generateFestivalVideoReel(string $festivalName, array $options = []): array
    {
        $pkg = $this->buildCampaignContent($festivalName);
        $prompt = $options['prompt'] ?? $pkg['video_prompt'];
        $branding = array_merge($pkg['branding'], $options['branding'] ?? []);

        return $this->ai->generateVideo($prompt, null, 5, $branding);
    }

    /**
     * Dispatch multi-channel campaign across WhatsApp, Brevo Email, and Web/App Push.
     */
    public function dispatchCampaign(
        string $festivalName,
        array $channels = ['whatsapp', 'email', 'push'],
        array $options = []
    ): array {
        $runId = RunLog::start('festival:campaign-dispatch', 'marketing', 'orchestrator');
        $pkg = $this->buildCampaignContent($festivalName);

        $results = [
            'festival'  => $festivalName,
            'channels'  => [],
            'video_url' => null,
            'dry_run'   => !empty($options['dry_run']),
        ];

        // 1. Synthesize or retrieve video reel if WhatsApp or social is active
        if (in_array('whatsapp', $channels) || in_array('video', $channels)) {
            if (!empty($options['video_url'])) {
                $results['video_url'] = $options['video_url'];
            } elseif (empty($options['dry_run'])) {
                $videoRes = $this->generateFestivalVideoReel($festivalName, $options);
                $results['video_url'] = $videoRes['video_url'] ?? null;
            } else {
                $results['video_url'] = 'https://onlinepuja.live/media/videos/mock_festival_reel.mp4';
            }
        }

        // 2. WhatsApp Channel Dispatch
        if (in_array('whatsapp', $channels)) {
            $recipients = $options['whatsapp_recipients'] ?? ['7007158014']; // Admin / Devotee test group by default
            $waSent = 0;

            foreach ($recipients as $num) {
                if (!empty($options['dry_run'])) {
                    $waSent++;
                    continue;
                }

                if (!empty($results['video_url'])) {
                    $res = $this->wa->sendMedia(
                        (string)$num,
                        $results['video_url'],
                        $pkg['whatsapp_copy'],
                        'video',
                        Str::slug($festivalName) . '_reel.mp4'
                    );
                } else {
                    $res = $this->wa->sendText((string)$num, $pkg['whatsapp_copy']);
                }

                if ($res['ok'] ?? false) {
                    $waSent++;
                }
            }

            $results['channels']['whatsapp'] = [
                'attempted' => count($recipients),
                'sent'      => $waSent,
            ];
        }

        // 3. Brevo Email Channel Dispatch
        if (in_array('email', $channels)) {
            $emailSent = 0;
            $emailRecipients = $options['email_recipients'] ?? [];

            if (empty($emailRecipients)) {
                // If no specific emails given, check for registered active users
                $users = DB::table('users')
                    ->whereNotNull('email')
                    ->where('email', '!=', '')
                    ->take(empty($options['dry_run']) ? 50 : 5)
                    ->get(['email', 'name']);

                foreach ($users as $u) {
                    $emailRecipients[] = ['email' => $u->email, 'name' => $u->name];
                }
            }

            foreach ($emailRecipients as $rec) {
                $toEmail = is_array($rec) ? $rec['email'] : (string)$rec;
                $toName = is_array($rec) ? ($rec['name'] ?? 'Devotee') : 'Devotee';

                if (!empty($options['dry_run'])) {
                    $emailSent++;
                    continue;
                }

                $personalizedHtml = str_replace('{{ devotee_name }}', htmlspecialchars($toName), $pkg['email_html']);
                $sendRes = $this->brevo->sendEmail(
                    $toEmail,
                    $toName,
                    $pkg['email_subject'],
                    $personalizedHtml
                );

                if ($sendRes['ok'] ?? false) {
                    $emailSent++;
                }
            }

            $results['channels']['email'] = [
                'attempted' => count($emailRecipients),
                'sent'      => $emailSent,
            ];
        }

        // 4. OneSignal / Web Push Notification Dispatch
        if (in_array('push', $channels)) {
            if (!empty($options['dry_run'])) {
                $results['channels']['push'] = ['ok' => true, 'dry_run' => true];
            } else {
                $pushData = $pkg['push_notification'];
                try {
                    $pushRes = $this->push->sendNotification([], [
                        'title' => $pushData['title'],
                        'body'  => $pushData['body'],
                        'priority' => 'custom',
                    ]);
                    $results['channels']['push'] = $pushRes;
                } catch (\Throwable $e) {
                    $results['channels']['push'] = ['ok' => false, 'error' => $e->getMessage()];
                }
            }
        }

        // Record in Brain Vault / Memory
        Vault::set('last_festival_campaign_' . Str::slug($festivalName), json_encode([
            'dispatched_at' => now()->toIso8601String(),
            'channels'      => $results['channels'],
            'video_url'     => $results['video_url'],
        ]));

        RunLog::finish(
            $runId,
            'ok',
            "Dispatched multi-channel campaign for {$festivalName}",
            $results,
            ($results['channels']['whatsapp']['sent'] ?? 0) + ($results['channels']['email']['sent'] ?? 0),
            0
        );

        return $results;
    }

    /**
     * Build tailored B2B Partner Pitch Package for Dev Deepawali Varanasi 2026.
     */
    public function buildB2bDevDeepawaliPitch(\App\Models\B2bLead $lead): array
    {
        $bizName = $lead->business_name ?: 'Partner';
        $contactPerson = $lead->contact_person ? " ({$lead->contact_person})" : '';
        $partnerCode = $lead->partner_code;
        $bookingUrl = $lead->getPartnerBookingUrl('dev-deepawali-maha-deepdaan-kashi-vishwanath-sankalp');
        $commissionPct = (int)$lead->commission_pct;

        $videoUrl = 'https://onlinepuja.live/storage/reels/reel_sacred_6ad8b1472b630.mp4';

        // 1. WhatsApp B2B Pitch
        $whatsappCopy = "🤝 *EXCLUSIVE B2B PARTNERSHIP: DEV DEEPAWALI VARANASI 2026* 🪔\n\n"
            . "Namaste *{$bizName}*{$contactPerson}!\n\n"
            . "On *Tuesday, 24 November 2026 (Kartik Purnima)*, over 10 Lakh lamps illuminate Varanasi's 84 Ghats. While hotels and boats in Kashi are 100% sold out, your clients can still participate through authentic Vedic Sankalp with *zero logistics hassle for you*.\n\n"
            . "💼 *What We Offer Your Travel Agency / Clients:*\n"
            . "• Certified Kashi Vedic Purohits chant your client's Gotra on Manikarnika & Dashashwamedh Ghats.\n"
            . "• 108 Sacred Earthen Diyas lit and released onto Maa Ganga in their family name.\n"
            . "• 1080p HD Video Reel delivered to client's WhatsApp within 24 hours.\n"
            . "• Kashi Vishwanath Mahaprasad & Ganga Jal couriered to client's doorstep.\n\n"
            . "💰 *Your B2B Commercial Earnings:*\n"
            . "• Earn *{$commissionPct}% Revenue Share* per booking (₹300 – ₹600 margin per client).\n"
            . "• 20 bookings = ₹10,000+ extra profit with zero operational overhead.\n\n"
            . "🔗 *Your Dedicated B2B Booking Link:*\n"
            . "{$bookingUrl}\n"
            . "*(Or share promo coupon code '{$partnerCode}' for client ₹101 discount)*\n\n"
            . "📞 *Varanasi B2B Desk Helpline:* +91 70071 58014\n"
            . "_OnlinePuja.live · Official Varanasi Pilgrimage Seva_";

        // 2. Brevo B2B Email Proposal HTML
        $emailSubject = "🤝 B2B Partnership: Offer Varanasi Dev Deepawali 2026 Pujas to Your Clients ({$commissionPct}% Margin)";
        $emailBody = "
            <div style='text-align: center; margin-bottom: 20px;'>
                <span style='background: #fef3c7; color: #b45309; font-size: 13px; font-weight: bold; padding: 6px 14px; border-radius: 9999px; text-transform: uppercase;'>
                    B2B Pilgrimage Partnership · Dev Deepawali 2026
                </span>
            </div>

            <p>Namaste <strong>{$bizName}</strong>,</p>

            <p>Dev Deepawali in Varanasi on <strong>24 November 2026</strong> attracts millions of Hindu devotees worldwide. Due to sold-out hotels and train tickets, 90% of devotees cannot reach Kashi in person. You can now fulfill their spiritual longing and generate substantial revenue for your business.</p>

            <div style='background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0;'>
                <h3 style='margin: 0 0 12px; color: #0f172a; font-size: 16px;'>📊 Partner Commercial Breakdown:</h3>
                <table width='100%' style='border-collapse: collapse; font-size: 14px;'>
                    <tr style='border-bottom: 1px solid #cbd5e1;'>
                        <td style='padding: 8px 0;'><strong>Dakshina Tier</strong></td>
                        <td style='padding: 8px 0;' align='center'><strong>Retail Price</strong></td>
                        <td style='padding: 8px 0;' align='right'><strong>Your Profit ({$commissionPct}%)</strong></td>
                    </tr>
                    <tr style='border-bottom: 1px solid #f1f5f9;'>
                        <td style='padding: 8px 0;'>Individual Deepdaan</td>
                        <td style='padding: 8px 0;' align='center'>₹501</td>
                        <td style='padding: 8px 0; color: #16a34a; font-weight: bold;' align='right'>₹125</td>
                    </tr>
                    <tr style='border-bottom: 1px solid #f1f5f9;'>
                        <td style='padding: 8px 0;'>Partner / Couple Sankalp</td>
                        <td style='padding: 8px 0;' align='center'>₹851</td>
                        <td style='padding: 8px 0; color: #16a34a; font-weight: bold;' align='right'>₹212</td>
                    </tr>
                    <tr style='border-bottom: 1px solid #f1f5f9;'>
                        <td style='padding: 8px 0;'>Family 108 Deepdaan + Bhog</td>
                        <td style='padding: 8px 0;' align='center'>₹1,500</td>
                        <td style='padding: 8px 0; color: #16a34a; font-weight: bold;' align='right'>₹375</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0;'>Joint Family Maha Deepdaan</td>
                        <td style='padding: 8px 0;' align='center'>₹2,100</td>
                        <td style='padding: 8px 0; color: #16a34a; font-weight: bold;' align='right'>₹525</td>
                    </tr>
                </table>
            </div>

            <p>Every ritual is recorded in 1080p HD with your client's name and gotra, and sanctified Mahaprasad from Kashi Vishwanath is couriered directly to their doorstep.</p>

            <div style='background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 8px; padding: 14px; margin: 20px 0; text-align: center;'>
                <p style='margin: 0; color: #065f46; font-size: 13px;'>Your Unique Partner Tracking Code: <strong style='font-size: 16px; color: #047857;'>{$partnerCode}</strong></p>
            </div>
        ";

        $emailHtml = $this->brevo->renderDevotionalHtml(
            '🤝 B2B Partnership: Varanasi Dev Deepawali 2026',
            $emailBody,
            'View B2B Partner Portal & Puja Details',
            $bookingUrl
        );

        return [
            'lead_id'        => $lead->id,
            'business_name'  => $bizName,
            'phone'          => $lead->phone,
            'email'          => $lead->email,
            'partner_code'   => $partnerCode,
            'booking_url'    => $bookingUrl,
            'whatsapp_copy'  => $whatsappCopy,
            'email_subject'  => $emailSubject,
            'email_html'     => $emailHtml,
            'video_url'      => $videoUrl,
        ];
    }

    /**
     * Dispatch B2B Multi-Channel Campaign to targeted B2B leads.
     */
    public function dispatchB2bCampaign(array $leadIds, array $channels = ['whatsapp', 'email'], array $options = []): array
    {
        $runId = RunLog::start('b2b:campaign-dispatch', 'marketing', 'b2b');

        $leads = \App\Models\B2bLead::whereIn('id', $leadIds)->get();
        if ($leads->isEmpty()) {
            return ['ok' => false, 'error' => 'No matching B2B leads found'];
        }

        $results = [
            'attempted' => $leads->count(),
            'whatsapp'  => ['sent' => 0, 'failed' => 0],
            'email'     => ['sent' => 0, 'failed' => 0],
            'dry_run'   => !empty($options['dry_run']),
        ];

        foreach ($leads as $lead) {
            $pitch = $this->buildB2bDevDeepawaliPitch($lead);

            // WhatsApp Dispatch
            if (in_array('whatsapp', $channels) && !empty($lead->phone)) {
                if (!empty($options['dry_run'])) {
                    $results['whatsapp']['sent']++;
                } else {
                    $waRes = $this->wa->sendMedia(
                        $lead->clean_phone,
                        $pitch['video_url'],
                        $pitch['whatsapp_copy'],
                        'video',
                        'dev_deepawali_b2b_pitch.mp4'
                    );

                    if ($waRes['ok'] ?? false) {
                        $results['whatsapp']['sent']++;
                    } else {
                        $results['whatsapp']['failed']++;
                    }
                }
            }

            // Brevo Email Dispatch
            if (in_array('email', $channels) && !empty($lead->email)) {
                if (!empty($options['dry_run'])) {
                    $results['email']['sent']++;
                } else {
                    $emRes = $this->brevo->sendEmail(
                        $lead->email,
                        $lead->contact_person ?: $lead->business_name,
                        $pitch['email_subject'],
                        $pitch['email_html']
                    );

                    if ($emRes['ok'] ?? false) {
                        $results['email']['sent']++;
                    } else {
                        $results['email']['failed']++;
                    }
                }
            }

            if (empty($options['dry_run'])) {
                $lead->update([
                    'last_contacted_at' => now(),
                    'status'            => $lead->status === 'new' ? 'contacted' : $lead->status,
                ]);
            }
        }

        RunLog::finish($runId, 'ok', "Dispatched B2B campaign to {$results['attempted']} partners", $results, $results['whatsapp']['sent'] + $results['email']['sent'], 0);

        return ['ok' => true] + $results;
    }
}
