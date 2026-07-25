<?php

namespace Modules\GulfCalculators\Features;

/**
 * VAT add/remove calculator.
 * UAE: 5% (Federal Decree-Law No. 8 of 2017).
 * KSA: 15% (ZATCA, rate effective 1 July 2020).
 */
class VatFeature
{
    public const MODE_ADD = 'add';

    public const MODE_REMOVE = 'remove';

    public function calculate(float $amount, float $rate, string $mode): array
    {
        if ($mode === self::MODE_REMOVE) {
            // Amount is VAT-inclusive: extract the tax portion
            $net = $amount / (1 + $rate);
            $vat = $amount - $net;
            $gross = $amount;
        } else {
            // Amount is net: add tax on top
            $net = $amount;
            $vat = $amount * $rate;
            $gross = $amount + $vat;
        }

        return [
            'mode' => $mode,
            'rate' => $rate,
            'net' => round($net, 2),
            'vat' => round($vat, 2),
            'gross' => round($gross, 2),
        ];
    }
}
