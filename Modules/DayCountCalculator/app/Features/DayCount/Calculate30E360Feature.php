<?php

namespace Modules\DayCountCalculator\Features\DayCount;

use Modules\DayCountCalculator\DTOs\CalculationRequest;
use Modules\DayCountCalculator\DTOs\CalculationResult;

/**
 * Calculate 30E/360 (Eurobond Basis) Feature
 *
 * Also known as: 30E/360, Eurobond Basis, ISMA-30/360
 *
 * Rules (per Wikipedia):
 * - If D1 is the last day of the month, then change D1 to 30
 * - If D2 is the last day of the month (unless Date2 is maturity and M2 is February), then change D2 to 30
 *
 * Formula: Days = 360×(Y2-Y1) + 30×(M2-M1) + (D2-D1)
 */
class Calculate30E360Feature
{
    /**
     * Execute the calculation
     */
    public function execute(CalculationRequest $request): CalculationResult
    {
        $steps = [];
        $d1 = $request->startDate->day;
        $m1 = $request->startDate->month;
        $y1 = $request->startDate->year;

        $d2 = $request->endDate->day;
        $m2 = $request->endDate->month;
        $y2 = $request->endDate->year;

        $originalD1 = $d1;
        $originalD2 = $d2;

        // Check if date is last day of month
        $isD1LastDay = $request->startDate->isLastOfMonth();
        $isD2LastDay = $request->endDate->isLastOfMonth();

        // Step 1: Check if D1 is last day of month
        if ($isD1LastDay) {
            $d1 = 30;
            $steps[] = [
                'title' => 'Adjustment: D1 is Last Day of Month',
                'description' => "{$request->startDate->format('Y-m-d')} is the last day of the month, changed to 30",
                'formula' => "D1: {$originalD1} → 30",
                'applied' => true,
            ];
        } else {
            $steps[] = [
                'title' => 'Check: D1 is Last Day of Month',
                'description' => "{$request->startDate->format('Y-m-d')} is not the last day of the month, no adjustment",
                'formula' => "D1: {$d1}",
                'applied' => false,
            ];
        }

        // Step 2: Check if D2 is last day of month (with February exception for maturity)
        $isFebruaryMaturity = ($m2 === 2 && $request->applyEomAdjustment);

        if ($isD2LastDay && !$isFebruaryMaturity) {
            $d2 = 30;
            $steps[] = [
                'title' => 'Adjustment: D2 is Last Day of Month',
                'description' => "{$request->endDate->format('Y-m-d')} is the last day of the month (not February maturity), changed to 30",
                'formula' => "D2: {$originalD2} → 30",
                'applied' => true,
            ];
        } else {
            $reason = $isFebruaryMaturity ? "(February maturity exception)" : "(not last day)";
            $steps[] = [
                'title' => 'Check: D2 is Last Day of Month',
                'description' => "{$request->endDate->format('Y-m-d')} - no adjustment {$reason}",
                'formula' => "D2: {$d2}",
                'applied' => false,
            ];
        }

        // Step 3: Calculate days
        $days = 360 * ($y2 - $y1) + 30 * ($m2 - $m1) + ($d2 - $d1);

        $steps[] = [
            'title' => 'Calculate Days',
            'description' => "Apply the 30E/360 formula",
            'formula' => "Days = 360×({$y2}-{$y1}) + 30×({$m2}-{$m1}) + ({$d2}-{$d1}) = {$days}",
            'applied' => true,
        ];

        // Step 4: Calculate day count factor
        $dayCountFactor = $days / 360;

        $steps[] = [
            'title' => 'Calculate Day Count Factor',
            'description' => "Divide days by 360",
            'formula' => "Factor = {$days} / 360 = " . number_format($dayCountFactor, 10),
            'applied' => true,
        ];

        // Step 5: Calculate interest if provided
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
            days: $days,
            dayCountFactor: $dayCountFactor,
            interestAmount: $interestAmount,
            steps: $steps,
            conventionType: '30E/360'
        );
    }
}
