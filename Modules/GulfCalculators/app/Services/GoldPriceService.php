<?php

namespace Modules\GulfCalculators\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Live gold price for the zakat nisab, via metalpriceapi.com.
 *
 * - Result is cached for 12 hours (the free tier has a monthly quota, and
 *   intraday precision is irrelevant for nisab purposes).
 * - AED and SAR are USD-pegged, so a fixed conversion is exact enough.
 * - Any failure returns null and the caller falls back to the static
 *   defaults in config('gulfcalculators.zakat').
 */
class GoldPriceService
{
    private const GRAMS_PER_TROY_OUNCE = 31.1034768;

    private const USD_PEGS = [
        'AED' => 3.6725,
        'SAR' => 3.75,
    ];

    public function pricePerGram(string $currency): ?float
    {
        $peg = self::USD_PEGS[$currency] ?? null;
        $usdPerGram = $this->usdPerGram();

        if ($peg === null || $usdPerGram === null) {
            return null;
        }

        return round($usdPerGram * $peg, 2);
    }

    private function usdPerGram(): ?float
    {
        $apiKey = config('gulfcalculators.zakat.api_key');

        if (! $apiKey) {
            return null;
        }

        return Cache::remember('gold_price_usd_per_gram', now()->addHours(12), function () use ($apiKey) {
            try {
                $response = Http::timeout(5)->get('https://api.metalpriceapi.com/v1/latest', [
                    'api_key' => $apiKey,
                    'base' => 'USD',
                    'currencies' => 'XAU',
                ]);

                $usdPerOunce = $response->json('rates.USDXAU');

                if (! $response->json('success') || ! is_numeric($usdPerOunce) || $usdPerOunce <= 0) {
                    Log::warning('Gold price API returned unusable payload', ['body' => $response->body()]);

                    return null;
                }

                return $usdPerOunce / self::GRAMS_PER_TROY_OUNCE;
            } catch (\Throwable $e) {
                Log::warning('Gold price API request failed', ['error' => $e->getMessage()]);

                return null;
            }
        });
    }
}
