<?php

namespace Modules\DayCountCalculator\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests for the monetization/SEO infrastructure: slug-based educational
 * URLs, sitemap correctness, structured data, and config-gated AdSense.
 */
class SeoInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_convention_educational_page_loads_by_slug(): void
    {
        foreach (config('daycountcalculator.conventions') as $convention) {
            $this->get(route('calculator.educate', $convention['slug']))
                ->assertOk()
                ->assertSee($convention['name']);
        }
    }

    public function test_educational_pages_still_load_by_canonical_type(): void
    {
        $this->get('/calculator/learn/30/360 US')->assertOk();
    }

    public function test_sitemap_lists_only_working_urls(): void
    {
        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml');

        preg_match_all('/<loc>(.*?)<\/loc>/', $response->getContent(), $matches);
        $this->assertNotEmpty($matches[1]);

        foreach ($matches[1] as $url) {
            $path = parse_url($url, PHP_URL_PATH) ?? '/';
            $this->get($path)->assertOk();
        }
    }

    public function test_sitemap_contains_all_convention_slugs(): void
    {
        $response = $this->get('/sitemap.xml');

        foreach (config('daycountcalculator.conventions') as $convention) {
            $response->assertSee($convention['slug']);
        }
    }

    public function test_educational_page_has_faq_structured_data(): void
    {
        $slug = config('daycountcalculator.conventions')[0]['slug'];

        $this->get(route('calculator.educate', $slug))
            ->assertSee('application/ld+json', false)
            ->assertSee('FAQPage', false)
            ->assertSee('BreadcrumbList', false);
    }

    public function test_adsense_disabled_by_default(): void
    {
        config(['daycountcalculator.adsense_client' => null]);

        $this->get(route('calculator.index'))
            ->assertDontSee('adsbygoogle', false);

        $this->get('/ads.txt')->assertNotFound();
    }

    public function test_adsense_enabled_when_configured(): void
    {
        config(['daycountcalculator.adsense_client' => 'ca-pub-0000000000000000']);

        $this->get(route('calculator.index'))
            ->assertSee('pagead2.googlesyndication.com', false);

        $this->get('/ads.txt')
            ->assertOk()
            ->assertSee('google.com, pub-0000000000000000, DIRECT', false);
    }
}
