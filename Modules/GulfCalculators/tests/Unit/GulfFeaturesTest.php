<?php

namespace Modules\GulfCalculators\Tests\Unit;

use Carbon\Carbon;
use Modules\GulfCalculators\Features\GratuityKsaFeature;
use Modules\GulfCalculators\Features\GratuityUaeFeature;
use Modules\GulfCalculators\Features\VatFeature;
use Modules\GulfCalculators\Features\ZakatFeature;
use Tests\TestCase;

/**
 * Known-answer tests for the Gulf calculators.
 * Gratuity vectors derived from UAE Federal Decree-Law 33/2021 Art. 51 and
 * Saudi Labor Law Arts. 84–85. Years of service = actual days / 365.
 */
class GulfFeaturesTest extends TestCase
{
    private function d(string $date): Carbon
    {
        return Carbon::parse($date);
    }

    /* ---------------- UAE gratuity ---------------- */

    public function test_uae_no_entitlement_under_one_year(): void
    {
        $r = (new GratuityUaeFeature)->calculate(10000, $this->d('2022-01-01'), $this->d('2022-12-01'));

        $this->assertFalse($r['eligible']);
        $this->assertSame(0.0, $r['gratuity']);
    }

    public function test_uae_exactly_one_year(): void
    {
        // 365 days → 1.0 year → 21 days of basic wage: 21 × (3000/30) = 2100
        $r = (new GratuityUaeFeature)->calculate(3000, $this->d('2022-01-01'), $this->d('2023-01-01'));

        $this->assertTrue($r['eligible']);
        $this->assertEqualsWithDelta(2100.0, $r['gratuity'], 0.01);
    }

    public function test_uae_three_years(): void
    {
        // 1096 days (incl. 2024 leap) → 3.00274y × 21 × (10000/30)
        $r = (new GratuityUaeFeature)->calculate(10000, $this->d('2022-01-01'), $this->d('2025-01-01'));

        $expected = (1096 / 365) * 21 * (10000 / 30);
        $this->assertEqualsWithDelta($expected, $r['gratuity'], 0.05);
        $this->assertFalse($r['capped']);
    }

    public function test_uae_beyond_five_years_uses_30_days(): void
    {
        // 2019-01-01 → 2025-01-01 = 2192 days → 6.00548y; basic 9000 (daily 300)
        $r = (new GratuityUaeFeature)->calculate(9000, $this->d('2019-01-01'), $this->d('2025-01-01'));

        $years = 2192 / 365;
        $expected = 5 * 21 * 300 + ($years - 5) * 30 * 300;
        $this->assertEqualsWithDelta($expected, $r['gratuity'], 0.05);
    }

    public function test_uae_cap_at_two_years_wage(): void
    {
        $r = (new GratuityUaeFeature)->calculate(1000, $this->d('1975-01-01'), $this->d('2025-01-01'));

        $this->assertTrue($r['capped']);
        $this->assertEqualsWithDelta(24000.0, $r['gratuity'], 0.01);
    }

    /* ---------------- KSA end of service ---------------- */

    public function test_ksa_termination_three_years(): void
    {
        // 1096 days → 3.00274y × ½ × 10000
        $r = (new GratuityKsaFeature)->calculate(10000, $this->d('2022-01-01'), $this->d('2025-01-01'), GratuityKsaFeature::END_TERMINATION);

        $this->assertEqualsWithDelta((1096 / 365) * 0.5 * 10000, $r['gratuity'], 0.05);
        $this->assertSame(1.0, $r['fraction']);
    }

    public function test_ksa_termination_seven_years(): void
    {
        // 2557 days → 7.00548y: 5×½ + 2.00548×1 month
        $r = (new GratuityKsaFeature)->calculate(10000, $this->d('2018-01-01'), $this->d('2025-01-01'), GratuityKsaFeature::END_TERMINATION);

        $years = 2557 / 365;
        $expected = 5 * 0.5 * 10000 + ($years - 5) * 10000;
        $this->assertEqualsWithDelta($expected, $r['gratuity'], 0.05);
    }

    public function test_ksa_resignation_under_two_years_gets_nothing(): void
    {
        $r = (new GratuityKsaFeature)->calculate(10000, $this->d('2023-06-01'), $this->d('2024-12-01'), GratuityKsaFeature::END_RESIGNATION);

        $this->assertFalse($r['eligible']);
        $this->assertSame(0.0, $r['gratuity']);
    }

    public function test_ksa_resignation_three_years_gets_one_third(): void
    {
        $r = (new GratuityKsaFeature)->calculate(10000, $this->d('2022-01-01'), $this->d('2025-01-01'), GratuityKsaFeature::END_RESIGNATION);

        $base = (1096 / 365) * 0.5 * 10000;
        $this->assertEqualsWithDelta($base / 3, $r['gratuity'], 0.05);
    }

    public function test_ksa_resignation_seven_years_gets_two_thirds(): void
    {
        $r = (new GratuityKsaFeature)->calculate(10000, $this->d('2018-01-01'), $this->d('2025-01-01'), GratuityKsaFeature::END_RESIGNATION);

        $years = 2557 / 365;
        $base = 5 * 0.5 * 10000 + ($years - 5) * 10000;
        $this->assertEqualsWithDelta($base * 2 / 3, $r['gratuity'], 0.05);
    }

    public function test_ksa_resignation_twelve_years_gets_full_award(): void
    {
        $r = (new GratuityKsaFeature)->calculate(10000, $this->d('2013-01-01'), $this->d('2025-01-01'), GratuityKsaFeature::END_RESIGNATION);

        $this->assertSame(1.0, $r['fraction']);
        $this->assertEqualsWithDelta($r['base_award'], $r['gratuity'], 0.01);
    }

    /* ---------------- VAT ---------------- */

    public function test_vat_add_uae(): void
    {
        $r = (new VatFeature)->calculate(100, 0.05, VatFeature::MODE_ADD);

        $this->assertSame(100.0, $r['net']);
        $this->assertSame(5.0, $r['vat']);
        $this->assertSame(105.0, $r['gross']);
    }

    public function test_vat_remove_ksa(): void
    {
        $r = (new VatFeature)->calculate(115, 0.15, VatFeature::MODE_REMOVE);

        $this->assertSame(100.0, $r['net']);
        $this->assertSame(15.0, $r['vat']);
        $this->assertSame(115.0, $r['gross']);
    }

    public function test_vat_add_ksa(): void
    {
        $r = (new VatFeature)->calculate(200, 0.15, VatFeature::MODE_ADD);

        $this->assertSame(30.0, $r['vat']);
        $this->assertSame(230.0, $r['gross']);
    }

    /* ---------------- Zakat ---------------- */

    public function test_zakat_due_above_nisab(): void
    {
        // Gold @400/g → nisab = 34,000; cash 50,000 → 2.5% = 1,250
        $r = (new ZakatFeature)->calculate(['cash' => 50000], 400);

        $this->assertTrue($r['due']);
        $this->assertSame(34000.0, $r['nisab']);
        $this->assertSame(1250.0, $r['zakat']);
    }

    public function test_zakat_not_due_below_nisab(): void
    {
        $r = (new ZakatFeature)->calculate(['cash' => 10000], 400);

        $this->assertFalse($r['due']);
        $this->assertSame(0.0, $r['zakat']);
    }

    public function test_zakat_debts_reduce_wealth_below_nisab(): void
    {
        $r = (new ZakatFeature)->calculate(['cash' => 50000, 'debts' => 20000], 400);

        $this->assertSame(30000.0, $r['zakatable']);
        $this->assertFalse($r['due']);
    }

    public function test_zakat_sums_all_asset_classes(): void
    {
        $r = (new ZakatFeature)->calculate([
            'cash' => 10000, 'gold' => 20000, 'silver' => 1000,
            'business' => 5000, 'receivables' => 4000, 'debts' => 2000,
        ], 400);

        $this->assertSame(38000.0, $r['zakatable']);
        $this->assertTrue($r['due']);
        $this->assertSame(950.0, $r['zakat']);
    }
}
