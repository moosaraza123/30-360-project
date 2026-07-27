<?php

namespace Modules\GulfCalculators\Features;

/**
 * UAE mortgage affordability — CBUAE mortgage regulations (Circular 31/2013,
 * LTV limits as amended March 2020).
 *
 * - Debt Burden Ratio: total monthly repayments (this mortgage + existing
 *   obligations) may not exceed 50% of monthly income.
 * - Loan amount cap: 7 years of annual income for expats, 8 for nationals.
 * - Maximum term: 25 years.
 * - LTV, first home: expat 80% (≤ AED 5M) / 70% (> AED 5M);
 *   national 85% / 75%. Second/investment property: expat 60%, national 65%.
 */
class MortgageAffordabilityUaeFeature
{
    public const DBR = 0.5;

    public const INCOME_MULTIPLE = ['expat' => 7, 'national' => 8];

    public const LTV = [
        'expat' => ['first_low' => 0.80, 'first_high' => 0.70, 'second' => 0.60],
        'national' => ['first_low' => 0.85, 'first_high' => 0.75, 'second' => 0.65],
    ];

    public const VALUE_BREAK = 5000000.0;

    public function calculate(float $monthlyIncome, float $obligations, float $annualRate, int $years, string $buyer, string $propertyType): array
    {
        $steps = [];

        $maxEmi = $monthlyIncome * self::DBR - $obligations;

        $steps[] = [
            'label' => __('Maximum monthly repayment (50% DBR)'),
            'value' => number_format(max(0, $maxEmi), 2),
            'detail' => number_format($monthlyIncome, 2).' × 50% − '.number_format($obligations, 2),
        ];

        if ($maxEmi <= 0) {
            return [
                'eligible' => false,
                'max_emi' => 0.0,
                'steps' => $steps,
            ];
        }

        $months = $years * 12;
        $r = $annualRate / 100 / 12;
        $loanByDbr = $r > 0
            ? $maxEmi * (1 - (1 + $r) ** -$months) / $r
            : $maxEmi * $months;

        $steps[] = [
            'label' => __('Maximum loan by repayment capacity'),
            'value' => number_format($loanByDbr, 2),
            'detail' => __('EMI').' '.number_format($maxEmi, 2).' × '.$years.' '.__('years').' @ '.$annualRate.'%',
        ];

        $loanByIncome = self::INCOME_MULTIPLE[$buyer] * $monthlyIncome * 12;

        $steps[] = [
            'label' => __('Loan cap by income multiple'),
            'value' => number_format($loanByIncome, 2),
            'detail' => self::INCOME_MULTIPLE[$buyer].' × '.number_format($monthlyIncome * 12, 2),
        ];

        $maxLoan = min($loanByDbr, $loanByIncome);
        $bindingConstraint = $loanByDbr <= $loanByIncome ? 'dbr' : 'income_multiple';

        // Resolve LTV: for a first home the bracket depends on the resulting
        // property value, so try the low-bracket LTV first and fall back.
        if ($propertyType === 'second') {
            $ltv = self::LTV[$buyer]['second'];
        } else {
            $ltv = self::LTV[$buyer]['first_low'];
            if ($maxLoan / $ltv > self::VALUE_BREAK) {
                $ltv = self::LTV[$buyer]['first_high'];
            }
        }

        $maxProperty = $maxLoan / $ltv;
        $downPayment = $maxProperty - $maxLoan;

        $steps[] = [
            'label' => __('Maximum property price').' ('.__('LTV').' '.($ltv * 100).'%)',
            'value' => number_format($maxProperty, 2),
            'detail' => number_format($maxLoan, 2).' ÷ '.($ltv * 100).'%',
        ];

        $steps[] = [
            'label' => __('Required down payment'),
            'value' => number_format($downPayment, 2),
            'detail' => number_format($maxProperty, 2).' − '.number_format($maxLoan, 2),
        ];

        return [
            'eligible' => true,
            'max_emi' => round($maxEmi, 2),
            'max_loan' => round($maxLoan, 2),
            'ltv' => $ltv,
            'max_property' => round($maxProperty, 2),
            'down_payment' => round($downPayment, 2),
            'binding_constraint' => $bindingConstraint,
            'steps' => $steps,
        ];
    }
}
