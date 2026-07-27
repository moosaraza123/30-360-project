<?php

namespace Modules\GulfCalculators\Features;

/**
 * Saudi Real Estate Transaction Tax (RETT) — 5% of the transaction value
 * (ZATCA; RETT Law and executive regulations effective April 2025).
 *
 * First-home relief: for a Saudi citizen's first home the state bears the
 * RETT on the first SAR 1,000,000 of the purchase price; 5% applies only to
 * the amount above that.
 */
class RettKsaFeature
{
    public const RATE = 0.05;

    public const FIRST_HOME_RELIEF_CAP = 1000000.0;

    public function calculate(float $propertyValue, bool $firstHome): array
    {
        $steps = [];

        $exempt = $firstHome ? min($propertyValue, self::FIRST_HOME_RELIEF_CAP) : 0.0;

        if ($firstHome) {
            $steps[] = [
                'label' => __('First-home relief (state bears RETT)'),
                'value' => number_format($exempt, 2),
                'detail' => 'min('.number_format($propertyValue, 2).', 1,000,000)',
            ];
        }

        $taxable = $propertyValue - $exempt;

        $steps[] = [
            'label' => __('Taxable amount'),
            'value' => number_format($taxable, 2),
            'detail' => number_format($propertyValue, 2).' − '.number_format($exempt, 2),
        ];

        $tax = $taxable * self::RATE;

        $steps[] = [
            'label' => __('RETT (5%)'),
            'value' => number_format($tax, 2),
            'detail' => number_format($taxable, 2).' × 5%',
        ];

        $totalCost = $propertyValue + $tax;

        $steps[] = [
            'label' => __('Total cost including RETT'),
            'value' => number_format($totalCost, 2),
            'detail' => number_format($propertyValue, 2).' + '.number_format($tax, 2),
        ];

        return [
            'exempt' => round($exempt, 2),
            'taxable' => round($taxable, 2),
            'tax' => round($tax, 2),
            'total_cost' => round($totalCost, 2),
            'steps' => $steps,
        ];
    }
}
