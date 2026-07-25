<?php

namespace Modules\GulfCalculators\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\GulfCalculators\Features\GratuityKsaFeature;
use Modules\GulfCalculators\Features\GratuityUaeFeature;
use Modules\GulfCalculators\Features\VatFeature;
use Modules\GulfCalculators\Features\ZakatFeature;

class GulfCalculatorsController extends Controller
{
    /* ---------------- Gratuity UAE ---------------- */

    public function gratuityUae()
    {
        return view('gulfcalculators::gratuity-uae', [
            'page' => config('gulfcalculators.pages.gratuity-calculator-uae'),
            'result' => null,
        ]);
    }

    public function gratuityUaeCalculate(Request $request)
    {
        $data = $request->validate([
            'basic_salary' => 'required|numeric|min:1|max:10000000',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $result = (new GratuityUaeFeature)->calculate(
            (float) $data['basic_salary'],
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
        );

        return view('gulfcalculators::gratuity-uae', [
            'page' => config('gulfcalculators.pages.gratuity-calculator-uae'),
            'result' => $result,
        ]);
    }

    /* ---------------- Gratuity KSA ---------------- */

    public function gratuityKsa()
    {
        return view('gulfcalculators::gratuity-ksa', [
            'page' => config('gulfcalculators.pages.end-of-service-calculator-saudi-arabia'),
            'result' => null,
        ]);
    }

    public function gratuityKsaCalculate(Request $request)
    {
        $data = $request->validate([
            'monthly_wage' => 'required|numeric|min:1|max:10000000',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'end_type' => 'required|in:resignation,termination',
        ]);

        $result = (new GratuityKsaFeature)->calculate(
            (float) $data['monthly_wage'],
            Carbon::parse($data['start_date']),
            Carbon::parse($data['end_date']),
            $data['end_type'],
        );

        return view('gulfcalculators::gratuity-ksa', [
            'page' => config('gulfcalculators.pages.end-of-service-calculator-saudi-arabia'),
            'result' => $result,
        ]);
    }

    /* ---------------- VAT ---------------- */

    public function vat(Request $request, string $country)
    {
        return $this->vatView($country, null);
    }

    public function vatCalculate(Request $request, string $country)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:1000000000',
            'mode' => 'required|in:add,remove',
        ]);

        $rate = config("gulfcalculators.vat_rates.{$country}");
        abort_unless($rate !== null, 404);

        $result = (new VatFeature)->calculate((float) $data['amount'], $rate, $data['mode']);

        return $this->vatView($country, $result);
    }

    private function vatView(string $country, ?array $result)
    {
        abort_unless(in_array($country, ['uae', 'ksa']), 404);

        $slug = $country === 'uae' ? 'vat-calculator-uae' : 'vat-calculator-saudi-arabia';

        return view('gulfcalculators::vat', [
            'page' => config("gulfcalculators.pages.{$slug}"),
            'country' => $country,
            'slug' => $slug,
            'rate' => config("gulfcalculators.vat_rates.{$country}"),
            'currency' => $country === 'uae' ? 'AED' : 'SAR',
            'result' => $result,
        ]);
    }

    /* ---------------- Zakat ---------------- */

    public function zakat()
    {
        return view('gulfcalculators::zakat', [
            'page' => config('gulfcalculators.pages.zakat-calculator'),
            'defaults' => config('gulfcalculators.zakat'),
            'result' => null,
        ]);
    }

    public function zakatCalculate(Request $request)
    {
        $data = $request->validate([
            'currency' => 'required|in:AED,SAR',
            'gold_price_per_gram' => 'required|numeric|min:0.01|max:100000',
            'cash' => 'nullable|numeric|min:0',
            'gold' => 'nullable|numeric|min:0',
            'silver' => 'nullable|numeric|min:0',
            'business' => 'nullable|numeric|min:0',
            'receivables' => 'nullable|numeric|min:0',
            'debts' => 'nullable|numeric|min:0',
        ]);

        $result = (new ZakatFeature)->calculate($data, (float) $data['gold_price_per_gram']);
        $result['currency'] = $data['currency'];

        return view('gulfcalculators::zakat', [
            'page' => config('gulfcalculators.pages.zakat-calculator'),
            'defaults' => config('gulfcalculators.zakat'),
            'result' => $result,
        ]);
    }
}
