<?php

namespace Modules\GulfCalculators\Tests\Unit;

use Carbon\Carbon;
use Modules\GulfCalculators\Features\CorporateTaxUaeFeature;
use Modules\GulfCalculators\Features\IqamaFeesKsaFeature;
use Modules\GulfCalculators\Features\MortgageAffordabilityUaeFeature;
use Modules\GulfCalculators\Features\OverstayUaeFeature;
use Modules\GulfCalculators\Features\PersonalLoanUaeFeature;
use Modules\GulfCalculators\Features\RettKsaFeature;
use Tests\TestCase;

/**
 * Known-answer tests for the expansion calculators (iqama fees, overstay
 * fine, corporate tax, mortgage affordability, personal loan, RETT).
 */
class ExpansionFeaturesTest extends TestCase
{
    /* ---------------- Iqama fees KSA ---------------- */

    public function test_iqama_full_year_company_worker_with_dependents(): void
    {
        $r = (new IqamaFeesKsaFeature)->calculate(12, 'company', 'noncompliant', 2);

        $this->assertSame(650.0, $r['iqama_fee']);
        $this->assertSame(9600.0, $r['levy']);          // 800 × 12
        $this->assertSame(9600.0, $r['dependent_levy']); // 400 × 2 × 12
        $this->assertSame(19850.0, $r['total']);
        $this->assertSame(10250.0, $r['employer_pays']);
        $this->assertSame(9600.0, $r['employee_pays']);
    }

    public function test_iqama_compliant_company_uses_lower_levy(): void
    {
        $r = (new IqamaFeesKsaFeature)->calculate(12, 'company', 'compliant', 0);

        $this->assertSame(8400.0, $r['levy']); // 700 × 12
        $this->assertSame(9050.0, $r['total']);
    }

    public function test_iqama_domestic_worker_quarter_has_no_levy(): void
    {
        $r = (new IqamaFeesKsaFeature)->calculate(3, 'domestic', 'compliant', 0);

        $this->assertSame(150.0, $r['iqama_fee']); // 600 × 3/12
        $this->assertSame(0.0, $r['levy']);
        $this->assertSame(150.0, $r['total']);
    }

    /* ---------------- Overstay UAE ---------------- */

    public function test_tourist_overstay_has_no_grace_period(): void
    {
        $r = (new OverstayUaeFeature)->calculate(
            'tourist',
            Carbon::parse('2026-01-01'),
            Carbon::parse('2026-01-11'),
        );

        $this->assertSame(10, $r['overstay_days']);
        $this->assertSame(500.0, $r['fine']); // 10 × 50
    }

    public function test_residence_within_30_day_grace_pays_nothing(): void
    {
        $r = (new OverstayUaeFeature)->calculate(
            'residence',
            Carbon::parse('2026-01-01'),
            Carbon::parse('2026-01-31'),
        );

        $this->assertSame(0, $r['overstay_days']);
        $this->assertSame(0.0, $r['fine']);
    }

    public function test_residence_beyond_grace_charged_per_day(): void
    {
        $r = (new OverstayUaeFeature)->calculate(
            'residence',
            Carbon::parse('2026-01-01'),
            Carbon::parse('2026-02-05'),
        );

        $this->assertSame('2026-01-31', $r['grace_end']);
        $this->assertSame(5, $r['overstay_days']);
        $this->assertSame(250.0, $r['fine']);
    }

    public function test_golden_visa_has_180_day_grace(): void
    {
        $r = (new OverstayUaeFeature)->calculate(
            'golden',
            Carbon::parse('2026-01-01'),
            Carbon::parse('2026-06-01'),
        );

        $this->assertSame(0, $r['overstay_days']);
        $this->assertSame(0.0, $r['fine']);
    }

    /* ---------------- Corporate tax UAE ---------------- */

    public function test_corporate_tax_9_percent_above_threshold(): void
    {
        $r = (new CorporateTaxUaeFeature)->calculate(5000000, 500000, false);

        $this->assertSame(125000.0, $r['taxable_above_threshold']);
        $this->assertSame(11250.0, $r['tax']);
        $this->assertSame(0.0225, $r['effective_rate']);
        $this->assertSame(488750.0, $r['net_income']);
    }

    public function test_corporate_tax_zero_below_threshold(): void
    {
        $r = (new CorporateTaxUaeFeature)->calculate(1000000, 300000, false);

        $this->assertSame(0.0, $r['tax']);
    }

    public function test_small_business_relief_zeroes_tax(): void
    {
        $r = (new CorporateTaxUaeFeature)->calculate(2500000, 900000, true);

        $this->assertTrue($r['sbr_applied']);
        $this->assertSame(0.0, $r['tax']);
    }

    public function test_small_business_relief_denied_above_revenue_cap(): void
    {
        $r = (new CorporateTaxUaeFeature)->calculate(4000000, 900000, true);

        $this->assertFalse($r['sbr_applied']);
        $this->assertFalse($r['sbr_eligible']);
        $this->assertSame(47250.0, $r['tax']); // (900k − 375k) × 9%
    }

    /* ---------------- Mortgage affordability UAE ---------------- */

    public function test_mortgage_income_multiple_binds_for_national_at_zero_rate(): void
    {
        $r = (new MortgageAffordabilityUaeFeature)->calculate(30000, 0, 0, 25, 'national', 'first');

        $this->assertSame(15000.0, $r['max_emi']);
        // DBR capacity 15,000 × 300 = 4.5M; income cap 8 × 360,000 = 2.88M binds.
        $this->assertSame(2880000.0, $r['max_loan']);
        $this->assertSame('income_multiple', $r['binding_constraint']);
        $this->assertSame(0.85, $r['ltv']);
        $this->assertSame(3388235.29, $r['max_property']);
    }

    public function test_mortgage_dbr_binds_for_expat_with_obligations(): void
    {
        $r = (new MortgageAffordabilityUaeFeature)->calculate(40000, 5000, 4, 25, 'expat', 'first');

        $this->assertSame(15000.0, $r['max_emi']);
        $this->assertSame('dbr', $r['binding_constraint']);
        $this->assertEqualsWithDelta(2841500, $r['max_loan'], 2500);
        $this->assertSame(0.80, $r['ltv']);
    }

    public function test_mortgage_second_property_uses_60_percent_ltv(): void
    {
        $r = (new MortgageAffordabilityUaeFeature)->calculate(50000, 0, 0, 10, 'expat', 'second');

        $this->assertSame(0.60, $r['ltv']);
    }

    public function test_mortgage_over_indebted_borrower_not_eligible(): void
    {
        $r = (new MortgageAffordabilityUaeFeature)->calculate(20000, 12000, 4, 25, 'expat', 'first');

        $this->assertFalse($r['eligible']);
    }

    /* ---------------- Personal loan UAE ---------------- */

    public function test_personal_loan_dbr_binds(): void
    {
        $r = (new PersonalLoanUaeFeature)->calculate(20000, 2000, 0, 48);

        $this->assertSame(8000.0, $r['max_emi']);
        $this->assertSame(384000.0, $r['max_loan']); // 8,000 × 48 < 20 × 20,000
        $this->assertSame('dbr', $r['binding_constraint']);
        $this->assertSame(8000.0, $r['emi']);
    }

    public function test_personal_loan_salary_multiple_binds(): void
    {
        $r = (new PersonalLoanUaeFeature)->calculate(10000, 0, 0, 48);

        $this->assertSame(200000.0, $r['max_loan']); // 20 × 10,000 < 5,000 × 48
        $this->assertSame('salary_multiple', $r['binding_constraint']);
        $this->assertSame(4166.67, $r['emi']);
    }

    public function test_personal_loan_over_indebted_borrower_not_eligible(): void
    {
        $r = (new PersonalLoanUaeFeature)->calculate(8000, 4000, 5, 48);

        $this->assertFalse($r['eligible']);
    }

    /* ---------------- RETT KSA ---------------- */

    public function test_rett_standard_5_percent(): void
    {
        $r = (new RettKsaFeature)->calculate(2000000, false);

        $this->assertSame(100000.0, $r['tax']);
        $this->assertSame(2100000.0, $r['total_cost']);
    }

    public function test_rett_first_home_relief_up_to_one_million(): void
    {
        $r = (new RettKsaFeature)->calculate(1500000, true);

        $this->assertSame(1000000.0, $r['exempt']);
        $this->assertSame(500000.0, $r['taxable']);
        $this->assertSame(25000.0, $r['tax']);
    }

    public function test_rett_first_home_below_cap_pays_nothing(): void
    {
        $r = (new RettKsaFeature)->calculate(800000, true);

        $this->assertSame(0.0, $r['tax']);
    }
}
