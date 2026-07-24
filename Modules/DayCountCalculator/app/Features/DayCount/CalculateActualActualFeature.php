<?php

namespace Modules\DayCountCalculator\Features\DayCount;

use Modules\DayCountCalculator\DTOs\CalculationRequest;
use Modules\DayCountCalculator\DTOs\CalculationResult;

/**
 * Calculate Actual/Actual Feature (calendar-year split)
 *
 * Splits the period at calendar-year boundaries and divides each segment
 * by the length of its own year (365 or 366). Note: this is NOT the ICMA
 * convention — true Act/Act ICMA is defined per coupon period and requires
 * a payment frequency, which this two-date calculator does not capture.
 *
 * Formula: Factor = Σ (days in year i) / (365 or 366)
 *
 * Common use: quick Act/Act estimates when no coupon schedule is available
 */
class CalculateActualActualFeature
{
    /**
     * Execute the calculation
     */
    public function execute(CalculationRequest $request): CalculationResult
    {
        $steps = [];

        // Step 1: Calculate actual days between dates
        $days = (int) $request->startDate->copy()->startOfDay()
            ->diffInDays($request->endDate->copy()->startOfDay());

        $steps[] = [
            'title' => 'Calculate Actual Days',
            'description' => "Count the actual number of days between {$request->startDate->format('Y-m-d')} and {$request->endDate->format('Y-m-d')}",
            'formula' => "Days = {$days}",
            'applied' => true,
        ];

        // Step 2: Determine if period spans leap year(s)
        $startYear = $request->startDate->year;
        $endYear = $request->endDate->year;

        if ($startYear === $endYear) {
            // Same year - simple calculation
            $daysInYear = $request->startDate->isLeapYear() ? 366 : 365;
            $isLeapYear = $request->startDate->isLeapYear();

            $steps[] = [
                'title' => 'Determine Days in Year',
                'description' => "Period is within {$startYear}, which is ".($isLeapYear ? 'a leap year' : 'not a leap year'),
                'formula' => "Days in Year = {$daysInYear}",
                'applied' => true,
            ];

            $dayCountFactor = $days / $daysInYear;
        } else {
            // Spans multiple years - weighted calculation.
            // Each segment runs up to (exclusive) the next Jan 1, so day
            // counts stay whole numbers.
            $factor = 0;
            $current = $request->startDate->copy()->startOfDay();
            $periodEnd = $request->endDate->copy()->startOfDay();
            $factorSteps = [];

            while ($current->lessThan($periodEnd)) {
                $nextYearStart = $current->copy()->addYear()->startOfYear()->startOfDay();
                $segmentEnd = $nextYearStart->greaterThan($periodEnd) ? $periodEnd->copy() : $nextYearStart;

                $daysInThisYear = (int) $current->diffInDays($segmentEnd);
                $totalDaysInYear = $current->isLeapYear() ? 366 : 365;
                $yearFactor = $daysInThisYear / $totalDaysInYear;
                $factor += $yearFactor;

                $factorSteps[] = "Year {$current->year}: {$daysInThisYear} days / {$totalDaysInYear} = ".number_format($yearFactor, 10);

                $current = $segmentEnd;
            }

            $steps[] = [
                'title' => 'Calculate Weighted Factor',
                'description' => 'Period spans multiple years, calculate weighted factor',
                'formula' => implode("\n", $factorSteps),
                'applied' => true,
            ];

            $dayCountFactor = $factor;
        }

        // Step 3: Calculate day count factor
        $steps[] = [
            'title' => 'Day Count Factor',
            'description' => 'Final day count factor',
            'formula' => 'Factor = '.number_format($dayCountFactor, 10),
            'applied' => true,
        ];

        // Step 4: Calculate interest if provided
        $interestAmount = null;
        if ($request->hasInterestCalculation()) {
            $interestAmount = $request->principal * $request->interestRate * $dayCountFactor;

            $steps[] = [
                'title' => 'Calculate Interest',
                'description' => 'Multiply principal by rate and factor',
                'formula' => "Interest = {$request->principal} × {$request->interestRate} × ".number_format($dayCountFactor, 10).' = $'.number_format($interestAmount, 2),
                'applied' => true,
            ];
        }

        return new CalculationResult(
            days: $days,
            dayCountFactor: $dayCountFactor,
            interestAmount: $interestAmount,
            steps: $steps,
            conventionType: 'Actual/Actual'
        );
    }
}
