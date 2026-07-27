<?php

namespace Modules\GulfCalculators\Features;

/**
 * Saudi GOSI contribution & net salary — Social Insurance Law (gosi.gov.sa).
 *
 * - Contribution base = basic salary + housing allowance, capped at SAR 45,000.
 * - Saudi nationals: employee 9.75% (9% annuities + 0.75% SANED),
 *   employer 11.75% (9% annuities + 0.75% SANED + 2% occupational hazards).
 * - Expatriates: no employee deduction; employer pays 2% occupational hazards.
 * - Net salary = basic + housing + other allowances − employee contribution.
 *
 * Note shown on-page: 2024 pension reform applies transitional rates to NEW
 * entrants (gradually rising); these are the standard established rates.
 */
class GosiKsaFeature
{
    public const CAP = 45000.0;

    public const RATES = [
        'saudi' => ['employee' => 0.0975, 'employer' => 0.1175],
        'expat' => ['employee' => 0.0, 'employer' => 0.02],
    ];

    public function calculate(float $basicSalary, float $housingAllowance, float $otherAllowances, string $nationality): array
    {
        $steps = [];
        $rates = self::RATES[$nationality];

        $gross = $basicSalary + $housingAllowance + $otherAllowances;

        $base = $basicSalary + $housingAllowance;
        $capped = $base > self::CAP;
        $contributionBase = min($base, self::CAP);

        $steps[] = [
            'label' => __('GOSI contribution base'),
            'value' => number_format($contributionBase, 2),
            'detail' => number_format($basicSalary, 2).' + '.number_format($housingAllowance, 2)
                .($capped ? ' → '.__('capped at').' 45,000' : ''),
        ];

        $employee = $contributionBase * $rates['employee'];
        $employer = $contributionBase * $rates['employer'];

        if ($rates['employee'] > 0) {
            $steps[] = [
                'label' => __('Employee contribution').' ('.($rates['employee'] * 100).'%)',
                'value' => number_format($employee, 2),
                'detail' => number_format($contributionBase, 2).' × '.($rates['employee'] * 100).'%',
            ];
        }

        $steps[] = [
            'label' => __('Employer contribution').' ('.($rates['employer'] * 100).'%)',
            'value' => number_format($employer, 2),
            'detail' => number_format($contributionBase, 2).' × '.($rates['employer'] * 100).'%',
        ];

        $net = $gross - $employee;

        $steps[] = [
            'label' => __('Net monthly salary'),
            'value' => number_format($net, 2),
            'detail' => number_format($gross, 2).' − '.number_format($employee, 2),
        ];

        return [
            'gross' => round($gross, 2),
            'contribution_base' => round($contributionBase, 2),
            'base_capped' => $capped,
            'employee_contribution' => round($employee, 2),
            'employer_contribution' => round($employer, 2),
            'net' => round($net, 2),
            'annual_net' => round($net * 12, 2),
            'steps' => $steps,
        ];
    }
}
