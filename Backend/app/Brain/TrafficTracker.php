<?php

namespace App\Brain;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

/**
 * First-party traffic truth until GA4 is connected.
 * Records pageviews excluding admin/API/assets and obvious bots,
 * into a daily cache counter that goal:snapshot persists.
 */
class TrafficTracker
{
    protected const SKIP_PREFIXES = [
        'admin', 'api', 'astrologer', 'livewire', 'vendor', 'build',
        'assets', 'images', 'storage', 'frontend', 'sound', 'kundli',
        'robots.txt', 'sitemap.xml', 'favicon.ico', 'ping.txt',
    ];

    public function handle(Request $request, Closure $next)
    {
        $path = ltrim($request->path(), '/');

        foreach (self::SKIP_PREFIXES as $skip) {
            if ($path === $skip || str_starts_with($path, $skip.'/') || str_starts_with($path, $skip)) {
                return $next($request);
            }
        }

        $ua = (string) $request->userAgent();

        if (preg_match('/bot|crawl|spider|slurp|curl|wget|python|monitor|uptime|pingdom|headless/i', $ua)) {
            return $next($request);
        }

        if ($request->isMethod('GET')) {
            $key = 'traffic.pv.'.now()->toDateString();
            Cache::increment($key);
            Cache::put($key, Cache::get($key, 1), now()->addDays(8));
        }

        return $next($request);
    }

    public static function today(): int
    {
        return (int) Cache::get('traffic.pv.'.now()->toDateString(), 0);
    }

    /** Pull yesterday’s counter before it expires (call from nightly snapshot). */
    public static function take(string $date): int
    {
        $v = (int) Cache::get('traffic.pv.'.$date, 0);
        Cache::forget('traffic.pv.'.$date);

        return $v;
    }
}
