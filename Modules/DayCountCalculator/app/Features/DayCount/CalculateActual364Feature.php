<?php

namespace Modules\DayCountCalculator\Features\DayCount;

use Modules\DayCountCalculator\DTOs\CalculationRequest;
use Modules\DayCountCalculator\DTOs\CalculationResult;

/**
 * Calculate Actual/364 Feature
 *
 * Formula: Factor = (Actual Days) / 364
 *
 * Common use: Some floating rate notes
 */
class CalculateActual364Feature
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

        // Step 2: Calculate day count factor
        $dayCountFactor = $days / 364;

        $steps[] = [
            'title' => 'Calculate Day Count Factor',
            'description' => 'Divide actual days by 364',
            'formula' => "Factor = {$days} / 364 = ".number_format($dayCountFactor, 10),
            'applied' => true,
        ];

        // Step 3: Calculate interest if provided
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
            conventionType: 'Actual/364'
        );
    }
}
