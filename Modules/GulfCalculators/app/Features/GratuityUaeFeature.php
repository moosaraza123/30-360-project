<?php

namespace Modules\GulfCalculators\Features;

use Carbon\Carbon;

/**
 * UAE End-of-Service Gratuity — Federal Decree-Law No. 33 of 2021, Article 51.
 *
 * Rules implemented:
 * - Eligibility: at least 1 year of continuous service.
 * - Base: BASIC monthly wage only (allowances excluded). Daily wage = basic / 30.
 * - 21 days of basic wage per year for the first 5 years of service.
 * - 30 days of basic wage per year beyond 5 years.
 * - Fractions of a year are paid pro-rata.
 * - Total gratuity is capped at 2 years' basic wage (24 x monthly basic).
 * - Under the 2021 law, resignation does NOT reduce the entitlement
 *   (the old Art. 137 reductions applied to pre-2022 unlimited contracts only).
 */
class GratuityUaeFeature
{
    public function calculate(float $basicMonthlySalary, Carbon $startDate, Carbon $endDate): array
    {
        $steps = [];

        $totalDays = (int) $startDate->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay());
        $years = $totalDays / 365;
        $dailyWage = $basicMonthlySalary / 30;

        $steps[] = [
            'label' => __('Years of service'),
            'value' => number_format($years, 2),
            'detail' => "{$totalDays} days ÷ 365",
        ];

        if ($years < 1) {
            return [
                'eligible' => false,
                'years' => round($years, 4),
                'gratuity' => 0.0,
                'capped' => false,
                'steps' => $steps,
            ];
        }

        $steps[] = [
            'label' => __('Daily basic wage'),
            'value' => number_format($dailyWage, 2),
            'detail' => number_format($basicMonthlySalary, 2).' ÷ 30',
        ];

        $firstYears = min($years, 5.0);
        $firstAmount = $firstYears * 21 * $dailyWage;
        $steps[] = [
            'label' => __('First 5 years portion'),
            'value' => number_format($firstAmount, 2),
            'detail' => number_format($firstYears, 2).' × 21 × '.number_format($dailyWage, 2),
        ];

        $extraYears = max(0.0, $years - 5.0);
        $extraAmount = $extraYears * 30 * $dailyWage;
        if ($extraYears > 0) {
            $steps[] = [
                'label' => __('Beyond 5 years portion'),
                'value' => number_format($extraAmount, 2),
                'detail' => number_format($extraYears, 2).' × 30 × '.number_format($dailyWage, 2),
            ];
        }

        $total = $firstAmount + $extraAmount;

        $cap = 24 * $basicMonthlySalary;
        $capped = $total > $cap;
        if ($capped) {
            $steps[] = [
                'label' => __('Legal cap: 2 years of wage'),
                'value' => number_format($cap, 2),
                'detail' => '24 × '.number_format($basicMonthlySalary, 2),
            ];
            $total = $cap;
        }

        return [
            'eligible' => true,
            'years' => round($years, 4),
            'gratuity' => round($total, 2),
            'capped' => $capped,
            'steps' => $steps,
        ];
    }
}
