<?php

namespace Modules\GulfCalculators\Features;

/**
 * Loan / financing installment calculator (AED & SAR).
 *
 * Two quoting methods common in the Gulf:
 * - Reducing balance (true amortization):
 *   EMI = P × r × (1+r)^n / ((1+r)^n − 1), r = annual rate / 12.
 * - Flat rate (common for personal/murabaha-style quotes):
 *   total profit = P × flat rate × years; EMI = (P + profit) / n.
 *   Flat rates look smaller but cost roughly 1.8× the equivalent reducing rate —
 *   the result includes the reducing-balance-equivalent APR for comparison.
 */
class LoanFeature
{
    public const METHOD_REDUCING = 'reducing';

    public const METHOD_FLAT = 'flat';

    public function calculate(float $principal, float $annualRatePercent, int $months, string $method): array
    {
        $steps = [];
        $rate = $annualRatePercent / 100;

        if ($method === self::METHOD_FLAT) {
            $years = $months / 12;
            $totalInterest = $principal * $rate * $years;
            $totalPayment = $principal + $totalInterest;
            $emi = $totalPayment / $months;

            $steps[] = [
                'label' => __('Total financing cost (flat)'),
                'value' => number_format($totalInterest, 2),
                'detail' => number_format($principal, 2)." × {$annualRatePercent}% × ".round($years, 2).' '.__('years'),
            ];
            $steps[] = [
                'label' => __('Monthly installment'),
                'value' => number_format($emi, 2),
                'detail' => '('.number_format($principal, 2).' + '.number_format($totalInterest, 2).") ÷ {$months}",
            ];

            $aprEquivalent = $this->reducingEquivalentRate($principal, $emi, $months);
            if ($aprEquivalent !== null) {
                $steps[] = [
                    'label' => __('Equivalent reducing-balance rate (APR)'),
                    'value' => number_format($aprEquivalent, 2).'%',
                    'detail' => __('The true comparable cost of this flat rate'),
                ];
            }
        } else {
            $monthlyRate = $rate / 12;

            if ($monthlyRate > 0) {
                $factor = pow(1 + $monthlyRate, $months);
                $emi = $principal * $monthlyRate * $factor / ($factor - 1);
            } else {
                $emi = $principal / $months;
            }

            $totalPayment = $emi * $months;
            $totalInterest = $totalPayment - $principal;
            $aprEquivalent = $annualRatePercent;

            $steps[] = [
                'label' => __('Monthly installment (EMI)'),
                'value' => number_format($emi, 2),
                'detail' => "P×r×(1+r)^n / ((1+r)^n−1), r = {$annualRatePercent}%/12, n = {$months}",
            ];
            $steps[] = [
                'label' => __('Total financing cost'),
                'value' => number_format($totalInterest, 2),
                'detail' => number_format($totalPayment, 2).' − '.number_format($principal, 2),
            ];
        }

        return [
            'method' => $method,
            'emi' => round($emi, 2),
            'total_interest' => round($totalInterest, 2),
            'total_payment' => round($totalPayment ?? $principal + $totalInterest, 2),
            'apr_equivalent' => $aprEquivalent !== null ? round($aprEquivalent, 2) : null,
            'steps' => $steps,
        ];
    }

    /**
     * Solve the reducing-balance annual rate that produces the same EMI
     * (bisection — monotonic in rate, converges fast).
     */
    private function reducingEquivalentRate(float $principal, float $emi, int $months): ?float
    {
        if ($emi * $months <= $principal) {
            return 0.0;
        }

        $low = 0.0;
        $high = 2.0; // 200% annual — safe upper bound

        for ($i = 0; $i < 80; $i++) {
            $mid = ($low + $high) / 2;
            $r = $mid / 12;
            $factor = pow(1 + $r, $months);
            $candidate = $r > 0 ? $principal * $r * $factor / ($factor - 1) : $principal / $months;

            if ($candidate < $emi) {
                $low = $mid;
            } else {
                $high = $mid;
            }
        }

        return ($low + $high) / 2 * 100;
    }
}
