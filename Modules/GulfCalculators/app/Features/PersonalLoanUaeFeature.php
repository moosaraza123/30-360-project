<?php

namespace Modules\GulfCalculators\Features;

/**
 * UAE personal loan eligibility — CBUAE personal loan regulations
 * (Circular 29/2011).
 *
 * - Loan amount may not exceed 20 times the monthly salary.
 * - Maximum repayment period: 48 months.
 * - Debt Burden Ratio: total monthly repayments capped at 50% of income.
 */
class PersonalLoanUaeFeature
{
    public const DBR = 0.5;

    public const SALARY_MULTIPLE = 20;

    public const MAX_MONTHS = 48;

    public function calculate(float $monthlySalary, float $obligations, float $annualRate, int $months): array
    {
        $steps = [];

        $maxEmi = $monthlySalary * self::DBR - $obligations;

        $steps[] = [
            'label' => __('Maximum monthly repayment (50% DBR)'),
            'value' => number_format(max(0, $maxEmi), 2),
            'detail' => number_format($monthlySalary, 2).' × 50% − '.number_format($obligations, 2),
        ];

        if ($maxEmi <= 0) {
            return [
                'eligible' => false,
                'max_emi' => 0.0,
                'steps' => $steps,
            ];
        }

        $r = $annualRate / 100 / 12;
        $loanByDbr = $r > 0
            ? $maxEmi * (1 - (1 + $r) ** -$months) / $r
            : $maxEmi * $months;

        $steps[] = [
            'label' => __('Maximum loan by repayment capacity'),
            'value' => number_format($loanByDbr, 2),
            'detail' => __('EMI').' '.number_format($maxEmi, 2).' × '.$months.' '.__('months').' @ '.$annualRate.'%',
        ];

        $salaryCap = self::SALARY_MULTIPLE * $monthlySalary;

        $steps[] = [
            'label' => __('Loan cap (20× monthly salary)'),
            'value' => number_format($salaryCap, 2),
            'detail' => '20 × '.number_format($monthlySalary, 2),
        ];

        $maxLoan = min($loanByDbr, $salaryCap);
        $bindingConstraint = $loanByDbr <= $salaryCap ? 'dbr' : 'salary_multiple';

        // EMI actually payable on the granted amount.
        $emi = $r > 0
            ? $maxLoan * $r / (1 - (1 + $r) ** -$months)
            : $maxLoan / $months;

        $steps[] = [
            'label' => __('Monthly installment on the maximum loan'),
            'value' => number_format($emi, 2),
            'detail' => number_format($maxLoan, 2).' @ '.$annualRate.'% × '.$months.' '.__('months'),
        ];

        return [
            'eligible' => true,
            'max_emi' => round($maxEmi, 2),
            'max_loan' => round($maxLoan, 2),
            'emi' => round($emi, 2),
            'binding_constraint' => $bindingConstraint,
            'steps' => $steps,
        ];
    }
}
