<?php

namespace Modules\DayCountCalculator\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    private array $conventions = [
        '30-360-us',
        '30-360-bond-basis',
        '30e-360',
        '30e-360-isda',
        'actual-360',
        'actual-364',
        'actual-365',
        'actual-actual-icma',
        'actual-actual-isda',
    ];

    public function index(): Response
    {
        $urls = $this->buildUrls();

        $xml = $this->generateXml($urls);

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function buildUrls(): array
    {
        $urls = [
            ['loc' => url('/calculator'), 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => url('/comparison'), 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => url('/calculator/history'), 'priority' => '0.4', 'changefreq' => 'never'],
        ];

        foreach ($this->conventions as $convention) {
            $urls[] = [
                'loc' => url("/calculator/learn/{$convention}"),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ];
        }

        return $urls;
    }

    private function generateXml(array $urls): string
    {
        $lastmod = now()->toAtomString();

        $items = '';
        foreach ($urls as $url) {
            $items .= "\n    <url>";
            $items .= "\n        <loc>" . e($url['loc']) . "</loc>";
            $items .= "\n        <lastmod>{$lastmod}</lastmod>";
            $items .= "\n        <changefreq>{$url['changefreq']}</changefreq>";
            $items .= "\n        <priority>{$url['priority']}</priority>";
            $items .= "\n    </url>";
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            . "\n" . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            . $items
            . "\n" . '</urlset>';
    }
}
