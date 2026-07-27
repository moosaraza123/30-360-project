<?php

namespace Modules\GulfCalculators\Features;

/**
 * Saudi iqama renewal cost — residency fee + work-permit levy + dependent levy.
 *
 * - Iqama (residency) fee: SAR 650/year for company workers, SAR 600/year for
 *   domestic workers, pro-rated to the renewal period (3/6/9/12 months).
 * - Work-permit levy (Maktab Amal): SAR 700/month where expats ≤ Saudis in the
 *   company, SAR 800/month where expats exceed Saudis. Not charged for
 *   domestic workers.
 * - Dependent levy: SAR 400/month per dependent.
 *
 * By law the employer bears the iqama fee and work-permit levy; the dependent
 * levy is borne by the employee unless the contract says otherwise.
 */
class IqamaFeesKsaFeature
{
    public const IQAMA_ANNUAL_FEE = ['company' => 650.0, 'domestic' => 600.0];

    public const LEVY_MONTHLY = ['compliant' => 700.0, 'noncompliant' => 800.0];

    public const DEPENDENT_MONTHLY = 400.0;

    public function calculate(int $months, string $workerType, string $saudization, int $dependents): array
    {
        $steps = [];

        $iqamaFee = self::IQAMA_ANNUAL_FEE[$workerType] * $months / 12;
        $steps[] = [
            'label' => __('Iqama (residency) fee'),
            'value' => number_format($iqamaFee, 2),
            'detail' => number_format(self::IQAMA_ANNUAL_FEE[$workerType], 0).' × '.$months.'/12',
        ];

        $levyMonthly = $workerType === 'company' ? self::LEVY_MONTHLY[$saudization] : 0.0;
        $levy = $levyMonthly * $months;

        if ($workerType === 'company') {
            $steps[] = [
                'label' => __('Work-permit levy'),
                'value' => number_format($levy, 2),
                'detail' => number_format($levyMonthly, 0).' × '.$months.' '.__('months'),
            ];
        }

        $dependentLevy = self::DEPENDENT_MONTHLY * $dependents * $months;

        if ($dependents > 0) {
            $steps[] = [
                'label' => __('Dependent levy'),
                'value' => number_format($dependentLevy, 2),
                'detail' => '400 × '.$dependents.' × '.$months.' '.__('months'),
            ];
        }

        $employerPays = $iqamaFee + $levy;
        $employeePays = $dependentLevy;
        $total = $employerPays + $employeePays;

        $steps[] = [
            'label' => __('Total renewal cost'),
            'value' => number_format($total, 2),
            'detail' => number_format($iqamaFee, 2).' + '.number_format($levy, 2).' + '.number_format($dependentLevy, 2),
        ];

        return [
            'iqama_fee' => round($iqamaFee, 2),
            'levy' => round($levy, 2),
            'dependent_levy' => round($dependentLevy, 2),
            'employer_pays' => round($employerPays, 2),
            'employee_pays' => round($employeePays, 2),
            'total' => round($total, 2),
            'months' => $months,
            'steps' => $steps,
        ];
    }
}
