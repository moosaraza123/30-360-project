<?php

namespace Modules\GulfCalculators\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Modules\GulfCalculators\Services\GoldPriceService;
use Tests\TestCase;

class GoldPriceServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_returns_null_without_api_key(): void
    {
        config(['gulfcalculators.zakat.api_key' => null]);

        $this->assertNull((new GoldPriceService)->pricePerGram('AED'));
    }

    public function test_converts_ounce_price_to_pegged_gram_prices(): void
    {
        config(['gulfcalculators.zakat.api_key' => 'test-key']);

        Http::fake([
            'api.metalpriceapi.com/*' => Http::response([
                'success' => true,
                'rates' => ['USDXAU' => 3110.34768], // → exactly 100 USD/gram
            ]),
        ]);

        $service = new GoldPriceService;

        $this->assertEqualsWithDelta(367.25, $service->pricePerGram('AED'), 0.01); // 100 × 3.6725
        $this->assertEqualsWithDelta(375.00, $service->pricePerGram('SAR'), 0.01); // 100 × 3.75
        $this->assertNull($service->pricePerGram('USD')); // unsupported currency
    }

    public function test_falls_back_to_null_on_api_failure(): void
    {
        config(['gulfcalculators.zakat.api_key' => 'test-key']);

        Http::fake([
            'api.metalpriceapi.com/*' => Http::response(['success' => false], 500),
        ]);

        $this->assertNull((new GoldPriceService)->pricePerGram('AED'));
    }

    public function test_zakat_page_shows_fallback_when_api_unconfigured(): void
    {
        config(['gulfcalculators.zakat.api_key' => null]);

        $this->get('/zakat-calculator')
            ->assertOk()
            ->assertSee(__('Default from'));
    }
}
