<?php

namespace Modules\DayCountCalculator\Features\DayCount;

use Modules\DayCountCalculator\DTOs\CalculationRequest;
use Modules\DayCountCalculator\DTOs\CalculationResult;

/**
 * Calculate Actual/Actual ISDA Feature
 *
 * Also known as: Act/Act ISDA, Actual/Actual (historical)
 *
 * Rules:
 * - Days in leap year(s) divided by 366
 * - Days in non-leap year(s) divided by 365
 *
 * Common use: Interest rate swaps, many derivatives
 */
class CalculateActualActualISDAFeature
{
    /**
     * Execute the calculation
     */
    public function execute(CalculationRequest $request): CalculationResult
    {
        $steps = [];

        // Step 1: Calculate actual days between dates
        $totalDays = $request->startDate->diffInDays($request->endDate);

        $steps[] = [
            'title' => 'Calculate Actual Days',
            'description' => "Count the actual number of days between {$request->startDate->format('Y-m-d')} and {$request->endDate->format('Y-m-d')}",
            'formula' => "Total Days = {$totalDays}",
            'applied' => true,
        ];

        // Step 2: Split days into leap year and non-leap year days
        $daysInLeapYear = 0;
        $daysInNonLeapYear = 0;
        $current = $request->startDate->copy();

        while ($current->lessThan($request->endDate)) {
            $nextDay = $current->copy()->addDay();

            if ($nextDay->greaterThan($request->endDate)) {
                break;
            }

            if ($current->isLeapYear()) {
                $daysInLeapYear++;
            } else {
                $daysInNonLeapYear++;
            }

            $current = $nextDay;
        }

        $steps[] = [
            'title' => 'Separate Leap and Non-Leap Year Days',
            'description' => "Split the period into days falling in leap years vs non-leap years",
            'formula' => "Leap year days: {$daysInLeapYear}\nNon-leap year days: {$daysInNonLeapYear}",
            'applied' => true,
        ];

        // Step 3: Calculate day count factor
        $leapFactor = $daysInLeapYear / 366;
        $nonLeapFactor = $daysInNonLeapYear / 365;
        $dayCountFactor = $leapFactor + $nonLeapFactor;

        $steps[] = [
            'title' => 'Calculate Day Count Factor',
            'description' => "Apply ISDA formula: (Leap Days/366) + (Non-Leap Days/365)",
            'formula' => "Factor = ({$daysInLeapYear}/366) + ({$daysInNonLeapYear}/365) = " . number_format($leapFactor, 10) . " + " . number_format($nonLeapFactor, 10) . " = " . number_format($dayCountFactor, 10),
            'applied' => true,
        ];

        // Step 4: Calculate interest if provided
        $interestAmount = null;
        if ($request->hasInterestCalculation()) {
            $interestAmount = $request->principal * $request->interestRate * $dayCountFactor;

            $steps[] = [
                'title' => 'Calculate Interest',
                'description' => "Multiply principal by rate and factor",
                'formula' => "Interest = {$request->principal} × {$request->interestRate} × " . number_format($dayCountFactor, 10) . " = $" . number_format($interestAmount, 2),
                'applied' => true,
            ];
        }

        return new CalculationResult(
            days: $totalDays,
            dayCountFactor: $dayCountFactor,
            interestAmount: $interestAmount,
            steps: $steps,
            conventionType: 'Actual/Actual ISDA'
        );
    }
}
