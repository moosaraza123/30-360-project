<?php

namespace Modules\GulfCalculators\Tests\Unit;

use Modules\GulfCalculators\Features\GosiKsaFeature;
use Modules\GulfCalculators\Features\LoanFeature;
use Modules\GulfCalculators\Features\SalaryUaeFeature;
use Tests\TestCase;

class Tier1FeaturesTest extends TestCase
{
    /* ---------------- GOSI KSA ---------------- */

    public function test_gosi_saudi_national(): void
    {
        // Base = 10,000 + 2,000 = 12,000 → employee 9.75% = 1,170; employer 11.75% = 1,410
        $r = (new GosiKsaFeature)->calculate(10000, 2000, 1000, 'saudi');

        $this->assertSame(13000.0, $r['gross']);
        $this->assertSame(12000.0, $r['contribution_base']);
        $this->assertSame(1170.0, $r['employee_contribution']);
        $this->assertSame(1410.0, $r['employer_contribution']);
        $this->assertSame(11830.0, $r['net']);
    }

    public function test_gosi_base_is_capped_at_45k(): void
    {
        $r = (new GosiKsaFeature)->calculate(40000, 10000, 0, 'saudi');

        $this->assertTrue($r['base_capped']);
        $this->assertSame(45000.0, $r['contribution_base']);
        $this->assertEqualsWithDelta(4387.50, $r['employee_contribution'], 0.01);
    }

    public function test_gosi_expat_has_no_employee_deduction(): void
    {
        $r = (new GosiKsaFeature)->calculate(8000, 2000, 500, 'expat');

        $this->assertSame(0.0, $r['employee_contribution']);
        $this->assertSame(200.0, $r['employer_contribution']); // 2% of 10,000
        $this->assertSame(10500.0, $r['net']); // net = gross
    }

    /* ---------------- Salary UAE ---------------- */

    public function test_uae_expat_net_equals_gross(): void
    {
        $r = (new SalaryUaeFeature)->calculate(20000, 'expat');

        $this->assertSame(20000.0, $r['net']);
        $this->assertSame(0.0, $r['pension']);
        $this->assertSame(240000.0, $r['annual_net']);
    }

    public function test_uae_national_pre_2023_pays_5_percent(): void
    {
        $r = (new SalaryUaeFeature)->calculate(20000, 'national_pre2023');

        $this->assertSame(1000.0, $r['pension']);
        $this->assertSame(19000.0, $r['net']);
    }

    public function test_uae_national_post_2023_pays_11_percent(): void
    {
        $r = (new SalaryUaeFeature)->calculate(20000, 'national_post2023');

        $this->assertSame(2200.0, $r['pension']);
        $this->assertSame(17800.0, $r['net']);
    }

    /* ---------------- Loan ---------------- */

    public function test_reducing_balance_emi_standard_vector(): void
    {
        // 100,000 @ 6% for 60 months → EMI 1,933.28 (standard amortization result)
        $r = (new LoanFeature)->calculate(100000, 6, 60, LoanFeature::METHOD_REDUCING);

        $this->assertEqualsWithDelta(1933.28, $r['emi'], 0.01);
        $this->assertEqualsWithDelta(15996.8, $r['total_interest'], 1.0);
    }

    public function test_reducing_zero_rate(): void
    {
        $r = (new LoanFeature)->calculate(12000, 0, 12, LoanFeature::METHOD_REDUCING);

        $this->assertSame(1000.0, $r['emi']);
        $this->assertSame(0.0, $r['total_interest']);
    }

    public function test_flat_rate_and_apr_equivalent(): void
    {
        // 100,000 @ 5% flat for 24 months → cost 10,000; EMI 4,583.33
        $r = (new LoanFeature)->calculate(100000, 5, 24, LoanFeature::METHOD_FLAT);

        $this->assertEqualsWithDelta(10000.0, $r['total_interest'], 0.01);
        $this->assertEqualsWithDelta(4583.33, $r['emi'], 0.01);
        // Flat 5% ≈ 9.3–9.6% reducing-equivalent APR
        $this->assertGreaterThan(9.0, $r['apr_equivalent']);
        $this->assertLessThan(10.0, $r['apr_equivalent']);
    }
}
