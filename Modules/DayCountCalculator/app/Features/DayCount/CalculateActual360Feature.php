<?php

namespace Modules\DayCountCalculator\Features\DayCount;

use Modules\DayCountCalculator\DTOs\CalculationRequest;
use Modules\DayCountCalculator\DTOs\CalculationResult;

/**
 * Calculate Actual/360 (Money Market) Feature
 *
 * Also known as: Act/360, French
 *
 * Formula: Factor = (Actual Days) / 360
 *
 * Common use: Money market instruments, repos, commercial paper
 */
class CalculateActual360Feature
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
        $dayCountFactor = $days / 360;

        $steps[] = [
            'title' => 'Calculate Day Count Factor',
            'description' => 'Divide actual days by 360 (money market basis)',
            'formula' => "Factor = {$days} / 360 = ".number_format($dayCountFactor, 10),
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
            conventionType: 'Actual/360'
        );
    }
}
