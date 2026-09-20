<?php

namespace App\Brain;

/**
 * Goal arithmetic engine — no promises, just maths.
 *
 * Mirrors the vm-seo-brain philosophy: show the implied velocity FIRST,
 * name the levers that close the gap, and flag when a target outruns
 * organic reality instead of pretending.
 */
class GoalPlanner
{
    /** Assumptions are explicit so they can be tuned, not hidden. */
    public const ASSUMPTIONS = [
        'visits_per_post_horizon' => 60,   // avg lifetime visits one SEO post brings in `days`
        'social_reach_per_post'   => 250,  // avg reach per social post, account < 5k followers
        'social_ctr_to_site'      => 0.01, // 1% of reach clicks through
        'harvest_cap_share'       => 0.20, // striking-distance fixes can lift baseline up to +20%/day
        'content_ramp_monthly'    => 1.55, // compounding factor per ~30d for a posting cadence
        'max_organic_posts_day'   => 3,    // sustainable without an editorial team
    ];

    /**
     * @param array{title:string,target:int,days:int,baseline_daily:int,channels?:array} $g
     */
    public static function plan(array $g): array
    {
        $a = self::ASSUMPTIONS;
        $days = max(1, (int) $g['days']);
        $baseline = max(0, (int) ($g['baseline_daily'] ?? 0));

        $baselineTotal = $baseline * $days;
        $gap = max(0, $g['target'] - $baselineTotal);
        $requiredAvgDaily = (int) ceil($gap / $days);

        // Channel split (weights adjustable per goal).
        $w = array_merge([
            'seo_harvest' => 0.20,   // CTR/striking-distance fixes on existing pages
            'content_seo' => 0.35,   // new compounding content
            'social'      => 0.25,   // social distribution referral
            'ai_search'   => 0.08,   // AI search surfaces (ChatGPT/Perplexity citations)
            'referral'    => 0.12,   // partnerships, directories, email
        ], $g['channels'] ?? []);

        foreach ($w as &$x) {
            $x = round($x, 4);
        }
        unset($x);

        // Implied velocities per channel.
        $implied = [
            'seo_harvest_daily_uplift' => (int) ceil($gap * $w['seo_harvest'] / $days),
            'social_reach_needed'      => (int) ceil(($gap * $w['social']) / max(0.001, $a['social_ctr_to_site'])),
            'ai_search_daily'          => (int) ceil($gap * $w['ai_search'] / $days),
            'referral_daily'           => (int) ceil($gap * $w['referral'] / $days),
        ];

        $postsForContentShare = (int) ceil(
            ($gap * $w['content_seo']) / max(1, $a['visits_per_post_horizon'])
        );
        $postsPerDay = round($postsForContentShare / $days, 2);
        $socialPostsNeeded = (int) ceil($implied['social_reach_needed'] / max(1, $a['social_reach_per_post']));
        $socialPostsDay = round($socialPostsNeeded / $days, 2);

        // Verdict against sustainable capacity.
        $verdict = 'feasible';
        $levers = [];

        if ($postsPerDay > $a['max_organic_posts_day']) {
            $verdict = 'unrealistic_without_leverage';
            $levers[] = 'Content velocity alone cannot carry this: need '.round($postsPerDay, 1).
                ' posts/day but sustainable ceiling is '.$a['max_organic_posts_day'].'. Add short-form video distribution or paid amplification.';
        } elseif ($postsPerDay > $a['max_organic_posts_day'] * 0.66) {
            $verdict = 'stretch';
        }

        if ($socialPostsDay > 8) {
            $verdict = $verdict === 'feasible' ? 'stretch' : $verdict;
            $levers[] = 'Social needs '.$socialPostsDay.' posts/day — repurpose every blog into 3+ social cuts automatically.';
        }

        if ($baseline === 0 && $verdict === 'feasible') {
            $verdict = 'stretch'; // zero baseline + 100k in 50d is never plain-feasible
            $levers[] = 'Baseline is zero: expect the classic lag — weeks 1–3 harvest almost nothing while indexing catches up. The curve is back-loaded.';
        }

        // Day-by-day projected curve (back-loaded, compounding content).
        $curve = [];
        $ramp = pow($a['content_ramp_monthly'], 1 / 30);
        for ($d = 1; $d <= $days; $d++) {
            $contentPart = ($gap * $w['content_seo']) * (pow($ramp, $d) - 1) / (pow($ramp, $days) - 1 + 0.0001);
            $harvestPart = ($gap * $w['seo_harvest']) * min(1, $d / 14);          // captured in first 2 weeks
            $linearPart = (($gap * $w['social']) + ($gap * $w['referral']) + ($gap * $w['ai_search'])) * ($d / $days);
            $curve[$d] = (int) round($baseline + ($contentPart + $harvestPart + $linearPart) / $days);
        }

        return [
            'days' => $days,
            'target' => $g['target'],
            'baseline_daily' => $baseline,
            'gap' => $gap,
            'required_avg_daily' => $requiredAvgDaily,
            'channel_weights' => $w,
            'implied' => $implied + [
                'content_posts_needed' => $postsForContentShare,
                'content_posts_per_day' => $postsPerDay,
                'social_posts_needed' => $socialPostsNeeded,
                'social_posts_per_day' => $socialPostsDay,
            ],
            'assumptions' => $a,
            'verdict' => $verdict,
            'levers' => $levers,
            'projected_curve' => $curve,
        ];
    }
}
