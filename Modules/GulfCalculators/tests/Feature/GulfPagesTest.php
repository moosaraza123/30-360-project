<?php

namespace Modules\GulfCalculators\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GulfPagesTest extends TestCase
{
    use RefreshDatabase;

    public static function slugs(): array
    {
        return [
            ['gratuity-calculator-uae'],
            ['end-of-service-calculator-saudi-arabia'],
            ['vat-calculator-uae'],
            ['vat-calculator-saudi-arabia'],
            ['zakat-calculator'],
        ];
    }

    #[DataProvider('slugs')]
    public function test_english_page_loads(string $slug): void
    {
        $this->get("/{$slug}")->assertOk();
    }

    #[DataProvider('slugs')]
    public function test_arabic_page_loads_rtl(string $slug): void
    {
        $this->get("/ar/{$slug}")
            ->assertOk()
            ->assertSee('dir="rtl"', false)
            ->assertSee('lang="ar"', false);
    }

    public function test_arabic_page_has_hreflang_alternates(): void
    {
        $this->get('/ar/zakat-calculator')
            ->assertSee('hreflang="en"', false)
            ->assertSee('hreflang="ar"', false);
    }

    public function test_uae_gratuity_calculation_round_trip(): void
    {
        $response = $this->post('/gratuity-calculator-uae', [
            'basic_salary' => 3000,
            'start_date' => '2022-01-01',
            'end_date' => '2023-01-01',
        ]);

        // 1 year → 21 × (3000/30) = 2,100.00
        $response->assertOk()->assertSee('2,100.00');
    }

    public function test_ksa_resignation_fraction_applied(): void
    {
        $response = $this->post('/ar/end-of-service-calculator-saudi-arabia', [
            'monthly_wage' => 10000,
            'start_date' => '2022-01-01',
            'end_date' => '2025-01-01',
            'end_type' => 'resignation',
        ]);

        // base ≈ 15,013.70 → one third ≈ 5,004.57
        $response->assertOk()->assertSee('5,004.5');
    }

    public function test_vat_remove_round_trip(): void
    {
        $response = $this->post('/vat-calculator-saudi-arabia', [
            'amount' => 115,
            'mode' => 'remove',
        ]);

        $response->assertOk()->assertSee('100.00')->assertSee('15.00');
    }

    public function test_zakat_round_trip(): void
    {
        $response = $this->post('/zakat-calculator', [
            'currency' => 'AED',
            'gold_price_per_gram' => 400,
            'cash' => 50000,
        ]);

        $response->assertOk()->assertSee('1,250.00');
    }

    public function test_sitemap_includes_gulf_pages_in_both_languages(): void
    {
        $this->get('/sitemap.xml')
            ->assertSee(url('gratuity-calculator-uae'))
            ->assertSee(url('ar/gratuity-calculator-uae'))
            ->assertSee(url('ar/zakat-calculator'));
    }
}
