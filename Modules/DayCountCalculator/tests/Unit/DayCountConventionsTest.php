<?php

namespace Modules\DayCountCalculator\Tests\Unit;

use Modules\DayCountCalculator\DTOs\CalculationRequest;
use Modules\DayCountCalculator\Features\DayCount\Calculate30360BondBasisFeature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30360USFeature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30E360Feature;
use Modules\DayCountCalculator\Features\DayCount\Calculate30E360ISDAFeature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual360Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual364Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActual365Feature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActualActualFeature;
use Modules\DayCountCalculator\Features\DayCount\CalculateActualActualISDAFeature;
use PHPUnit\Framework\TestCase;

/**
 * Known-answer tests for the nine day count conventions.
 *
 * Expected values are hand-derived from the convention definitions
 * (ISDA 2006 Sections 4.16(f)-(h), NASD 30/360, Act/360, Act/365F).
 */
class DayCountConventionsTest extends TestCase
{
    private const DELTA = 1e-9;

    private function request(string $start, string $end, array $extra = []): CalculationRequest
    {
        return CalculationRequest::fromArray(array_merge([
            'convention_type' => 'test',
            'start_date' => $start,
            'end_date' => $end,
        ], $extra));
    }

    // ---- 30/360 US ---------------------------------------------------------

    public function test_30360_us_plain_month(): void
    {
        $result = (new Calculate30360USFeature)->execute($this->request('2024-01-15', '2024-02-15'));

        $this->assertSame(30, $result->days);
        $this->assertEqualsWithDelta(30 / 360, $result->dayCountFactor, self::DELTA);
    }

    public function test_30360_us_adjusts_day_31_on_both_dates(): void
    {
        // D1: 31 -> 30, then D2: 31 with D1 in {30,31} -> 30
        $result = (new Calculate30360USFeature)->execute($this->request('2024-01-31', '2024-07-31'));

        $this->assertSame(180, $result->days);
        $this->assertEqualsWithDelta(0.5, $result->dayCountFactor, self::DELTA);
    }

    public function test_30360_us_keeps_d2_31_when_d1_is_low(): void
    {
        $result = (new Calculate30360USFeature)->execute($this->request('2024-01-15', '2024-01-31'));

        $this->assertSame(16, $result->days);
    }

    public function test_30360_us_interest_amount(): void
    {
        $result = (new Calculate30360USFeature)->execute($this->request('2024-01-31', '2024-07-31', [
            'principal' => 1_000_000,
            'interest_rate' => 0.05,
        ]));

        $this->assertEqualsWithDelta(25_000.0, $result->interestAmount, 1e-6);
    }

    public function test_30360_us_eom_adjusts_end_of_february_start(): void
    {
        // EOM rule: D1 last day of Feb -> 30, then D2 31 with D1 30 -> 30
        $eom = (new Calculate30360USFeature)->execute($this->request('2024-02-29', '2024-08-31', [
            'apply_eom_adjustment' => true,
        ]));
        $plain = (new Calculate30360USFeature)->execute($this->request('2024-02-29', '2024-08-31'));

        $this->assertSame(180, $eom->days);
        $this->assertSame(182, $plain->days); // D1=29 stays, D2=31 stays (D1 not 30/31)
    }

    public function test_30360_us_eom_adjusts_both_february_ends(): void
    {
        $result = (new Calculate30360USFeature)->execute($this->request('2024-02-29', '2025-02-28', [
            'apply_eom_adjustment' => true,
        ]));

        $this->assertSame(360, $result->days);
        $this->assertEqualsWithDelta(1.0, $result->dayCountFactor, self::DELTA);
    }

    public function test_30360_bond_basis_matches_us_and_keeps_its_name(): void
    {
        $result = (new Calculate30360BondBasisFeature)->execute($this->request('2024-01-31', '2024-07-31'));

        $this->assertSame(180, $result->days);
        $this->assertSame('30/360 Bond Basis', $result->conventionType);
    }

    // ---- 30E/360 (Eurobond) ------------------------------------------------

    public function test_30e360_adjusts_day_31_on_both_dates(): void
    {
        $result = (new Calculate30E360Feature)->execute($this->request('2024-01-31', '2024-03-31'));

        $this->assertSame(60, $result->days);
    }

    public function test_30e360_never_adjusts_february_dates(): void
    {
        // 30E/360 has no February rule: D1=29 stays 29, D2 31 -> 30
        $result = (new Calculate30E360Feature)->execute($this->request('2024-02-29', '2024-08-31'));
        $this->assertSame(181, $result->days);

        // End on last day of February: stays 29
        $result = (new Calculate30E360Feature)->execute($this->request('2024-01-15', '2024-02-29'));
        $this->assertSame(44, $result->days);
    }

    // ---- 30E/360 ISDA ------------------------------------------------------

    public function test_30e360_isda_adjusts_end_of_february(): void
    {
        // D1 last day of Feb -> 30, D2 31 -> 30
        $result = (new Calculate30E360ISDAFeature)->execute($this->request('2024-02-29', '2024-08-31'));
        $this->assertSame(180, $result->days);

        // D2 last day of Feb (not maturity) -> 30
        $result = (new Calculate30E360ISDAFeature)->execute($this->request('2024-08-31', '2025-02-28'));
        $this->assertSame(180, $result->days);
    }

    public function test_30e360_isda_maturity_exception_for_february_end(): void
    {
        // ISDA 2006 4.16(h): D2 on last day of Feb is NOT rolled when it is the maturity date
        $result = (new Calculate30E360ISDAFeature)->execute($this->request('2024-08-31', '2025-02-28', [
            'end_date_is_maturity' => true,
        ]));

        $this->assertSame(178, $result->days);
    }

    // ---- Actual/360, /364, /365 Fixed --------------------------------------

    public function test_actual_360(): void
    {
        $result = (new CalculateActual360Feature)->execute($this->request('2024-01-01', '2024-07-01'));

        $this->assertSame(182, $result->days);
        $this->assertEqualsWithDelta(182 / 360, $result->dayCountFactor, self::DELTA);
    }

    public function test_actual_364(): void
    {
        $result = (new CalculateActual364Feature)->execute($this->request('2024-01-01', '2024-07-01'));

        $this->assertSame(182, $result->days);
        $this->assertEqualsWithDelta(0.5, $result->dayCountFactor, self::DELTA);
    }

    public function test_actual_365_fixed_uses_canonical_name(): void
    {
        $result = (new CalculateActual365Feature)->execute($this->request('2024-01-01', '2024-07-01'));

        $this->assertSame(182, $result->days);
        $this->assertEqualsWithDelta(182 / 365, $result->dayCountFactor, self::DELTA);
        // Regression: previously stamped 'Actual/365', breaking lookups everywhere else
        $this->assertSame('Actual/365 Fixed', $result->conventionType);
    }

    // ---- Actual/Actual (calendar-year split) -------------------------------

    public function test_actual_actual_same_year(): void
    {
        $result = (new CalculateActualActualFeature)->execute($this->request('2024-01-01', '2024-07-01'));

        $this->assertSame(182, $result->days);
        $this->assertEqualsWithDelta(182 / 366, $result->dayCountFactor, self::DELTA);
    }

    public function test_actual_actual_multi_year_split(): void
    {
        // 2023 segment: Jul 1 -> Jan 1 = 184 days / 365
        // 2024 segment: Jan 1 -> Jul 1 = 182 days / 366
        $result = (new CalculateActualActualFeature)->execute($this->request('2023-07-01', '2024-07-01'));

        $this->assertSame(366, $result->days);
        $this->assertEqualsWithDelta(184 / 365 + 182 / 366, $result->dayCountFactor, self::DELTA);
    }

    public function test_actual_actual_multi_year_steps_show_whole_days(): void
    {
        // Regression: endOfYear() capping used to produce fractional day counts
        $result = (new CalculateActualActualFeature)->execute($this->request('2023-07-01', '2024-07-01'));

        $splitStep = collect($result->steps)->firstWhere('title', 'Calculate Weighted Factor');
        $this->assertNotNull($splitStep);
        $this->assertStringContainsString('Year 2023: 184 days / 365', $splitStep['formula']);
        $this->assertStringContainsString('Year 2024: 182 days / 366', $splitStep['formula']);
    }

    // ---- Actual/Actual ISDA ------------------------------------------------

    public function test_actual_actual_isda_multi_year(): void
    {
        $result = (new CalculateActualActualISDAFeature)->execute($this->request('2023-07-01', '2024-07-01'));

        $this->assertSame(366, $result->days);
        $this->assertEqualsWithDelta(184 / 365 + 182 / 366, $result->dayCountFactor, self::DELTA);
    }

    public function test_actual_actual_isda_long_period_is_fast_and_correct(): void
    {
        // 20-year span: previously an O(days) loop; now segment arithmetic.
        // 2010-01-01 -> 2030-01-01: 5 leap years (2012,16,20,24,28) x 366 + 15 x 365
        $result = (new CalculateActualActualISDAFeature)->execute($this->request('2010-01-01', '2030-01-01'));

        $this->assertSame(5 * 366 + 15 * 365, $result->days);
        $this->assertEqualsWithDelta(20.0, $result->dayCountFactor, self::DELTA);
    }

    public function test_zero_length_period(): void
    {
        $result = (new CalculateActual360Feature)->execute($this->request('2024-03-15', '2024-03-15'));

        $this->assertSame(0, $result->days);
        $this->assertEqualsWithDelta(0.0, $result->dayCountFactor, self::DELTA);
        $this->assertNull($result->interestAmount);
    }
}
