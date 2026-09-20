<?php

namespace App\Brain;

/**
 * Pre-queue quality gate — mirrors vm-social-ai's Critic.
 * Catches stock-AI filler before it can ship.
 */
class Critic
{
    protected const STOCK_PHRASES = [
        'in today\'s fast-paced world', 'delve into', 'tapestry', 'embark on a journey',
        'unlock the power', 'game-changer', 'revolutionize', 'it is important to note',
        'in conclusion', 'furthermore,', 'moreover,', 'navigating the', 'realm of',
        'testament to', 'elevate your', 'seamlessly', 'harness the power', 'dive into',
        'treasure trove', 'myriad of', 'beacon of',
    ];

    protected const MIN_CHARS = 4500;

    public static function lint(string $html, ?string $keyword): array
    {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        $failures = [];
        $lower = mb_strtolower($text);

        foreach (self::STOCK_PHRASES as $p) {
            if (str_contains($lower, $p)) {
                $failures[] = "stock phrase: '{$p}'";
            }
        }

        $chars = mb_strlen($text);

        if ($chars < self::MIN_CHARS) {
            $failures[] = "too short: {$chars} chars (min ".self::MIN_CHARS.')';
        }

        if ($keyword) {
            $sig = preg_split('/\s+/', mb_strtolower($keyword));
            $sig = array_slice(array_filter($sig, fn ($w) => mb_strlen($w) > 3), 0, 3);
            $misses = array_filter($sig, fn ($w) => ! str_contains($lower, $w));

            if ($misses) {
                $failures[] = 'keyword parts missing: '.implode(', ', $misses);
            }
        }

        if (substr_count(mb_strtolower($html), '<h2>') < 2) {
            $failures[] = 'structure: fewer than 2 H2 sections';
        }

        if (preg_match('/<html|<body|<!DOCTYPE/i', $html)) {
            $failures[] = 'fragment violation: full document tags present';
        }

        return ['pass' => ! $failures, 'failures' => $failures, 'chars' => $chars];
    }
}
