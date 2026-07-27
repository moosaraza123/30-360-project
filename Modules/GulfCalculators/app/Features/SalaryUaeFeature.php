<?php

namespace Modules\GulfCalculators\Features;

/**
 * UAE take-home salary.
 *
 * - No personal income tax in the UAE.
 * - Expatriates: no mandatory pension deduction → net = gross.
 * - UAE nationals (GPSSA, private sector): employee pension share depends on
 *   joining date — 5% for pre-Oct-2023 joiners (Law 7/1999) or 11% for those
 *   who joined after (Law 57/2023). Applied to the gross contribution salary
 *   (simplification stated on-page; GPSSA defines a contribution salary).
 */
class SalaryUaeFeature
{
    public const EMPLOYEE_RATES = [
        'expat' => 0.0,
        'national_pre2023' => 0.05,
        'national_post2023' => 0.11,
    ];

    public function calculate(float $grossSalary, string $category): array
    {
        $steps = [];
        $rate = self::EMPLOYEE_RATES[$category];

        $steps[] = [
            'label' => __('Gross monthly salary'),
            'value' => number_format($grossSalary, 2),
            'detail' => __('No personal income tax in the UAE'),
        ];

        $pension = $grossSalary * $rate;

        if ($rate > 0) {
            $steps[] = [
                'label' => __('GPSSA pension contribution').' ('.($rate * 100).'%)',
                'value' => number_format($pension, 2),
                'detail' => number_format($grossSalary, 2).' × '.($rate * 100).'%',
            ];
        } else {
            $steps[] = [
                'label' => __('Pension contribution'),
                'value' => '0.00',
                'detail' => __('Expatriates have no mandatory pension deduction'),
            ];
        }

        $net = $grossSalary - $pension;

        $steps[] = [
            'label' => __('Net monthly salary'),
            'value' => number_format($net, 2),
            'detail' => number_format($grossSalary, 2).' − '.number_format($pension, 2),
        ];

        return [
            'gross' => round($grossSalary, 2),
            'pension' => round($pension, 2),
            'rate' => $rate,
            'net' => round($net, 2),
            'annual_net' => round($net * 12, 2),
            'steps' => $steps,
        ];
    }
}
