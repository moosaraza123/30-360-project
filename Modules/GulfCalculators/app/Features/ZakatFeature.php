<?php

namespace Modules\GulfCalculators\Features;

/**
 * Zakat calculator.
 *
 * - Zakatable wealth = cash + gold + silver + business assets + expected
 *   receivables − short-term debts.
 * - Nisab = 85 grams of gold (configurable price per gram; the gold nisab is
 *   used as the default threshold — methodology is stated on the page).
 * - Rate: 2.5% when wealth ≥ nisab and held for a full lunar year (hawl).
 */
class ZakatFeature
{
    public const RATE = 0.025;

    public const NISAB_GOLD_GRAMS = 85;

    public function calculate(array $inputs, float $goldPricePerGram): array
    {
        $cash = max(0.0, (float) ($inputs['cash'] ?? 0));
        $gold = max(0.0, (float) ($inputs['gold'] ?? 0));
        $silver = max(0.0, (float) ($inputs['silver'] ?? 0));
        $business = max(0.0, (float) ($inputs['business'] ?? 0));
        $receivables = max(0.0, (float) ($inputs['receivables'] ?? 0));
        $debts = max(0.0, (float) ($inputs['debts'] ?? 0));

        $totalAssets = $cash + $gold + $silver + $business + $receivables;
        $zakatable = max(0.0, $totalAssets - $debts);

        $nisab = self::NISAB_GOLD_GRAMS * $goldPricePerGram;
        $due = $zakatable >= $nisab;

        return [
            'total_assets' => round($totalAssets, 2),
            'debts' => round($debts, 2),
            'zakatable' => round($zakatable, 2),
            'nisab' => round($nisab, 2),
            'due' => $due,
            'zakat' => $due ? round($zakatable * self::RATE, 2) : 0.0,
            'rate' => self::RATE,
            'nisab_grams' => self::NISAB_GOLD_GRAMS,
            'gold_price_per_gram' => $goldPricePerGram,
        ];
    }
}
