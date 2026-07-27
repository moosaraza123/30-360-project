<?php

namespace Modules\GulfCalculators\Features;

/**
 * UAE corporate tax — Federal Decree-Law No. 47 of 2022.
 *
 * - 0% on taxable income up to AED 375,000; 9% on the excess.
 * - Small Business Relief: a resident business with revenue of AED 3,000,000
 *   or less may elect to be treated as having no taxable income, for tax
 *   periods ending on or before 31 December 2026.
 */
class CorporateTaxUaeFeature
{
    public const THRESHOLD = 375000.0;

    public const RATE = 0.09;

    public const SBR_REVENUE_CAP = 3000000.0;

    public function calculate(float $revenue, float $taxableIncome, bool $sbrElected): array
    {
        $steps = [];

        $sbrEligible = $revenue <= self::SBR_REVENUE_CAP;
        $sbrApplied = $sbrElected && $sbrEligible;

        if ($sbrElected) {
            $steps[] = [
                'label' => __('Small Business Relief'),
                'value' => $sbrApplied ? __('Applied') : __('Not eligible'),
                'detail' => __('Revenue').' '.number_format($revenue, 2).($sbrEligible ? ' ≤ ' : ' > ').'3,000,000',
            ];
        }

        if ($sbrApplied) {
            return [
                'tax' => 0.0,
                'taxable_above_threshold' => 0.0,
                'effective_rate' => 0.0,
                'net_income' => round($taxableIncome, 2),
                'sbr_applied' => true,
                'sbr_eligible' => true,
                'steps' => $steps,
            ];
        }

        $above = max(0.0, $taxableIncome - self::THRESHOLD);

        $steps[] = [
            'label' => __('Income above the AED 375,000 threshold'),
            'value' => number_format($above, 2),
            'detail' => 'max(0, '.number_format($taxableIncome, 2).' − 375,000)',
        ];

        $tax = $above * self::RATE;

        $steps[] = [
            'label' => __('Corporate tax (9%)'),
            'value' => number_format($tax, 2),
            'detail' => number_format($above, 2).' × 9%',
        ];

        $effectiveRate = $taxableIncome > 0 ? $tax / $taxableIncome : 0.0;

        $steps[] = [
            'label' => __('Effective tax rate'),
            'value' => number_format($effectiveRate * 100, 2).'%',
            'detail' => number_format($tax, 2).' ÷ '.number_format($taxableIncome, 2),
        ];

        return [
            'tax' => round($tax, 2),
            'taxable_above_threshold' => round($above, 2),
            'effective_rate' => round($effectiveRate, 4),
            'net_income' => round($taxableIncome - $tax, 2),
            'sbr_applied' => false,
            'sbr_eligible' => $sbrEligible,
            'steps' => $steps,
        ];
    }
}
