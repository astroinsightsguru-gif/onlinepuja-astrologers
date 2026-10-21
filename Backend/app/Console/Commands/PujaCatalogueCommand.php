<?php

namespace App\Console\Commands;

use App\Brain\RunLog;
use App\Media\ImageStudio;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * The real puja catalogue.
 *
 * The site shipped with three listings — Lakshmi, "Knowledge Education &
 * Creativity" and Maha Mrityunjaya — every one of them priced at ten rupees,
 * with a booking window that closed in December 2025 and a one-line
 * description. A visitor arriving from search or an ad landed on a storefront
 * that read as abandoned, and the Growth OS was generating articles to send
 * more of them there.
 *
 * This command replaces that with a catalogue an operator would actually put
 * their name to: pujas anchored to the festivals people search for, priced in
 * line with the market (YatraDham/Sri Mandir sit at Rs 649-2,899), scheduled on
 * real upcoming dates, each with genuine ritual detail and concrete benefits.
 *
 * Nothing goes live on its own. Everything is inserted with puja_status = 0 and
 * isAdminApproved = 'Pending', so it sits in the admin puja list for review and
 * shows nowhere on the public site until `--activate` (or a human in the admin)
 * turns it on.
 *
 *   php artisan puja:catalogue                 # seed/refresh drafts
 *   php artisan puja:catalogue --with-images   # + generate a hero image each
 *   php artisan puja:catalogue --activate      # flip every seeded draft live
 *   php artisan puja:catalogue --activate --only=diwali-lakshmi-ganesh-puja
 */
class PujaCatalogueCommand extends Command
{
    protected $signature = 'puja:catalogue
        {--activate : Flip seeded drafts to live (puja_status=1, Approved)}
        {--with-images : Generate a hero image for each puja via ImageStudio}
        {--only= : Restrict to one slug}
        {--dry-run : Show what would change, write nothing}';

    protected $description = 'Seed / refresh / activate the reviewed puja catalogue';

    /** Marker written into every seeded row so --activate and refresh can find them. */
    protected const SOURCE_TAG = 'catalogue-v1';

    /** The four package tiers every catalogue puja offers, by number of yajaman. */
    protected const PACKAGE_PRICING = [
        2 => ['title' => 'Individual Puja',                        'person' => 1, 'inr' => 501,  'usd' => 7],
        4 => ['title' => 'Partner Puja',                           'person' => 2, 'inr' => 851,  'usd' => 11],
        5 => ['title' => 'Family + Bhog',                          'person' => 4, 'inr' => 1500, 'usd' => 18],
        6 => ['title' => 'Joint Family + Bhog + Flower Basket',    'person' => 6, 'inr' => 2100, 'usd' => 25],
    ];

    protected const PACKAGE_IDS = ['2', '4', '5', '6'];

    public function handle(ImageStudio $images): int
    {
        $runId = RunLog::start('puja:catalogue', 'catalogue', 'manual');

        if ($this->option('activate')) {
            $n = $this->activate();
            $this->info("Activated {$n} catalogue puja(s) — now live and bookable.");
            RunLog::finish($runId, 'ok', "Activated {$n} puja(s)", [], $n, 0);

            return self::SUCCESS;
        }

        $this->syncPackages();
        $this->supersedeDummies();
        $categories = $this->syncCategories();

        $pujas = $this->pujas();
        $only = $this->option('only');
        $created = 0;
        $updated = 0;
        $withImage = 0;

        foreach ($pujas as $p) {
            $slug = Str::slug($p['title']);

            if ($only && $slug !== $only) {
                continue;
            }

            [$start, $end] = $this->schedule($p);

            $row = [
                'category_id' => $categories[$p['category']],
                'sub_category_id' => null,
                'puja_title' => $p['title'],
                'slug' => $slug,
                'puja_subtitle' => $p['subtitle'],
                'puja_place' => $p['place'],
                'long_description' => trim($p['description']),
                'puja_benefits' => json_encode($this->benefitObjects($p['benefits'])),
                'package_id' => json_encode(self::PACKAGE_IDS),
                'puja_start_datetime' => $start,
                'puja_end_datetime' => $end,
                'puja_duration' => (string) $p['duration'],
                'created_by' => 'admin',            // required for the admin + frontend lists
                'updated_at' => now(),
            ];

            $existing = DB::table('pujas')->where('slug', $slug)->first();

            // A fresh row is hidden until --activate. A refresh must NOT touch
            // the live/approval state — re-running this to fix copy or backfill
            // an image was silently reverting pujas the operator had already
            // taken live back to draft.
            if (! $existing) {
                $row['puja_status'] = 0;
                $row['isAdminApproved'] = 'Pending';
            }

            if ($this->option('dry-run')) {
                $this->line(($existing ? '~ ' : '+ ')."{$p['title']}  ({$p['category']}, Rs".self::PACKAGE_PRICING[2]['inr'].'+, '.$start.')');

                continue;
            }

            if ($existing) {
                // Only refresh rows this command owns, or the original three
                // dummies we are explicitly here to replace.
                $ownable = ($existing->created_by === 'admin')
                    && ($existing->puja_price === null || (float) $existing->puja_price <= 100 || $this->isSeeded($existing));

                if (! $ownable) {
                    $this->warn("skip (hand-edited): {$p['title']}");

                    continue;
                }

                DB::table('pujas')->where('id', $existing->id)->update($row + ['puja_price' => null]);
                $id = $existing->id;
                $updated++;
            } else {
                $id = DB::table('pujas')->insertGetId($row + ['puja_price' => null, 'created_at' => now()]);
                $created++;
            }

            $this->tagSeeded($id);

            if ($this->option('with-images')) {
                if ($this->makeImage($images, $id, $p)) {
                    $withImage++;
                }
            }
        }

        // Retire any older catalogue row that is no longer in the list.
        $liveSlugs = collect($pujas)->map(fn ($p) => Str::slug($p['title']))->all();
        $orphans = DB::table('pujas')
            ->whereIn('id', $this->seededIds())
            ->whereNotIn('slug', $liveSlugs)
            ->update(['puja_status' => 0, 'isAdminApproved' => 'Rejected', 'updated_at' => now()]);

        $this->info("Catalogue: {$created} created, {$updated} refreshed, {$withImage} with images, {$orphans} retired.");
        $this->line('All drafts. Review in admin, then: php artisan puja:catalogue --activate');

        RunLog::finish(
            $runId,
            'ok',
            "Seeded {$created} + refreshed {$updated} puja draft(s)",
            ['created' => $created, 'updated' => $updated, 'images' => $withImage, 'retired' => $orphans],
            $created + $updated,
            0
        );

        return self::SUCCESS;
    }

    /* ------------------------------------------------------------ packages */

    /**
     * Bring the four shared package rows to the agreed mid-market pricing. They
     * were seeded at Rs 10-45 (test data) and are referenced only by the dummy
     * pujas, so this is safe to overwrite.
     */
    protected function syncPackages(): void
    {
        foreach (self::PACKAGE_PRICING as $id => $pkg) {
            $exists = DB::table('puja_package')->where('id', $id)->exists();

            $data = [
                'title' => $pkg['title'],
                'person' => (string) $pkg['person'],
                'package_price' => $pkg['inr'],
                'package_price_usd' => $pkg['usd'],
                'package_status' => 1,
                'updated_at' => now(),
            ];

            if ($this->option('dry-run')) {
                $this->line("package #{$id}: {$pkg['title']} -> Rs{$pkg['inr']} / \${$pkg['usd']}");

                continue;
            }

            $exists
                ? DB::table('puja_package')->where('id', $id)->update($data)
                : DB::table('puja_package')->insert(['id' => $id] + $data + ['created_at' => now()]);
        }
    }

    /**
     * The three seeded dummy pujas — Rs 10, expired December 2025, one-line
     * descriptions — are superseded by richer listings in this catalogue
     * (Friday Lakshmi Puja, Maha Mrityunjaya Jaap, Vasant Panchami Saraswati).
     * Pull them out of the live site rather than leaving a good and a dummy
     * Lakshmi listing side by side. Nothing is deleted — the rows stay,
     * de-listed, in case an order history references them.
     */
    protected function supersedeDummies(): void
    {
        $dummySlugs = ['lakshmi-puja', 'knowledge-education-creativity', 'maha-mrityunjaya-puja'];

        $q = DB::table('pujas')
            ->whereIn('slug', $dummySlugs)
            ->where('created_by', 'admin')
            ->where(function ($w) {
                // Guard: only retire it if it still looks like the seed data.
                $w->whereNull('puja_price')->orWhere('puja_price', '<=', 100);
            })
            ->where('puja_end_datetime', '<', now());

        if ($this->option('dry-run')) {
            $this->line('supersede: '.$q->count().' dummy puja(s) would be de-listed');

            return;
        }

        $n = $q->update([
            'puja_status' => 0,
            'isAdminApproved' => 'Rejected',
            'updated_at' => now(),
        ]);

        if ($n) {
            $this->line("Superseded {$n} dummy puja(s) — de-listed, not deleted.");
        }
    }

    /* ---------------------------------------------------------- categories */

    /**
     * Ensure every category the catalogue needs exists, reusing the ones that
     * are already there. Returns name => id.
     *
     * @return array<string,int>
     */
    protected function syncCategories(): array
    {
        $needed = [
            'Ganesh', 'Lakshmi', 'Durga', 'Shiva', 'Vishnu', 'Saraswati',
            'Hanuman', 'Surya', 'Krishna', 'Shradh & Pitru', 'Navagraha & Dosha Nivaran',
        ];

        $map = [];

        foreach ($needed as $name) {
            $row = DB::table('puja_categories')
                ->where('isDelete', 0)
                ->where('name', 'like', $name)
                ->first();

            if ($row) {
                $map[$name] = $row->id;

                continue;
            }

            // Fuzzy-match an existing category before creating a new one, so we
            // do not end up with "Ganesh" alongside the seeded "Ganesh".
            $fuzzy = DB::table('puja_categories')->where('isDelete', 0)
                ->where('name', 'like', '%'.explode(' ', $name)[0].'%')->first();

            if ($fuzzy) {
                $map[$name] = $fuzzy->id;

                continue;
            }

            if ($this->option('dry-run')) {
                $this->line("category + {$name}");
                $map[$name] = 0;

                continue;
            }

            $map[$name] = DB::table('puja_categories')->insertGetId([
                'name' => $name,
                'image' => '',                       // operator uploads in admin
                'isActive' => 1,
                'isDelete' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $map;
    }

    /**
     * The puja-details view renders each benefit as {{ $benefit['title'] }} and
     * {{ $benefit['description'] }}, so a flat list of strings crashes the page
     * ("Cannot access offset of type string on string"). Benefits are authored
     * here as 'Short title' => 'Full sentence' and converted to the shape the
     * template — and the admin editor — expect.
     *
     * @param  array<string,string>|array<int,string>  $benefits
     * @return array<int,array{title:string,description:string}>
     */
    protected function benefitObjects(array $benefits): array
    {
        $out = [];

        foreach ($benefits as $title => $description) {
            if (is_int($title)) {
                // Bare string — derive a title from the first clause.
                $description = (string) $description;
                $title = Str::of($description)->before(',')->before(' — ')->limit(48, '')->trim();
            }

            $out[] = ['title' => (string) $title, 'description' => (string) $description];
        }

        return $out;
    }

    /* ----------------------------------------------------------- scheduling */

    /**
     * Resolve a puja's schedule spec into a concrete [start, end] window.
     *
     * A fresh page needs a runway to be crawled and to rank, so a festival puja
     * is scheduled ON the festival if there is enough lead time and otherwise
     * one year on — never on a date that has already passed.
     *
     * @return array{0:string,1:string}
     */
    protected function schedule(array $p): array
    {
        $tz = 'Asia/Kolkata';
        $duration = (int) $p['duration'];

        if (! empty($p['festival'])) {
            $date = DB::table('hindu_festivals')
                ->where('name', 'like', '%'.$p['festival'].'%')
                ->orderBy('date')
                ->value('date');

            $start = $date ? Carbon::parse($date, $tz) : now($tz)->addDays(21);

            // Past, or too close to index in time -> same festival next year.
            if ($start->lt(now($tz)->addDays(5))) {
                $start = $start->addYear();
            }

            $start->setTime(7, 30);
        } elseif (! empty($p['weekday'])) {
            // Next occurrence of this weekday (1=Mon … 7=Sun), at least 4 days out.
            $start = now($tz)->addDays(4)->next($p['weekday'])->setTime(8, 0);
        } else {
            $start = now($tz)->addDays($p['in_days'] ?? 14)->setTime(8, 0);
        }

        return [
            $start->format('Y-m-d H:i:s'),
            $start->copy()->addMinutes($duration)->format('Y-m-d H:i:s'),
        ];
    }

    /* --------------------------------------------------------------- images */

    protected function makeImage(ImageStudio $studio, int $pujaId, array $p): bool
    {
        // Resumable: Pollinations is slow and rate-limited under load, so a run
        // may only get through a handful. Skip a puja that already has a real
        // (non-empty, non-placeholder) image so a repeat run fills the gaps.
        $current = DB::table('pujas')->where('id', $pujaId)->value('puja_images');
        $decoded = json_decode((string) $current, true);

        if (is_array($decoded) && ! empty($decoded[0]) && ! str_contains((string) $decoded[0], 'person.png')) {
            return false;
        }

        $prompt = ($p['image_prompt'] ?? '')
            ?: "Devotional Hindu puja scene for {$p['title']}: brass diya lamps, marigold and rose garlands, "
               ."a decorated altar with the deity, incense smoke, warm golden temple light, no text, photographic";

        try {
            $res = $studio->generate($prompt, ['width' => 1200, 'height' => 800]);
        } catch (\Throwable $e) {
            $this->warn("  image failed ({$p['title']}): ".$e->getMessage());

            return false;
        }

        $path = $res['path'] ?? null;

        if (! ($res['ok'] ?? false) || ! $path) {
            $this->warn("  image failed ({$p['title']}): ".($res['error'] ?? 'no path'));

            return false;
        }

        DB::table('pujas')->where('id', $pujaId)->update([
            'puja_images' => json_encode([$path]),
            'updated_at' => now(),
        ]);

        return true;
    }

    /* --------------------------------------------------------- seeded-set */

    protected function tagSeeded(int $id): void
    {
        // brain_memories.v carries a json_valid() CHECK constraint, so the
        // marker has to be JSON, not a bare string.
        DB::table('brain_memories')->updateOrInsert(
            ['k' => 'puja_seeded_'.$id],
            [
                'v' => json_encode(['tag' => self::SOURCE_TAG, 'at' => now()->toDateTimeString()]),
                'source' => 'puja:catalogue',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    protected function isSeeded(object $puja): bool
    {
        return in_array($puja->id, $this->seededIds(), true);
    }

    /**
     * Not memoised: this command tags rows as it runs, and the orphan sweep at
     * the end needs to see those tags.
     *
     * @return array<int,int>
     */
    protected function seededIds(): array
    {
        return DB::table('brain_memories')
            ->where('k', 'like', 'puja_seeded_%')
            ->where('v', 'like', '%'.self::SOURCE_TAG.'%')
            ->pluck('k')
            ->map(fn ($k) => (int) Str::after($k, 'puja_seeded_'))
            ->all();
    }

    protected function activate(): int
    {
        $q = DB::table('pujas')->whereIn('id', $this->seededIds());

        if ($only = $this->option('only')) {
            $q->where('slug', $only);
        }

        return $q->update([
            'puja_status' => 1,
            'isAdminApproved' => 'Approved',
            'updated_at' => now(),
        ]);
    }

    /* ------------------------------------------------------------- content */

    /**
     * The catalogue. Ordered roughly by the calendar so the nearest festivals
     * come first. `place` names a real pilgrimage site the puja is associated
     * with; `duration` is in minutes and is meant to be believable, not padded.
     *
     * @return array<int,array<string,mixed>>
     */
    protected function pujas(): array
    {
        return [
            /* ============================ GANESH ============================ */
            [
                'title' => 'Ganesh Chaturthi Vinayaka Puja & Modak Bhog',
                'subtitle' => 'Begin every new venture with the remover of obstacles',
                'category' => 'Ganesh',
                'place' => 'Siddhivinayak, Mumbai',
                'festival' => 'Ganesh Chaturthi',
                'duration' => 75,
                'description' => <<<'TXT'
Ganesh Chaturthi marks the birth of Lord Ganesha, the deity invoked before any
new beginning — a home, a business, a marriage, a journey. On this day the
Pranapratishtha (invocation) and Shodashopachara (sixteen-step) puja carry
special weight, and a puja done with your name and gotra is believed to clear
the year of obstacles before they form.

Our pandit performs the full Vinayaka vratam on your behalf at Siddhivinayak:
Sankalpa in your name and gotra, Ganapati Atharvashirsha path, 108 names of
Ganesha, Durva and modak offering, and aarti. You choose the tier by how many
family members are named in the Sankalpa.

You receive a recorded HD video of the complete ritual on WhatsApp within 3-4
days, and tirth prasad and blessed durva are couriered to your address.
TXT,
                'benefits' => [
                    'Obstacles cleared before the year begins' => 'The Vinayaka vratam is performed in your name and gotra so new ventures, homes and journeys started this year begin without hindrance.',
                    'Ganapati Atharvashirsha path' => 'The full Atharvashirsha is recited on your behalf, the most authoritative Ganesha stotra in the tradition.',
                    'Traditional durva and modak offering' => 'Twenty-one blades of durva grass and modak are offered exactly as the shastra prescribes.',
                    'Recorded HD puja video' => 'The complete ritual is filmed and sent to your WhatsApp within 3-4 days.',
                    'Prasad couriered home' => 'Blessed durva and tirth prasad from Siddhivinayak reach your door.',
                ],
                'image_prompt' => 'Lord Ganesha idol on a decorated altar with red hibiscus flowers, modak sweets, 21 blades of durva grass, brass lamps, marigold garlands, warm temple light, photographic, no text',
            ],

            /* ========================= PITRU PAKSHA ========================= */
            [
                'title' => 'Pitru Paksha Shradh & Tarpan Puja at Gaya',
                'subtitle' => 'Repay the ancestral debt and free the family line',
                'category' => 'Shradh & Pitru',
                'place' => 'Vishnupad Temple, Gaya',
                'festival' => 'Pitru Paksha',
                'duration' => 90,
                'description' => <<<'TXT'
The sixteen days of Pitru Paksha are the one window in the year the shastras set
aside for Shradh — the rite by which a household honours three generations of
departed ancestors and asks their blessing for the living. Gaya is the foremost
place on earth to perform it; a Pind Daan done here is said to grant the
ancestors permanent peace so the rite need not be repeated.

Our pandit performs Pind Daan and Tarpan in your name and gotra at the Vishnupad
temple and the Falgu river: Sankalpa, Pind preparation with rice and til, Tarpan
with water and black sesame, Brahmin bhojan, and daan on your behalf. Name the
ancestors you wish remembered when you book.

You receive the recorded ritual video and the sankalpa details on WhatsApp, and
prasad from the Vishnupad temple is sent to your home.
TXT,
                'benefits' => [
                    'Pind Daan at Gaya' => 'Performed at the Vishnupad temple and the Falgu river, the highest seat for Shradh in the Hindu tradition.',
                    'Relief from Pitru Dosh' => 'Addresses the ancestral affliction linked to obstacles in progeny, marriage and family harmony.',
                    'Ancestors named individually' => 'Each departed family member you name is remembered by name and gotra in the Sankalpa.',
                    'Brahmin bhojan and daan' => 'The feeding of Brahmins and charitable giving are completed on your behalf.',
                    'Video and Vishnupad prasad' => 'The recorded ritual and temple prasad are sent to your home.',
                ],
                'image_prompt' => 'Riverbank shradh ritual with pind offerings of rice on banana leaves, black sesame, kusha grass, a brass water pot, soft morning light on the Falgu river, respectful and solemn, photographic, no text',
            ],

            /* ============================ NAVRATRI ============================ */
            [
                'title' => 'Navratri Durga Saptashati Path & Kanya Pujan',
                'subtitle' => 'Nine nights of the Mother, one complete recitation for your family',
                'category' => 'Durga',
                'place' => 'Vindhyavasini Dham, Mirzapur',
                'festival' => 'Navratri',
                'duration' => 180,
                'description' => <<<'TXT'
Across the nine nights of Sharad Navratri the Durga Saptashati — the 700 verses
of the Devi Mahatmya — is recited in full to invoke the Mother's protection over
a household for the coming year. Done with sankalpa, nyasa and the prescribed
argala and keelak stotras, a complete path is considered one of the most
powerful family remedies in the Shakta tradition.

Our pandits at Vindhyavasini Dham perform Ghatasthapana in your name on the first
day, recite the complete Saptashati across the nine days with the Navarna mantra
japa, conduct a havan on Ashtami, and perform Kanya Pujan on the ninth day —
feeding and honouring nine young girls as the living Devi, with the daan and
dakshina made in your name.

The Ashtami havan and Kanya Pujan are filmed and shared on WhatsApp, and prasad
and a red chunri offered to the Devi are couriered to you.
TXT,
                'benefits' => [
                    'Complete Durga Saptashati' => 'All 700 verses of the Devi Mahatmya are recited across the nine nights in your name.',
                    'Ghatasthapana and Navarna japa' => 'The kalash is established on day one and the Navarna mantra is chanted through Navratri for family protection.',
                    'Ashtami havan and Kanya Pujan' => 'The fire offering and the worship of nine girls as the living Devi are performed on your behalf.',
                    'Shield for the year' => 'Traditionally invoked against negativity, litigation and ill-wishers for the twelve months ahead.',
                    'Chunri and prasad couriered' => 'A red chunri offered to the Devi and prasad from Vindhyavasini Dham reach your home.',
                ],
                'image_prompt' => 'Goddess Durga idol with a lion, red chunri, nine clay kalash pots with barley sprouts, a havan fire, red hibiscus garlands, oil lamps, vivid festive temple light, photographic, no text',
            ],
            [
                'title' => 'Durga Ashtami Sandhi Puja & Havan',
                'subtitle' => 'The 48 minutes when the Mother is most present',
                'category' => 'Durga',
                'place' => 'Kolkata',
                'festival' => 'Durga Ashtami',
                'duration' => 90,
                'description' => <<<'TXT'
Sandhi Puja falls in the 48-minute juncture between Ashtami and Navami — the
moment the shastras say Durga took the form of Chamunda to slay Chanda and
Munda. A puja offered in this window, with 108 lamps and 108 lotuses, is held to
carry the force of the whole Navratri concentrated into one sitting.

Our pandit performs the Sandhi Puja at the exact muhurat: 108 diyas lit
together, 108 lotus offering, bali of ash-gourd or banana in the traditional
manner, Chandi path and havan, all in your name and gotra. The aarti at the
close is performed holding your sankalpa.

You receive the recorded video of the lamp offering and havan on WhatsApp, and
prasad is sent to your address.
TXT,
                'benefits' => [
                    'Performed in the exact Sandhi muhurat' => 'The puja is timed to the 48-minute juncture between Ashtami and Navami.',
                    '108 lamps and 108 lotuses' => 'Both are offered together in your name in the traditional Sandhi manner.',
                    'Chandi path and havan' => 'Recited and offered for the decisive removal of a problem that has not moved.',
                    'For a situation you have been avoiding' => 'The Chamunda form is invoked for the resolve to face it.',
                    'Video and prasad delivered' => 'The lamp offering and havan are filmed and prasad is couriered to you.',
                ],
            ],
            [
                'title' => 'Vijayadashami Aparajita Puja & Shastra Puja',
                'subtitle' => 'Start what you have been putting off, on the day of certain victory',
                'category' => 'Durga',
                'place' => 'Kullu, Himachal Pradesh',
                'festival' => 'Dussehra',
                'duration' => 60,
                'description' => <<<'TXT'
Vijayadashami is the day Rama defeated Ravana and Durga defeated Mahishasura —
by tradition the single most auspicious day of the year to begin anything, with
no need to consult a separate muhurat. Aparajita Puja invokes the "undefeated"
form of the Devi, and Shastra/Vahana Puja consecrates the tools of one's work.

Our pandit performs Aparajita Puja in your name and gotra, offers the shami leaf
exchange, and performs a symbolic Shastra Puja consecrating your work — books
and instruments for a student, ledgers for a business, tools for a craftsperson,
named in the sankalpa. A short Vidyarambh is included for children starting
study.

The ritual is recorded and shared on WhatsApp, with shami leaves and prasad
couriered to you.
TXT,
                'benefits' => [
                    'The year\'s most auspicious day' => 'Aparajita Puja is performed on Vijayadashami, when no separate muhurat is needed to begin anything.',
                    'Shastra / Vahana Puja' => 'The tools of your work or study are consecrated by name — books, ledgers, instruments or implements.',
                    'Vidyarambh for a child' => 'A child beginning education is guided through their first letters in your family\'s name.',
                    'Removes hesitation and delay' => 'Invoked for the resolve to act on a decision already made.',
                    'Shami leaves and prasad' => 'Blessed shami leaves and prasad are couriered to your home.',
                ],
            ],

            /* ========================= KARWA CHAUTH ========================= */
            [
                'title' => 'Karwa Chauth Vrat Katha & Puja for Married Couples',
                'subtitle' => 'The fast kept, the katha heard, the puja done in your name',
                'category' => 'Shiva',
                'place' => 'Ujjain',
                'festival' => 'Karwa Chauth',
                'duration' => 60,
                'description' => <<<'TXT'
Karwa Chauth is the nirjala vrat married women keep from sunrise to moonrise for
the long life and wellbeing of their husband. The puja in the evening — Karwa
Mata and Gauri-Ganesh worship, the Vrat Katha, and the offering through the
sieve at moonrise — is its heart.

Where distance, health or work makes the full ritual hard to arrange, our pandit
performs the complete Karwa Chauth puja in your name and your husband's name and
gotra at Ujjain: Gauri-Ganesh sthapana, Karwa Mata puja, the Vrat Katha
recitation, and the moonrise argha. A couple's sankalpa for a long and settled
married life is made on your behalf.

The katha and puja are recorded and shared on WhatsApp, and the blessed karwa,
sindoor and prasad are couriered to reach you before the festival.
TXT,
                'benefits' => [
                    'Complete puja and Vrat Katha' => 'The full Karwa Chauth ritual and the Vrat Katha recitation are performed in your name.',
                    'Couple\'s sankalpa' => 'A joint sankalpa for a long, healthy and harmonious marriage is made for you and your husband.',
                    'Gauri-Ganesh and Karwa Mata worship' => 'Both are worshipped on your behalf in the traditional order.',
                    'Karwa and sindoor delivered' => 'The blessed karwa, sindoor and prasad are couriered to reach you before the festival.',
                    'Recorded katha video' => 'The katha and puja are filmed and shared on WhatsApp.',
                ],
            ],

            /* ======================= DHANTERAS / DIWALI ======================= */
            [
                'title' => 'Dhanteras Lakshmi-Kubera Puja',
                'subtitle' => 'Open the account books of the year to Lakshmi and Kubera',
                'category' => 'Lakshmi',
                'place' => 'Mahalakshmi Temple, Kolhapur',
                'festival' => 'Dhanteras',
                'duration' => 60,
                'description' => <<<'TXT'
Dhanteras opens the Diwali week and is the day the shastras set for Dhan
(wealth) and Arogya (health) — Lakshmi with Kubera, and Dhanvantari the physician
of the gods. Businesses open new ledgers, households buy metal, and a puja done
with your name and gotra is believed to set the tone of the year's finances.

Our pandit at the Kolhapur Mahalakshmi temple performs Lakshmi-Kubera puja in
your name: Shodashopachara puja of Lakshmi, Kubera yantra pujan, Shree Suktam
path, a short Dhanvantari prayer for the family's health, and aarti. A sankalpa
for stability of income and freedom from debt is made on your behalf.

The puja video is shared on WhatsApp within 3-4 days, and a blessed Kubera coin
and prasad are couriered to you.
TXT,
                'benefits' => [
                    'On the shastra-appointed day for wealth' => 'Lakshmi-Kubera puja is performed on Dhanteras, the day set for Dhan and Arogya.',
                    'Shree Suktam and Kubera yantra pujan' => 'Both are recited and offered in your name and gotra.',
                    'Sankalpa for steady income' => 'A prayer for stable income and freedom from debt is made on your behalf.',
                    'Blessed Kubera coin' => 'A consecrated coin and prasad are couriered to your home.',
                    'Recorded HD puja video' => 'Shared on WhatsApp within 3-4 days.',
                ],
                'image_prompt' => 'Goddess Lakshmi idol with gold coins flowing, a Kubera yantra, brass lamps, lotus flowers, account ledgers wrapped in red cloth, marigold garlands, warm Diwali light, photographic, no text',
            ],
            [
                'title' => 'Diwali Lakshmi-Ganesh Puja & Deepdaan',
                'subtitle' => 'The main-night puja, done in your name at the muhurat',
                'category' => 'Lakshmi',
                'place' => 'Mahalakshmi Temple, Kolhapur',
                'festival' => 'Diwali',
                'duration' => 75,
                'description' => <<<'TXT'
The Diwali night Lakshmi-Ganesh puja, performed in the pradosh muhurat, is the
single most widely kept ritual in the Hindu year — the invitation to Lakshmi to
enter and stay in a home for the coming twelve months, with Ganesh worshipped
first so no obstacle stands in her way.

Our pandit performs the complete puja in your name and gotra at the Kolhapur
Mahalakshmi temple at the correct muhurat: Ganesh and Lakshmi sthapana,
Shodashopachara puja, Shree Suktam and Lakshmi Ashtottara, kalash and ledger
(bahi-khata) pujan for those in business, Deepdaan of eleven lamps, and aarti.

You receive the recorded HD video on WhatsApp, and blessed prasad, a silver-
plated Lakshmi-Ganesh coin and a lamp from the temple are couriered to you.
TXT,
                'benefits' => [
                    'Performed at the pradosh muhurat' => 'The Diwali-night Lakshmi-Ganesh puja is timed to the correct evening window.',
                    'Bahi-khata / ledger pujan' => 'Included for those beginning a new business year.',
                    'Shree Suktam and Lakshmi Ashtottara' => 'Recited in your name and gotra.',
                    'Deepdaan of eleven lamps' => 'The lamp offering and aarti are held with your sankalpa.',
                    'Coin, lamp and prasad couriered' => 'A silver-plated Lakshmi-Ganesh coin, a temple lamp and prasad reach your home.',
                ],
                'image_prompt' => 'Diwali altar with Lakshmi and Ganesha idols, rows of clay diyas, a silver kalash, lotus flowers, rangoli, marigold garlands, glowing warm night light, photographic, no text',
            ],

            /* ========================= GOVARDHAN / KRISHNA ========================= */
            [
                'title' => 'Govardhan Puja & Annakut Bhog',
                'subtitle' => 'The mountain Krishna lifted, honoured with a mountain of food',
                'category' => 'Krishna',
                'place' => 'Govardhan, Mathura',
                'festival' => 'Govardhan Puja',
                'duration' => 60,
                'description' => <<<'TXT'
The day after Diwali, Govardhan Puja recalls Krishna lifting Mount Govardhan to
shelter Vrindavan from Indra's storm — the teaching that devotion and nature
provide, not fear of the powerful. An Annakut ("mountain of food") of many
cooked items is offered and then distributed.

Our pandit performs Govardhan and Gau (cow) puja in your name at Govardhan,
Mathura: parikrama sankalpa, Annakut bhog offering, Giriraj puja, and Gau seva
with feed and care sponsored in your name. A sankalpa for the household's
security and sufficiency is made on your behalf.

The Annakut offering and Gau seva are filmed and shared on WhatsApp, and prasad
from the Govardhan bhog is couriered to you.
TXT,
                'benefits' => [
                    'Giriraj puja in your name' => 'Govardhan puja is performed on your behalf at Govardhan, Mathura.',
                    'Gau seva sponsored' => 'Feed and care for cows is arranged in your name as part of the ritual.',
                    'Annakut bhog offered' => 'The mountain of cooked food is offered and then distributed for you.',
                    'Sankalpa for sufficiency' => 'A prayer for the household\'s security, food and sufficiency is made.',
                    'Video and Govardhan prasad' => 'The offering and Gau seva are filmed and prasad is sent home.',
                ],
            ],
            [
                'title' => 'Bhai Dooj Puja for the Brother-Sister Bond',
                'subtitle' => 'Tika, aarti and a prayer for a sibling far away',
                'category' => 'Krishna',
                'place' => 'Yamuna Ghat, Mathura',
                'festival' => 'Bhai Dooj',
                'duration' => 45,
                'description' => <<<'TXT'
Bhai Dooj recalls Yama visiting his sister Yamuna, who applied his tika and fed
him — and Yama's boon that any brother receiving a tika from his sister that day
would be freed from fear of untimely death. For siblings separated by distance,
migration or circumstance, the ritual is often missed entirely.

Our pandit performs the Bhai Dooj puja at the Yamuna ghat in Mathura in the
names of the brother and sister you name: sankalpa, Yama-Yamuna puja, a tika and
aarti offered on the sibling's behalf, and a prayer for the brother's long life
and the bond's endurance.

The puja is recorded and shared on WhatsApp, and blessed roli, akshat and
prasad are couriered to both addresses if you wish.
TXT,
                'benefits' => [
                    'Performed in both siblings\' names' => 'The Bhai Dooj puja is done in the names of the brother and the sister you specify.',
                    'Yama-Yamuna worship' => 'Invoked for the brother\'s long life and protection, as in the original story.',
                    'For siblings who live apart' => 'A way to keep the ritual when distance or migration makes it impossible in person.',
                    'Roli, akshat and prasad' => 'Blessed items are couriered to both homes if you wish.',
                    'Recorded puja video' => 'Shared on WhatsApp.',
                ],
            ],

            /* ======================== DEV DEEPAWALI VARANASI ======================== */
            [
                'title' => 'Dev Deepawali Maha Deepdaan & Kashi Vishwanath Sankalp',
                'subtitle' => 'Light 108 sacred earthen diyas on 84 Varanasi Ghats on Kartik Purnima',
                'category' => 'Shiva',
                'place' => 'Dashashwamedh & Manikarnika Ghats, Varanasi (Kashi)',
                'festival' => 'Dev Deepawali',
                'duration' => 90,
                'description' => <<<'TXT'
Dev Deepawali (Tripurotsav) celebrated on Kartik Purnima in Kashi (Varanasi) is one of
the most divine and magnificent spiritual spectacles on Earth. The shastras state that
Lord Shiva defeated the demon Tripurasura on this night, prompting all the 33 koti gods
to descend from the heavens to illuminate the 84 sacred ghats of Varanasi with millions
of earthen lamps and bathe in the holy Ganges.

Offering Deepdaan (floating lit earthen lamps) on Kartik Purnima at Varanasi washes away
lifetime sins, removes ancestral afflictions, and invokes Mahadev's supreme grace for
peace, wealth, and spiritual liberation (Moksha).

Our certified Vedic Purohit performs the complete ritual at Manikarnika and Dashashwamedh
Ghats in your exact name and gotra:
1. Vedic Sankalp with your family members' names.
2. 108 Sacred Earthen Diyas lit with pure sesame oil and released into the holy Ganga.
3. Ganga Maha Aarti & Rudrashtakam recitation by Vedic priests.
4. Kashi Vishwanath Temple Archana & Bhasma offering.

You receive the full personalized HD ritual video reel of your lamps being released on
the Ganga on WhatsApp within 24 hours, and sanctified Kashi Vishwanath Mahaprasad,
Ganga Jal, and Raksha Sutra are couriered directly to your home.
TXT,
                'benefits' => [
                    'Sankalp on Sacred Varanasi Ghats' => 'Performed with your exact name and gotra at Manikarnika and Dashashwamedh Ghats by verified Kashi Purohits.',
                    '108 Sacred Diya Deepdaan' => '108 earthen oil lamps lit and offered into the sacred Ganga on the most auspicious Kartik Purnima night.',
                    'Lord Shiva & Maa Ganga Blessings' => 'Washes away accumulated karma, brings health, prosperity, and spiritual liberation (Moksha).',
                    'Personalized HD Video on WhatsApp' => 'Watch your family sankalp and diya offering on the illuminated Varanasi Ghats via WhatsApp video.',
                    'Sanctified Kashi Prasad Couriered' => 'Kashi Vishwanath Bhasma, sanctified Ganga Jal, and prasad delivered to your home address.',
                ],
                'image_prompt' => 'Dev Deepawali in Varanasi, 84 ghats illuminated with millions of glowing earthen diyas, grand Dashashwamedh evening Ganga Aarti with brass multi-tier lamps, holy river Ganga reflecting golden flames, temple spires in background, night spiritual atmosphere, photographic, 4k, no text',
            ],
            [
                'title' => 'Chhath Puja Surya Mahaparv Sandhya & Usha Arghya',
                'subtitle' => 'Offer sacred Arghya to Surya Dev and Chhathi Maiya on the holy riverbanks',
                'category' => 'Surya',
                'place' => 'Assi Ghat, Varanasi',
                'festival' => 'Chhath Puja',
                'duration' => 90,
                'description' => <<<'TXT'
Chhath Puja is the ancient Vedic Mahaparv dedicated to Surya Bhagwan (the Sun God)
and Chhathi Maiya (Usha/Pratyusha), celebrating cosmic energy, longevity, prosperity,
and family well-being. It is the only festival where both the setting and rising sun
are revered.

For families living in metros or abroad unable to perform the rigorous 36-hour nirjala
vrat, our certified pandits perform the complete Sandhya Arghya (evening) and Usha Arghya
(morning) with full Shodashopachara rituals in your family's name and gotra at Assi Ghat,
Varanasi.

A traditional bamboo soop with Thekua, seasonal fruits, coconut, and sugarcane is offered
with sacred milk and Ganga jal arghya while chanting the Aditya Hridaya Stotram.

You receive an HD video of the Arghya offering on WhatsApp, and authentic dry prasad
(Thekua, dry fruits) is sanctified and sent to your doorstep.
TXT,
                'benefits' => [
                    'Complete Surya Arghya in Your Gotra' => 'Both Sandhya (sunset) and Usha (sunrise) Arghya offered in your family name at Varanasi Assi Ghat.',
                    'Aditya Hridaya Stotram Recitation' => 'Chanted by Vedic pandits for radiant health, eyesight, vitality, and career elevation.',
                    'Traditional Bamboo Soop Offering' => 'Includes handcrafted Thekua, sugarcane, fresh coconut, and seasonal fruits.',
                    'HD Video of Arghya on WhatsApp' => 'Personalized recording showing your family sankalp and ritual.',
                    'Authentic Chhath Prasad Couriered' => 'Sanctified Thekua and dry prasad delivered to your doorstep.',
                ],
                'image_prompt' => 'Chhath puja ritual at riverbank ghat during golden sunrise, devotee offering milk arghya from brass pot, decorated bamboo soop with thekua sweets, sugarcane stalks, burning incense, warm golden sunlight on river, photographic, no text',
            ],

            /* ============================ VISHNU ============================ */
            [
                'title' => 'Tulsi Vivah Puja for Marriage & Relationship Harmony',
                'subtitle' => 'The wedding of Tulsi and Shaligram, in your family\'s name',
                'category' => 'Vishnu',
                'place' => 'Pandharpur',
                'festival' => 'Tulsi Vivah',
                'duration' => 75,
                'description' => <<<'TXT'
Tulsi Vivah, on the eleventh day of the bright Kartik fortnight, marks the
ceremonial marriage of the Tulsi plant to Vishnu as Shaligram — and the start of
the Hindu wedding season. Families perform it for blessings on a marriage yet to
happen, and for harmony in one already made; it is a classic remedy where a
match keeps being delayed.

Our pandit performs the full Tulsi Vivah at Pandharpur in your name and gotra:
mandap for the Tulsi plant, Shaligram puja, the marriage rites with mangalsutra
and haldi, kanyadaan sankalpa in your family's name, and aarti. Where a specific
person's marriage is the intention, they are named in the sankalpa.

The ceremony is filmed and shared on WhatsApp, and Tulsi leaves, prasad and a
Shaligram-touched thread are couriered to you.
TXT,
                'benefits' => [
                    'Full Tulsi Vivah in your name' => 'The complete ceremony is performed at Pandharpur in your family\'s name and gotra.',
                    'Remedy for marriage delays' => 'The traditional recourse where a match keeps falling through.',
                    'Kanyadaan sankalpa' => 'Made for a named person\'s marriage where that is the intention.',
                    'Tulsi leaves and thread' => 'Blessed Tulsi leaves, prasad and a Shaligram-touched thread are couriered to you.',
                    'Recorded ceremony video' => 'Shared on WhatsApp.',
                ],
            ],
            [
                'title' => 'Satyanarayan Katha & Puja for the Home',
                'subtitle' => 'The thanksgiving puja for a new house, job, child or vehicle',
                'category' => 'Vishnu',
                'place' => 'Badrinath Dham',
                'festival' => null,
                'weekday' => 5,
                'duration' => 90,
                'description' => <<<'TXT'
The Satyanarayan Vrat Katha is the puja Hindu households turn to at every
threshold — moving into a home, a promotion, a child's birth, a new vehicle, a
debt cleared — a way of acknowledging that the good outcome was given, not only
earned. It is simple, universally accepted, and needs no special muhurat beyond
a Purnima or an auspicious day.

Our pandit performs the full puja in your name and gotra at Badrinath: kalash
and Ganesh sthapana, Navagraha and Panchdev puja, the five chapters of the
Satyanarayan Katha, sheera/panchamrit bhog, and aarti. Your reason for the puja
— named or unspoken — is carried in the sankalpa.

The katha and aarti are recorded and shared on WhatsApp, and sheera prasad and
tirth from Badrinath are couriered to you.
TXT,
                'benefits' => [
                    'The accepted threshold puja' => 'The one Hindu households use for a new home, job, child, vehicle or a debt cleared.',
                    'Complete five-chapter Katha' => 'All five chapters of the Satyanarayan Katha are recited in your name.',
                    'Navagraha and Panchdev puja included' => 'Added for all-round shanti alongside the main ritual.',
                    'Sheera prasad and Badrinath tirth' => 'Couriered to your home.',
                    'Recorded katha video' => 'The katha and aarti are filmed and shared on WhatsApp.',
                ],
            ],

            /* ========================= SURYA / SANKRANTI ========================= */
            [
                'title' => 'Makar Sankranti Surya Puja & Til-Gud Daan',
                'subtitle' => 'As the sun turns north, a puja and daan at the Sangam',
                'category' => 'Surya',
                'place' => 'Triveni Sangam, Prayagraj',
                'festival' => 'Makar Sankranti',
                'duration' => 60,
                'description' => <<<'TXT'
Makar Sankranti is the day the sun enters Capricorn and begins its northward
course — Uttarayan, considered the bright half of the year and an especially
potent time for daan (charitable giving), snana (holy bathing) and Surya
worship. At Prayagraj the Sangam bath on this day draws millions.

Our pandit performs Surya Puja and Arghya at the Triveni Sangam in your name and
gotra: Aditya Hridaya Stotra path, Surya Namaskar sankalpa, and til-gud, khichdi
and blanket daan made on your behalf to those who gather at the ghats. A
sankalpa for health, vitality and clarity of purpose is offered.

The arghya and daan are filmed and shared on WhatsApp, and til-gud prasad and
Sangam jal are couriered to you.
TXT,
                'benefits' => [
                    'Surya Arghya at the Triveni Sangam' => 'Performed in your name at Prayagraj on the day of Uttarayan.',
                    'Aditya Hridaya Stotra' => 'Recited for health, vitality and clarity of purpose.',
                    'Til-gud, khichdi and blanket daan' => 'Charitable giving is completed on your behalf at the ghats.',
                    'Sangam jal couriered' => 'Along with til-gud prasad, sent to your home.',
                    'Recorded arghya video' => 'The offering and daan are filmed and shared on WhatsApp.',
                ],
            ],

            /* ============================ SARASWATI ============================ */
            [
                'title' => 'Vasant Panchami Saraswati Puja & Vidya Aarambh',
                'subtitle' => 'The day to begin learning — for a child or for yourself',
                'category' => 'Saraswati',
                'place' => 'Basar, Telangana',
                'festival' => 'Vasant Panchami',
                'duration' => 60,
                'description' => <<<'TXT'
Vasant Panchami is the day of Saraswati — the deity of speech, learning, music
and the arts — and by tradition the most auspicious day of the year for
Vidyarambh, a child's first writing, and for anyone beginning a course of study,
an instrument or a creative discipline.

Our pandit performs Saraswati Puja at the Gnana Saraswati temple in Basar in
your name and gotra: Saraswati sthapana on white cloth, Saraswati Vandana and
the 108 names, pen/book and instrument pujan, and for a child, the Aksharabhyasam
— guiding the first letters in rice with the sankalpa in your family's name.

The puja and, where applicable, the Aksharabhyasam are recorded and shared on
WhatsApp, and blessed saffron, a slate pencil and prasad are couriered to you.
TXT,
                'benefits' => [
                    'The year\'s best day to begin learning' => 'Saraswati Puja is performed on Vasant Panchami in your name and gotra.',
                    'Aksharabhyasam for a child' => 'A child\'s first letters are guided in rice with a sankalpa in your family\'s name.',
                    'Pen, book and instrument pujan' => 'For students, writers and musicians beginning something new.',
                    'For exam anxiety and creative block' => 'Traditionally invoked against mental fog and stalled work.',
                    'Saffron, slate pencil and prasad' => 'Couriered to your home.',
                ],
                'image_prompt' => 'Goddess Saraswati idol in white seated on a lotus with a veena, white flowers, an open book and pen, a plate of rice for writing first letters, soft morning light, photographic, no text',
            ],

            /* ============================ SHIVA ============================ */
            [
                'title' => 'Maha Shivratri Rudrabhishek & Night-Long Jaap',
                'subtitle' => 'The one night Shiva is closest — abhishek through all four prahars',
                'category' => 'Shiva',
                'place' => 'Kashi Vishwanath, Varanasi',
                'festival' => 'Maha Shivratri',
                'duration' => 150,
                'description' => <<<'TXT'
Maha Shivratri is the night the shastras say the formless Shiva is most
accessible, and the abhishek performed across its four prahars — with water,
milk, curd, honey, ghee and finally bel patra — is held to be worth a year of
ordinary worship. It is the central remedy for Shani, Rahu-Ketu and health
afflictions in the horoscope.

Our pandits at Kashi Vishwanath perform a full Rudrabhishek in your name and
gotra with the Rudri path (Namakam-Chamakam), maha-mrityunjaya japa through the
night, bel patra archana, and the prahar-wise abhishek. A sankalpa for health,
release from a long-standing fear, and Shani-Rahu shanti is made on your behalf.

The abhishek is recorded and shared on WhatsApp, and bhasma, bel patra and
Ganga jal from the abhishek are couriered to you.
TXT,
                'benefits' => [
                    'Abhishek across all four prahars' => 'A full night Rudrabhishek at Kashi Vishwanath, worth a year of ordinary worship by tradition.',
                    'Night-long Maha Mrityunjaya japa' => 'Chanted through the night in your name and gotra.',
                    'Central Shani and Rahu-Ketu remedy' => 'Also invoked for chronic health afflictions in the horoscope.',
                    'Rudri path (Namakam-Chamakam)' => 'Recited on your behalf during the abhishek.',
                    'Bhasma, bel patra and Ganga jal' => 'From the abhishek, couriered to your home.',
                ],
                'image_prompt' => 'A Shiva lingam under a stream of milk and water abhishekam, bel patra leaves, white flowers, brass vessels, rudraksha, sacred ash, oil lamps in a dark temple, photographic, no text',
            ],
            [
                'title' => 'Monday Rudrabhishek at Kashi for Health & Obstacles',
                'subtitle' => 'The classic weekly Shiva remedy, done at Varanasi in your name',
                'category' => 'Shiva',
                'place' => 'Kashi Vishwanath, Varanasi',
                'festival' => null,
                'weekday' => 1,
                'duration' => 90,
                'description' => <<<'TXT'
Rudrabhishek — the abhishek of the Shiva lingam accompanied by the Rudri path —
is the most requested single puja in the Hindu tradition, performed for health,
for the removal of an obstacle that will not move, and as a remedy for Shani
sade-sati, Kaal Sarp and Pitru Dosh. Monday, Shiva's day, carries added weight.

Our pandits at Kashi Vishwanath perform the abhishek in your name and gotra with
panchamrit (milk, curd, ghee, honey, sugar) and Ganga jal, the Rudri path,
bel patra archana with the 108 names, and a maha-mrityunjaya japa. Your specific
intention is carried in the sankalpa.

You receive the recorded HD abhishek video on WhatsApp within 3-4 days, and
bhasma, bel patra and Ganga jal are couriered to your home.
TXT,
                'benefits' => [
                    'Abhishek with panchamrit and Ganga jal' => 'Performed at Kashi Vishwanath on Monday, Shiva\'s day.',
                    'Remedy for Shani sade-sati and Kaal Sarp' => 'Also used for chronic illness and a persistent obstacle.',
                    'Rudri path and Maha Mrityunjaya japa' => 'Recited in your name and gotra.',
                    'Recorded HD abhishek video' => 'Shared on WhatsApp within 3-4 days.',
                    'Bhasma, bel patra and Ganga jal' => 'Couriered to your home.',
                ],
            ],
            [
                'title' => 'Maha Mrityunjaya Jaap for Health & Protection',
                'subtitle' => '1.25 lakh mantra japa with havan for recovery and long life',
                'category' => 'Shiva',
                'place' => 'Mahakaleshwar, Ujjain',
                'festival' => null,
                'in_days' => 9,
                'duration' => 120,
                'description' => <<<'TXT'
The Maha Mrityunjaya mantra is the tradition's foremost prayer for healing,
protection from untimely death, and recovery from serious illness or accident.
Performed as an anushthan — a fixed count of japa (commonly 1.25 lakh) followed
by a havan with a tenth of that count as ahutis — it is the puja families
arrange when someone is unwell or a horoscope shows a difficult transit.

Our pandits at Mahakaleshwar, Ujjain — one of the twelve Jyotirlingas and the
seat associated with victory over death — perform the anushthan in the name and
gotra of the person you name: sankalpa, Mrityunjaya kavach, the japa over the
prescribed days, and the concluding havan with bel, durva and herbs.

The concluding havan is recorded and shared on WhatsApp, and a Mrityunjaya
yantra, bhasma and prasad from Mahakaleshwar are couriered to you.
TXT,
                'benefits' => [
                    '1.25 lakh japa with havan' => 'The full anushthan is performed in the patient\'s name and gotra.',
                    'At Mahakaleshwar, Ujjain' => 'The Jyotirlinga associated with victory over death.',
                    'Support through illness or surgery' => 'Also used for a difficult planetary transit in the horoscope.',
                    'Mrityunjaya kavach and yantra' => 'Energised on your behalf during the anushthan.',
                    'Havan video, yantra and prasad' => 'Sent to your home.',
                ],
            ],

            /* ============================ HANUMAN ============================ */
            [
                'title' => 'Tuesday Sundarkand Path for Courage & Obstacles',
                'subtitle' => 'The one chapter of the Ramayana that is all success',
                'category' => 'Hanuman',
                'place' => 'Sankat Mochan, Varanasi',
                'festival' => null,
                'weekday' => 2,
                'duration' => 150,
                'description' => <<<'TXT'
The Sundarkand is the fifth book of Tulsidas's Ramcharitmanas — the account of
Hanuman crossing the ocean, finding Sita and burning Lanka. It is the only kand
in which nothing goes wrong, and for that reason it is recited on its own, on
Tuesdays and Saturdays, for courage, for a task that feels impossible, and for
protection from fear and ill-will.

Our pandits at the Sankat Mochan temple in Varanasi perform a complete
Sundarkand path in your name and gotra with Hanuman Chalisa, Bajrang Baan, a
sindoor-chola offering to Hanuman, and aarti. Where there is a specific
obstacle — a court case, a stalled career move, a health scare — it is named in
the sankalpa.

The path and aarti are recorded and shared on WhatsApp, and sindoor, a chola
thread and boondi prasad from Sankat Mochan are couriered to you.
TXT,
                'benefits' => [
                    'Complete Sundarkand path' => 'Recited in your name at the Sankat Mochan temple in Varanasi.',
                    'Hanuman Chalisa and Bajrang Baan' => 'Added for courage and protection.',
                    'For court cases and stalled work' => 'The traditional recourse when a task feels impossible.',
                    'Sindoor-chola offering' => 'Made to Hanuman on your behalf.',
                    'Sindoor, thread and boondi prasad' => 'Couriered to your home.',
                ],
                'image_prompt' => 'Lord Hanuman idol coated in orange sindoor with a silver mace, a small chola cloth, boondi laddus, marigold garlands, oil lamps, an open Ramayana, warm temple light, photographic, no text',
            ],
            [
                'title' => 'Ram Navami Puja & Akhand Ramayan Path',
                'subtitle' => 'The birth of Rama, marked with a continuous reading',
                'category' => 'Hanuman',
                'place' => 'Ram Janmabhoomi, Ayodhya',
                'festival' => 'Ram Navami',
                'duration' => 120,
                'description' => <<<'TXT'
Ram Navami marks the birth of Rama at noon on the ninth day of Chaitra. Families
mark it with an Akhand Path — a continuous recitation of the Ramcharitmanas — and
a midday puja at the moment of the birth, for dharma in the household, harmony
between its members, and the strength to hold to what is right under pressure.

Our pandits at Ayodhya perform the Ram Janmotsav puja at the noon muhurat in
your name and gotra: Ram Darbar puja (Rama, Sita, Lakshmana, Hanuman), the
birth aarti, Ram Raksha Stotra, and a Sundarkand or Ramayan path as the tier
allows. A sankalpa for unity and right conduct in the family is made on your
behalf.

The noon aarti and path are recorded and shared on WhatsApp, and charnamrit,
tulsi and prasad from Ayodhya are couriered to you.
TXT,
                'benefits' => [
                    'Ram Janmotsav at the noon muhurat' => 'The puja is performed at the moment of Rama\'s birth in Ayodhya.',
                    'Ram Raksha Stotra and Ramayan path' => 'Recited in your name and gotra.',
                    'Sankalpa for family unity' => 'A prayer for harmony and right conduct in the household.',
                    'Ram Darbar puja' => 'Rama, Sita, Lakshmana and Hanuman are worshipped together on your behalf.',
                    'Charnamrit, tulsi and Ayodhya prasad' => 'Couriered to your home.',
                ],
            ],

            /* =============== NAVAGRAHA & DOSHA NIVARAN =============== */
            [
                'title' => 'Navagraha Shanti Puja & Havan',
                'subtitle' => 'Settle all nine planets before they set the year\'s tone',
                'category' => 'Navagraha & Dosha Nivaran',
                'place' => 'Navagraha Temple, Guwahati',
                'festival' => null,
                'in_days' => 12,
                'duration' => 120,
                'description' => <<<'TXT'
Navagraha Shanti is the general planetary remedy — a puja and havan addressing
all nine grahas together, used when a horoscope shows several afflictions at
once, at the start of a mahadasha, before a major undertaking, or simply once a
year as maintenance. It is the puja an astrologer recommends when no single
planet is the whole problem.

Our pandits at the Navagraha temple in Guwahati perform the full Navagraha
Shanti in your name, gotra and birth details: sthapana of the nine grahas,
japa of each graha's beej mantra to the prescribed count, offering of the nine
grains, cloths and woods, and a havan with graha-wise ahutis. If you share your
birth chart, the pandit weights the japa toward the afflicted planets.

The havan is recorded and shared on WhatsApp, and a Navagraha yantra, the nine
grains and prasad are couriered to you.
TXT,
                'benefits' => [
                    'All nine planets in one ritual' => 'Puja and havan for every graha, in your name and birth details.',
                    'Japa weighted to your chart' => 'Concentrated on the afflicted planets when you share your birth chart.',
                    'For a mahadasha or major undertaking' => 'The general remedy when no single planet is the whole problem.',
                    'Navagraha yantra energised' => 'And couriered to your home.',
                    'Recorded havan video' => 'Shared on WhatsApp.',
                ],
            ],
            [
                'title' => 'Mangal Dosh (Manglik) Nivaran Puja',
                'subtitle' => 'For marriage delays and friction traced to Mars',
                'category' => 'Navagraha & Dosha Nivaran',
                'place' => 'Mangalnath Temple, Ujjain',
                'festival' => null,
                'weekday' => 2,
                'duration' => 90,
                'description' => <<<'TXT'
Mangal Dosh — the Manglik condition — is Mars placed in the 1st, 4th, 7th, 8th
or 12th house, and is the affliction most often cited for a marriage match that
keeps breaking down, or for friction and health strain early in a marriage.
Mangalnath in Ujjain is regarded as the birthplace of Mars and the foremost
place for its remedy.

Our pandits at Mangalnath perform the Mangal Dosh Nivaran in your name, gotra
and birth details: Mangal graha sthapana and abhishek, Mangal beej mantra japa
(commonly 10,000), red masoor, red cloth and copper daan, and a havan. For a
match under consideration, both persons can be named in the sankalpa.

The havan is recorded and shared on WhatsApp, and a Mangal yantra, coral-
touched thread and prasad are couriered to you.
TXT,
                'benefits' => [
                    'At Mangalnath, the birthplace of Mars' => 'The foremost place for the Mangal Dosh remedy.',
                    'For marriage delays and early friction' => 'Addresses the affliction most often cited when a match keeps breaking down.',
                    'Mangal beej mantra japa' => 'To the prescribed count in your name, with a concluding havan.',
                    'Both persons named' => 'For a match under consideration, both can be included in the sankalpa.',
                    'Mangal yantra and coral thread' => 'Couriered to your home.',
                ],
            ],
            [
                'title' => 'Kaal Sarp Dosh Nivaran Puja at Trimbakeshwar',
                'subtitle' => 'For the sense that effort never quite converts to result',
                'category' => 'Navagraha & Dosha Nivaran',
                'place' => 'Trimbakeshwar, Nashik',
                'festival' => null,
                'in_days' => 16,
                'duration' => 150,
                'description' => <<<'TXT'
Kaal Sarp Dosh — all seven planets held between Rahu and Ketu in the chart — is
associated with a life of hard work that keeps falling short at the last step,
sudden reversals, and disturbed sleep. Trimbakeshwar, at the source of the
Godavari and one of the twelve Jyotirlingas, is the traditional place for its
remedy.

Our pandits at Trimbakeshwar perform the full Kaal Sarp Shanti in your name,
gotra and birth details: sankalpa, Rahu-Ketu and Nag-Nagin puja, silver serpent
offering, Rudrabhishek of the Jyotirlinga, Sarpasukta and Rahu-Ketu mantra japa,
and a havan. A Nag pratima is released in flowing water at the close.

The Rudrabhishek and havan are recorded and shared on WhatsApp, and a silver
serpent token, bhasma and Godavari jal are couriered to you.
TXT,
                'benefits' => [
                    'Full Kaal Sarp Shanti at Trimbakeshwar' => 'Performed at the Jyotirlinga at the source of the Godavari.',
                    'For stalled progress and reversals' => 'Addresses the pattern of effort falling short at the last step.',
                    'Rahu-Ketu and Nag-Nagin puja' => 'With a Rudrabhishek of the Jyotirlinga in your name.',
                    'Sarpasukta and Rahu-Ketu japa' => 'Chanted on your behalf, with a concluding havan.',
                    'Silver serpent token and Godavari jal' => 'Couriered to your home.',
                ],
            ],

            /* ===================== EVERGREEN LAKSHMI ===================== */
            [
                'title' => 'Friday Lakshmi Puja for Wealth & Stability',
                'subtitle' => 'The weekly Shree Suktam puja, done at Kolhapur in your name',
                'category' => 'Lakshmi',
                'place' => 'Mahalakshmi Temple, Kolhapur',
                'festival' => null,
                'weekday' => 5,
                'duration' => 60,
                'description' => <<<'TXT'
Friday is Lakshmi's day, and a regular Friday puja — Shree Suktam with a
kanakadhara prayer — is the tradition's steady, non-dramatic remedy for money
that comes but does not stay, income that will not rise, and the anxiety that
comes with both. It is meant to be repeated, monthly or quarterly, as
maintenance rather than rescue.

Our pandit at the Kolhapur Mahalakshmi temple performs the puja in your name and
gotra: Shodashopachara puja of Lakshmi, Shree Suktam and Kanakadhara Stotram,
kamalgatta mala japa, and aarti. A sankalpa for stable income, controlled
expenses and freedom from debt is made on your behalf.

You receive the recorded HD puja video on WhatsApp within 3-4 days, and a
blessed kamalgatta bead, kesar and prasad are couriered to you.
TXT,
                'benefits' => [
                    'Weekly Shree Suktam and Kanakadhara puja' => 'Performed at Kolhapur Mahalakshmi on Friday, Lakshmi\'s day.',
                    'For income that will not rise' => 'The steady remedy for money that comes but does not stay.',
                    'Kamalgatta mala japa' => 'Chanted in your name and gotra.',
                    'Recorded HD puja video' => 'Shared on WhatsApp within 3-4 days.',
                    'Kamalgatta bead, kesar and prasad' => 'Couriered to your home.',
                ],
                'image_prompt' => 'Goddess Lakshmi seated on a pink lotus with gold coins, a brass kalash, lotus-seed prayer beads, yellow marigold and lotus flowers, oil lamps, calm warm light, photographic, no text',
            ],
        ];
    }
}
