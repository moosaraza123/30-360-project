<?php

namespace Modules\DayCountCalculator\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $xml = $this->generateXml($this->buildUrls());

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function buildUrls(): array
    {
        $viewsPath = module_path('DayCountCalculator', 'resources/views');

        $urls = [
            [
                'loc' => url('/calculator'),
                'priority' => '1.0',
                'changefreq' => 'weekly',
                'lastmod' => $this->fileLastmod("{$viewsPath}/calculator/index.blade.php"),
            ],
            [
                'loc' => url('/comparison'),
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'lastmod' => $this->fileLastmod("{$viewsPath}/comparison/index.blade.php"),
            ],
        ];

        $educationalLastmod = $this->fileLastmod("{$viewsPath}/educational/convention.blade.php");

        foreach (config('daycountcalculator.conventions', []) as $convention) {
            $urls[] = [
                'loc' => route('calculator.educate', $convention['slug']),
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'lastmod' => $educationalLastmod,
            ];
        }

        return $urls;
    }

    /**
     * lastmod derived from the view file's modification time — changes only
     * when the page content actually changes (unlike the previous now()).
     */
    private function fileLastmod(string $path): string
    {
        $timestamp = is_file($path) ? filemtime($path) : time();

        return Carbon::createFromTimestamp($timestamp)->toAtomString();
    }

    private function generateXml(array $urls): string
    {
        $items = '';
        foreach ($urls as $url) {
            $items .= "\n    <url>";
            $items .= "\n        <loc>".e($url['loc']).'</loc>';
            $items .= "\n        <lastmod>{$url['lastmod']}</lastmod>";
            $items .= "\n        <changefreq>{$url['changefreq']}</changefreq>";
            $items .= "\n        <priority>{$url['priority']}</priority>";
            $items .= "\n    </url>";
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            ."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$items
            ."\n".'</urlset>';
    }
}
