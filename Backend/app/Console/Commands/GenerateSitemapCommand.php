<?php

namespace App\Console\Commands;

use App\Models\AstrologerModel\Blog;
use App\Models\Puja;
use App\Models\PujaCategory;
use Illuminate\Console\Command;
use Carbon\Carbon;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate an updated search-engine friendly XML sitemap for Google and Bing';

    public function handle(): int
    {
        $this->info('Generating XML Sitemap for OnlinePuja...');

        $baseUrl = rtrim(config('app.url', 'https://onlinepuja.live'), '/');
        $xml = [];
        $xml[] = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
        $xml[] = '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
        $xml[] = '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9';
        $xml[] = '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

        // 1. Static Core Landing Pages
        $staticRoutes = [
            ['url' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
            ['url' => $baseUrl . '/puja/category/list', 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => $baseUrl . '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => $baseUrl . '/panchang', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['url' => $baseUrl . '/horoscope', 'priority' => '0.7', 'changefreq' => 'daily'],
            ['url' => $baseUrl . '/kundali', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => $baseUrl . '/kundali-matching', 'priority' => '0.7', 'changefreq' => 'weekly'],
            ['url' => $baseUrl . '/about-us', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => $baseUrl . '/contact', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['url' => $baseUrl . '/terms-condition', 'priority' => '0.3', 'changefreq' => 'yearly'],
            ['url' => $baseUrl . '/privacy-policy', 'priority' => '0.3', 'changefreq' => 'yearly'],
        ];

        foreach ($staticRoutes as $r) {
            $xml[] = $this->buildUrlXml($r['url'], now()->toAtomString(), $r['changefreq'], $r['priority']);
        }

        // 2. Dynamic Pujas
        $pujas = Puja::where('puja_status', 1)->whereNotNull('slug')->get();
        $this->line("Adding {$pujas->count()} active Pujas...");
        foreach ($pujas as $puja) {
            $loc = $baseUrl . '/puja-details/' . rawurlencode($puja->slug);
            $lastmod = ($puja->updated_at ?? now())->toAtomString();
            $xml[] = $this->buildUrlXml($loc, $lastmod, 'weekly', '0.9');
        }

        // 3. Dynamic Blogs
        $blogs = Blog::where('isActive', 1)->whereNotNull('slug')->get();
        $this->line("Adding {$blogs->count()} published Blog articles...");
        foreach ($blogs as $blog) {
            $loc = $baseUrl . '/blog/' . rawurlencode($blog->slug);
            $lastmod = ($blog->updated_at ?? $blog->created_at ?? now())->toAtomString();
            $xml[] = $this->buildUrlXml($loc, $lastmod, 'weekly', '0.8');
        }

        $xml[] = '</urlset>';

        $outputPath = public_path('sitemap.xml');
        file_put_contents($outputPath, implode("\n", $xml));

        $totalUrls = count($staticRoutes) + $pujas->count() + $blogs->count();
        $this->info("✓ Sitemap successfully generated at: {$outputPath}");
        $this->info("✓ Total URLs in sitemap: {$totalUrls}");

        return self::SUCCESS;
    }

    private function buildUrlXml(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n" .
               "    <loc>" . htmlspecialchars($loc, ENT_QUOTES, 'UTF-8') . "</loc>\n" .
               "    <lastmod>{$lastmod}</lastmod>\n" .
               "    <changefreq>{$changefreq}</changefreq>\n" .
               "    <priority>{$priority}</priority>\n" .
               "  </url>";
    }
}
