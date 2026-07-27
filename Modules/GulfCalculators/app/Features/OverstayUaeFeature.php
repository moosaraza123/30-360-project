<?php

namespace Modules\GulfCalculators\Features;

use Carbon\Carbon;

/**
 * UAE visa overstay fine — unified ICP rule: AED 50 per day of overstay for
 * every visa type, counted after the applicable grace period ends.
 *
 * Grace periods: tourist / visit visas have none; a cancelled or expired
 * residence visa carries 30 days by default and up to 180 days for Golden
 * Visa holders.
 */
class OverstayUaeFeature
{
    public const FINE_PER_DAY = 50.0;

    public const GRACE_DAYS = [
        'tourist' => 0,
        'residence' => 30,
        'golden' => 180,
    ];

    public function calculate(string $visaType, Carbon $expiryDate, Carbon $settlementDate): array
    {
        $steps = [];

        $graceDays = self::GRACE_DAYS[$visaType];
        $graceEnd = $expiryDate->copy()->addDays($graceDays);

        $steps[] = [
            'label' => __('Grace period ends'),
            'value' => $graceEnd->toDateString(),
            'detail' => $expiryDate->toDateString().' + '.$graceDays.' '.__('days'),
        ];

        $overstayDays = max(0, (int) $graceEnd->diffInDays($settlementDate, false));

        $steps[] = [
            'label' => __('Chargeable overstay days'),
            'value' => (string) $overstayDays,
            'detail' => $graceEnd->toDateString().' → '.$settlementDate->toDateString(),
        ];

        $fine = $overstayDays * self::FINE_PER_DAY;

        $steps[] = [
            'label' => __('Overstay fine'),
            'value' => number_format($fine, 2),
            'detail' => $overstayDays.' × 50',
        ];

        return [
            'grace_days' => $graceDays,
            'grace_end' => $graceEnd->toDateString(),
            'overstay_days' => $overstayDays,
            'fine' => round($fine, 2),
            'steps' => $steps,
        ];
    }
}
