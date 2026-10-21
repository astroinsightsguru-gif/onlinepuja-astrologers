<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class MarketingAutomationService
{
    protected BrevoEmailService $brevo;
    protected EvolutionWaService $wa;

    public function __construct(BrevoEmailService $brevo, EvolutionWaService $wa)
    {
        $this->brevo = $brevo;
        $this->wa = $wa;
    }

    /**
     * Journey 1: Devotee Onboarding (Welcome Email + WhatsApp).
     */
    public function welcomeNewDevotee(User $user): array
    {
        $results = ['email' => null, 'whatsapp' => null, 'sync' => null];
        $name = $user->name ?: 'Devotee';
        $email = $user->email ?? null;
        $mobile = $user->contactNo ?? $user->mobile ?? null;

        // 1. Sync Contact to Brevo Marketing List
        if (!empty($email)) {
            $results['sync'] = $this->brevo->syncContact($email, [
                'FIRSTNAME' => $name,
                'SMS'       => $mobile ? ('+91' . preg_replace('/[^0-9]/', '', $mobile)) : '',
            ]);

            // 2. Send Devotional Welcome Email
            $bodyHtml = "
                <p>Pranam <strong>{$name}</strong>,</p>
                <p>Welcome to <strong>OnlinePuja.live</strong> — your direct bridge to authentic Vedic rituals and sacred Hindu ceremonies performed at Varanasi (Kashi), Haridwar, and Trimbakeshwar.</p>
                <div style='background-color: #fef2f2; border-left: 4px solid #dc2626; padding: 14px 18px; margin: 20px 0; border-radius: 6px;'>
                    <p style='margin: 0; color: #991b1b; font-weight: bold;'>Special Devotee Blessing Gift 🎁</p>
                    <p style='margin: 4px 0 0; color: #7f1d1d; font-size: 14px;'>Use promo coupon code <strong style='font-size: 16px; color: #b91c1c;'>SANKALP101</strong> at checkout to claim ₹101 off your first sanctified Puja or Pandit consultation.</p>
                </div>
                <p>May Lord Shiva and the Divine Mother bestow health, peace, and abundance upon you and your family.</p>
            ";

            $html = $this->brevo->renderDevotionalHtml(
                '🕉️ Welcome to the Divine OnlinePuja Family',
                $bodyHtml,
                'Explore Bookable Pujas',
                'https://onlinepuja.live/pujas'
            );

            $results['email'] = $this->brevo->sendEmail(
                $email,
                $name,
                '🕉️ Welcome to OnlinePuja — Your Special Devotee Welcome Gift Inside',
                $html
            );
        }

        // 3. Send WhatsApp Welcome Message
        if (!empty($mobile)) {
            $waMsg = "🕉️ *Hari Om, {$name}! Welcome to OnlinePuja.live*\n\n"
                   . "We are honored to have you in our spiritual community. Our verified Vedic Pandits from Varanasi Ghats are here to assist your family in all sacred rituals.\n\n"
                   . "🎁 *Your First Puja Gift:* Use coupon code *SANKALP101* for ₹101 off.\n\n"
                   . "How can we assist you today?\n"
                   . "1. 🪔 Book a Vedic Puja\n"
                   . "2. 🔮 Consult an Astrologer\n"
                   . "3. 📜 Free Janam Kundli & Dosha Analysis\n\n"
                   . "Visit us anytime at https://onlinepuja.live";

            $results['whatsapp'] = $this->wa->sendButtons(
                $mobile,
                '🕉️ Welcome to OnlinePuja.live',
                "Hari Om, {$name}! Welcome to our spiritual community. Claim ₹101 off with code *SANKALP101*.",
                [
                    ['id' => 'view_pujas', 'text' => '🙏 Book Puja'],
                    ['id' => 'astrologer', 'text' => '🔮 Talk to Pandit'],
                    ['id' => 'kundli', 'text' => '📜 Free Kundli']
                ]
            );
        }

        return $results;
    }

    /**
     * Journey 2: Puja Booking Confirmation (WhatsApp + Email).
     */
    public function notifyPujaBooking(array $order): array
    {
        $name = $order['customer_name'] ?? 'Devotee';
        $puja = $order['puja_title'] ?? 'Sanctified Puja Ceremony';
        $date = $order['puja_date'] ?? date('d M Y');
        $time = $order['puja_time'] ?? 'Auspicious Muhurat';
        $mobile = $order['customer_mobile'] ?? null;
        $email = $order['customer_email'] ?? null;

        $results = [];

        // WhatsApp Confirmation
        if ($mobile) {
            $msg = "🕉️ *Puja Booking Confirmed — Har Har Mahadev!*\n\n"
                 . "Pranam *{$name}*,\n"
                 . "Your booking for *{$puja}* has been sanctified.\n\n"
                 . "📅 *Date:* {$date}\n"
                 . "⏰ *Muhurat:* {$time}\n"
                 . "📍 *Location:* Varanasi Kashi Temple Ghats\n\n"
                 . "Our senior Acharya will take your Gotra and family Sankalp. You will receive live video stream links 30 minutes prior to the ritual.\n\n"
                 . "For assistance, reply to this message anytime.";

            $results['whatsapp'] = $this->wa->sendText($mobile, $msg);
        }

        // Email Confirmation
        if ($email) {
            $body = "
                <p>Pranam <strong>{$name}</strong>,</p>
                <p>Your auspicious booking for <strong>{$puja}</strong> has been confirmed by our temple council.</p>
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
                    <tr style='border-bottom: 1px solid #fee2e2;'>
                        <td style='padding: 8px 0; color: #64748b;'>Puja Ceremony:</td>
                        <td style='padding: 8px 0; font-weight: bold; color: #991b1b;'>{$puja}</td>
                    </tr>
                    <tr style='border-bottom: 1px solid #fee2e2;'>
                        <td style='padding: 8px 0; color: #64748b;'>Date & Muhurat:</td>
                        <td style='padding: 8px 0; font-weight: bold;'>{$date} · {$time}</td>
                    </tr>
                    <tr style='border-bottom: 1px solid #fee2e2;'>
                        <td style='padding: 8px 0; color: #64748b;'>Ritual Sthalam:</td>
                        <td style='padding: 8px 0;'>Varanasi Holy Ganga Ghats</td>
                    </tr>
                </table>
                <p>Our Vedic Acharya will chant your ancestral Gotra Sankalp during the sacred havan offerings. Blessed Prasad and Raksha Sutra will be dispatched following completion.</p>
            ";

            $html = $this->brevo->renderDevotionalHtml(
                "Booking Confirmed: {$puja}",
                $body,
                'View My Booking Details',
                'https://onlinepuja.live/admin/login'
            );

            $results['email'] = $this->brevo->sendEmail(
                $email,
                $name,
                "🕉️ Sankalp Confirmed: {$puja} on {$date}",
                $html
            );
        }

        return $results;
    }

    /**
     * Journey 3: Festival Campaign Broadcast (WhatsApp Reel/Poster + Brevo Newsletter).
     */
    public function broadcastFestivalCampaign(
        string $festivalTitle,
        string $description,
        ?string $videoReelUrl = null,
        ?string $posterUrl = null,
        array $devoteeContacts = []
    ): array {
        $stats = ['whatsapp_sent' => 0, 'email_sent' => 0, 'errors' => []];

        foreach ($devoteeContacts as $devotee) {
            $mobile = $devotee['mobile'] ?? null;
            $email = $devotee['email'] ?? null;
            $name = $devotee['name'] ?? 'Devotee';

            // WhatsApp Dispatch (Send Video Reel MP4 if available!)
            if ($mobile) {
                if ($videoReelUrl) {
                    $caption = "🕉️ *{$festivalTitle} Special Havan & Puja*\n\n"
                             . "Pranam {$name},\n{$description}\n\n"
                             . "👉 Book your family Sankalp with Kashi Pandits: https://onlinepuja.live/pujas";
                    $res = $this->wa->sendMedia($mobile, $videoReelUrl, $caption, 'video');
                } elseif ($posterUrl) {
                    $caption = "🕉️ *{$festivalTitle} Special Puja*\n\n{$description}\n\n👉 Book: https://onlinepuja.live/pujas";
                    $res = $this->wa->sendMedia($mobile, $posterUrl, $caption, 'image');
                } else {
                    $msg = "🕉️ *{$festivalTitle} Blessings*\n\n{$description}\n\n👉 Book your Sankalp: https://onlinepuja.live/pujas";
                    $res = $this->wa->sendText($mobile, $msg);
                }

                if (!empty($res['ok'])) {
                    $stats['whatsapp_sent']++;
                } else {
                    $stats['errors'][] = "WA: {$mobile} - " . ($res['error'] ?? 'failed');
                }
            }

            // Email Dispatch
            if ($email) {
                $bodyHtml = "<p>Pranam <strong>{$name}</strong>,</p><p>{$description}</p>";
                if ($posterUrl) {
                    $bodyHtml .= "<p style='text-align:center;'><img src='{$posterUrl}' style='max-width:100%; border-radius:12px; margin:16px 0;' /></p>";
                }
                $html = $this->brevo->renderDevotionalHtml("🕉️ {$festivalTitle} Maha Puja", $bodyHtml, 'Participate in Sacred Puja', 'https://onlinepuja.live/pujas');
                $resMail = $this->brevo->sendEmail($email, $name, "🕉️ {$festivalTitle} — Auspicious Sankalp & Puja Invitations", $html);
                if (!empty($resMail['ok'])) {
                    $stats['email_sent']++;
                }
            }
        }

        return $stats;
    }

    /**
     * Journey 4: Abandoned Cart Nudge.
     */
    public function recoverAbandonedCart(string $name, ?string $mobile, ?string $email, string $pujaTitle, string $checkoutUrl): array
    {
        $res = [];
        if ($mobile) {
            $msg = "🕉️ *Namaste {$name}*,\n\n"
                 . "You began booking the auspicious *{$pujaTitle}*, but your Sankalp is not yet completed.\n\n"
                 . "Astrologically, completing your ritual during this planetary window brings peace and removes obstacles.\n\n"
                 . "👉 Tap here to complete your Sankalp: {$checkoutUrl}";
            $res['whatsapp'] = $this->wa->sendText($mobile, $msg);
        }

        if ($email) {
            $body = "
                <p>Pranam <strong>{$name}</strong>,</p>
                <p>We noticed you were scheduling your family Sankalp for <strong>{$pujaTitle}</strong>.</p>
                <p>Senior Acharyas at Kashi recommend completing your reservation ahead of the upcoming tithi to guarantee pandit availability and authentic havan samagri preparation.</p>
            ";
            $html = $this->brevo->renderDevotionalHtml("Complete Your Sankalp: {$pujaTitle}", $body, 'Resume Puja Booking', $checkoutUrl);
            $res['email'] = $this->brevo->sendEmail($email, $name, "🕉️ Reminder: Your {$pujaTitle} Sankalp is Awaiting", $html);
        }

        return $res;
    }
}
