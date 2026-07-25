<?php

namespace Modules\GulfCalculators\Features;

use Carbon\Carbon;

/**
 * Saudi Arabia End-of-Service Award — Labor Law Articles 84 & 85.
 *
 * Article 84 (base award, on termination by employer):
 * - Half a month's wage per year for the first 5 years.
 * - One month's wage per year beyond 5 years.
 * - Wage = last wage; fractions of a year pro-rata.
 *
 * Article 85 (resignation):
 * - Service < 2 years: no award.
 * - 2–5 years: one third of the award.
 * - 5–10 years: two thirds of the award.
 * - 10+ years: full award.
 */
class GratuityKsaFeature
{
    public const END_TERMINATION = 'termination';

    public const END_RESIGNATION = 'resignation';

    public function calculate(float $monthlyWage, Carbon $startDate, Carbon $endDate, string $endType): array
    {
        $steps = [];

        $totalDays = (int) $startDate->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay());
        $years = $totalDays / 365;

        $steps[] = [
            'label' => __('Years of service'),
            'value' => number_format($years, 2),
            'detail' => "{$totalDays} days ÷ 365",
        ];

        // Article 84 base award
        $firstYears = min($years, 5.0);
        $firstAmount = $firstYears * 0.5 * $monthlyWage;
        $steps[] = [
            'label' => __('First 5 years portion'),
            'value' => number_format($firstAmount, 2),
            'detail' => number_format($firstYears, 2).' × ½ × '.number_format($monthlyWage, 2),
        ];

        $extraYears = max(0.0, $years - 5.0);
        $extraAmount = $extraYears * $monthlyWage;
        if ($extraYears > 0) {
            $steps[] = [
                'label' => __('Beyond 5 years portion'),
                'value' => number_format($extraAmount, 2),
                'detail' => number_format($extraYears, 2).' × 1 × '.number_format($monthlyWage, 2),
            ];
        }

        $baseAward = $firstAmount + $extraAmount;

        // Article 85 resignation fractions
        $fraction = 1.0;
        $fractionLabel = null;

        if ($endType === self::END_RESIGNATION) {
            if ($years < 2) {
                $fraction = 0.0;
                $fractionLabel = __('Resignation with under 2 years of service: no award (Art. 85)');
            } elseif ($years < 5) {
                $fraction = 1 / 3;
                $fractionLabel = __('Resignation with 2–5 years of service: one third (Art. 85)');
            } elseif ($years < 10) {
                $fraction = 2 / 3;
                $fractionLabel = __('Resignation with 5–10 years of service: two thirds (Art. 85)');
            } else {
                $fractionLabel = __('Resignation with 10+ years of service: full award (Art. 85)');
            }
        }

        if ($fractionLabel !== null) {
            $steps[] = [
                'label' => $fractionLabel,
                'value' => number_format($baseAward * $fraction, 2),
                'detail' => number_format($baseAward, 2).' × '.($fraction === 0.0 ? '0' : ($fraction === 1.0 ? '1' : ($fraction > 0.5 ? '⅔' : '⅓'))),
            ];
        }

        $total = $baseAward * $fraction;

        return [
            'eligible' => $total > 0,
            'years' => round($years, 4),
            'base_award' => round($baseAward, 2),
            'fraction' => $fraction,
            'gratuity' => round($total, 2),
            'steps' => $steps,
        ];
    }
}
