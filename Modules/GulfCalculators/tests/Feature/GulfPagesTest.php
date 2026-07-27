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
            ['gosi-calculator-saudi-arabia'],
            ['salary-calculator-uae'],
            ['loan-calculator'],
            ['iqama-fees-calculator-saudi-arabia'],
            ['overstay-fine-calculator-uae'],
            ['corporate-tax-calculator-uae'],
            ['mortgage-affordability-calculator-uae'],
            ['personal-loan-eligibility-calculator-uae'],
            ['rett-calculator-saudi-arabia'],
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

    public function test_gosi_calculation_round_trip(): void
    {
        $response = $this->post('/gosi-calculator-saudi-arabia', [
            'basic_salary' => 10000,
            'housing_allowance' => 2000,
            'other_allowances' => 1000,
            'nationality' => 'saudi',
        ]);

        // net = 13,000 − 1,170
        $response->assertOk()->assertSee('11,830.00');
    }

    public function test_uae_salary_round_trip(): void
    {
        $response = $this->post('/ar/salary-calculator-uae', [
            'gross_salary' => 20000,
            'category' => 'national_post2023',
        ]);

        $response->assertOk()->assertSee('17,800.00');
    }

    public function test_loan_flat_round_trip_shows_apr_equivalent(): void
    {
        $response = $this->post('/loan-calculator', [
            'currency' => 'SAR',
            'principal' => 100000,
            'annual_rate' => 5,
            'months' => 24,
            'method' => 'flat',
        ]);

        $response->assertOk()->assertSee('4,583.33')->assertSee('APR');
    }

    public function test_iqama_round_trip(): void
    {
        $response = $this->post('/iqama-fees-calculator-saudi-arabia', [
            'months' => 12,
            'worker_type' => 'company',
            'saudization' => 'noncompliant',
            'dependents' => 2,
        ]);

        // 650 + 9,600 + 9,600
        $response->assertOk()->assertSee('19,850.00');
    }

    public function test_overstay_round_trip(): void
    {
        $response = $this->post('/ar/overstay-fine-calculator-uae', [
            'visa_type' => 'tourist',
            'expiry_date' => '2026-01-01',
            'settlement_date' => '2026-01-11',
        ]);

        // 10 days × AED 50
        $response->assertOk()->assertSee('500.00');
    }

    public function test_corporate_tax_round_trip(): void
    {
        $response = $this->post('/corporate-tax-calculator-uae', [
            'revenue' => 5000000,
            'taxable_income' => 500000,
        ]);

        // (500k − 375k) × 9%
        $response->assertOk()->assertSee('11,250.00');
    }

    public function test_mortgage_round_trip(): void
    {
        $response = $this->post('/mortgage-affordability-calculator-uae', [
            'monthly_income' => 30000,
            'obligations' => 0,
            'annual_rate' => 0,
            'years' => 25,
            'buyer' => 'national',
            'property_type' => 'first',
        ]);

        // Income-multiple cap: 8 × 360,000 = 2.88M loan
        $response->assertOk()->assertSee('2,880,000');
    }

    public function test_personal_loan_round_trip(): void
    {
        $response = $this->post('/ar/personal-loan-eligibility-calculator-uae', [
            'monthly_salary' => 10000,
            'obligations' => 0,
            'annual_rate' => 0,
            'months' => 48,
        ]);

        // 20 × 10,000 salary cap
        $response->assertOk()->assertSee('200,000');
    }

    public function test_rett_round_trip(): void
    {
        $response = $this->post('/rett-calculator-saudi-arabia', [
            'property_value' => 1500000,
            'first_home' => 1,
        ]);

        // 5% × (1.5M − 1M relief)
        $response->assertOk()->assertSee('25,000.00');
    }

    public function test_legal_pages_load(): void
    {
        $this->get('/privacy-policy')->assertOk()->assertSee('Privacy Policy');
        $this->get('/terms')->assertOk()->assertSee('Terms of Use');
    }

    public function test_sitemap_includes_gulf_pages_in_both_languages(): void
    {
        $this->get('/sitemap.xml')
            ->assertSee(url('gratuity-calculator-uae'))
            ->assertSee(url('ar/gratuity-calculator-uae'))
            ->assertSee(url('ar/zakat-calculator'));
    }
}
