<?php

namespace Modules\GulfCalculators\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Modules\GulfCalculators\Features\CorporateTaxUaeFeature;
use Modules\GulfCalculators\Features\GosiKsaFeature;
use Modules\GulfCalculators\Features\GratuityKsaFeature;
use Modules\GulfCalculators\Features\GratuityUaeFeature;
use Modules\GulfCalculators\Features\IqamaFeesKsaFeature;
use Modules\GulfCalculators\Features\LoanFeature;
use Modules\GulfCalculators\Features\MortgageAffordabilityUaeFeature;
use Modules\GulfCalculators\Features\OverstayUaeFeature;
use Modules\GulfCalculators\Features\PersonalLoanUaeFeature;
use Modules\GulfCalculators\Features\RettKsaFeature;
use Modules\GulfCalculators\Features\SalaryUaeFeature;
use Modules\GulfCalculators\Features\VatFeature;
use Modules\GulfCalculators\Features\ZakatFeature;
use Modules\GulfCalculators\Services\GoldPriceService;

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

    /* ---------------- GOSI / Net salary KSA ---------------- */

    public function gosi()
    {
        return view('gulfcalculators::gosi-ksa', [
            'page' => config('gulfcalculators.pages.gosi-calculator-saudi-arabia'),
            'result' => null,
        ]);
    }

    public function gosiCalculate(Request $request)
    {
        $data = $request->validate([
            'basic_salary' => 'required|numeric|min:1|max:10000000',
            'housing_allowance' => 'nullable|numeric|min:0|max:10000000',
            'other_allowances' => 'nullable|numeric|min:0|max:10000000',
            'nationality' => 'required|in:saudi,expat',
        ]);

        $result = (new GosiKsaFeature)->calculate(
            (float) $data['basic_salary'],
            (float) ($data['housing_allowance'] ?? 0),
            (float) ($data['other_allowances'] ?? 0),
            $data['nationality'],
        );

        return view('gulfcalculators::gosi-ksa', [
            'page' => config('gulfcalculators.pages.gosi-calculator-saudi-arabia'),
            'result' => $result,
        ]);
    }

    /* ---------------- Take-home salary UAE ---------------- */

    public function salaryUae()
    {
        return view('gulfcalculators::salary-uae', [
            'page' => config('gulfcalculators.pages.salary-calculator-uae'),
            'result' => null,
        ]);
    }

    public function salaryUaeCalculate(Request $request)
    {
        $data = $request->validate([
            'gross_salary' => 'required|numeric|min:1|max:10000000',
            'category' => 'required|in:expat,national_pre2023,national_post2023',
        ]);

        $result = (new SalaryUaeFeature)->calculate((float) $data['gross_salary'], $data['category']);

        return view('gulfcalculators::salary-uae', [
            'page' => config('gulfcalculators.pages.salary-calculator-uae'),
            'result' => $result,
        ]);
    }

    /* ---------------- Loan / EMI ---------------- */

    public function loan()
    {
        return view('gulfcalculators::loan', [
            'page' => config('gulfcalculators.pages.loan-calculator'),
            'result' => null,
        ]);
    }

    public function loanCalculate(Request $request)
    {
        $data = $request->validate([
            'currency' => 'required|in:AED,SAR',
            'principal' => 'required|numeric|min:100|max:1000000000',
            'annual_rate' => 'required|numeric|min:0|max:100',
            'months' => 'required|integer|min:1|max:480',
            'method' => 'required|in:flat,reducing',
        ]);

        $result = (new LoanFeature)->calculate(
            (float) $data['principal'],
            (float) $data['annual_rate'],
            (int) $data['months'],
            $data['method'],
        );
        $result['currency'] = $data['currency'];

        return view('gulfcalculators::loan', [
            'page' => config('gulfcalculators.pages.loan-calculator'),
            'result' => $result,
        ]);
    }

    /* ---------------- Zakat ---------------- */

    public function zakat(GoldPriceService $goldPrice)
    {
        return view('gulfcalculators::zakat', [
            'page' => config('gulfcalculators.pages.zakat-calculator'),
            'defaults' => $this->zakatDefaults($goldPrice),
            'result' => null,
        ]);
    }

    /**
     * Static config defaults, upgraded to live market prices when the
     * gold price API is configured and reachable.
     */
    private function zakatDefaults(GoldPriceService $goldPrice): array
    {
        $defaults = config('gulfcalculators.zakat');
        $defaults['live'] = false;

        foreach (array_keys($defaults['gold_price_per_gram']) as $currency) {
            $livePrice = $goldPrice->pricePerGram($currency);

            if ($livePrice !== null) {
                $defaults['gold_price_per_gram'][$currency] = $livePrice;
                $defaults['live'] = true;
            }
        }

        if ($defaults['live']) {
            $defaults['prices_updated_at'] = now()->toDateString();
        }

        return $defaults;
    }

    public function zakatCalculate(Request $request, GoldPriceService $goldPrice)
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
            'defaults' => $this->zakatDefaults($goldPrice),
            'result' => $result,
        ]);
    }

    /* ---------------- Iqama fees KSA ---------------- */

    public function iqama()
    {
        return view('gulfcalculators::iqama-ksa', [
            'page' => config('gulfcalculators.pages.iqama-fees-calculator-saudi-arabia'),
            'result' => null,
        ]);
    }

    public function iqamaCalculate(Request $request)
    {
        $data = $request->validate([
            'months' => 'required|integer|in:3,6,9,12',
            'worker_type' => 'required|in:company,domestic',
            'saudization' => 'required|in:compliant,noncompliant',
            'dependents' => 'nullable|integer|min:0|max:30',
        ]);

        $result = (new IqamaFeesKsaFeature)->calculate(
            (int) $data['months'],
            $data['worker_type'],
            $data['saudization'],
            (int) ($data['dependents'] ?? 0),
        );

        return view('gulfcalculators::iqama-ksa', [
            'page' => config('gulfcalculators.pages.iqama-fees-calculator-saudi-arabia'),
            'result' => $result,
        ]);
    }

    /* ---------------- Overstay fine UAE ---------------- */

    public function overstay()
    {
        return view('gulfcalculators::overstay-uae', [
            'page' => config('gulfcalculators.pages.overstay-fine-calculator-uae'),
            'result' => null,
        ]);
    }

    public function overstayCalculate(Request $request)
    {
        $data = $request->validate([
            'visa_type' => 'required|in:tourist,residence,golden',
            'expiry_date' => 'required|date',
            'settlement_date' => 'required|date|after_or_equal:expiry_date',
        ]);

        $result = (new OverstayUaeFeature)->calculate(
            $data['visa_type'],
            Carbon::parse($data['expiry_date']),
            Carbon::parse($data['settlement_date']),
        );

        return view('gulfcalculators::overstay-uae', [
            'page' => config('gulfcalculators.pages.overstay-fine-calculator-uae'),
            'result' => $result,
        ]);
    }

    /* ---------------- Corporate tax UAE ---------------- */

    public function corporateTax()
    {
        return view('gulfcalculators::corporate-tax-uae', [
            'page' => config('gulfcalculators.pages.corporate-tax-calculator-uae'),
            'result' => null,
        ]);
    }

    public function corporateTaxCalculate(Request $request)
    {
        $data = $request->validate([
            'revenue' => 'required|numeric|min:0|max:1000000000000',
            'taxable_income' => 'required|numeric|min:0|max:1000000000000',
            'sbr_elected' => 'nullable|boolean',
        ]);

        $result = (new CorporateTaxUaeFeature)->calculate(
            (float) $data['revenue'],
            (float) $data['taxable_income'],
            $request->boolean('sbr_elected'),
        );

        return view('gulfcalculators::corporate-tax-uae', [
            'page' => config('gulfcalculators.pages.corporate-tax-calculator-uae'),
            'result' => $result,
        ]);
    }

    /* ---------------- Mortgage affordability UAE ---------------- */

    public function mortgage()
    {
        return view('gulfcalculators::mortgage-uae', [
            'page' => config('gulfcalculators.pages.mortgage-affordability-calculator-uae'),
            'result' => null,
        ]);
    }

    public function mortgageCalculate(Request $request)
    {
        $data = $request->validate([
            'monthly_income' => 'required|numeric|min:1|max:100000000',
            'obligations' => 'nullable|numeric|min:0|max:100000000',
            'annual_rate' => 'required|numeric|min:0|max:30',
            'years' => 'required|integer|min:1|max:25',
            'buyer' => 'required|in:expat,national',
            'property_type' => 'required|in:first,second',
        ]);

        $result = (new MortgageAffordabilityUaeFeature)->calculate(
            (float) $data['monthly_income'],
            (float) ($data['obligations'] ?? 0),
            (float) $data['annual_rate'],
            (int) $data['years'],
            $data['buyer'],
            $data['property_type'],
        );

        return view('gulfcalculators::mortgage-uae', [
            'page' => config('gulfcalculators.pages.mortgage-affordability-calculator-uae'),
            'result' => $result,
        ]);
    }

    /* ---------------- Personal loan eligibility UAE ---------------- */

    public function personalLoan()
    {
        return view('gulfcalculators::personal-loan-uae', [
            'page' => config('gulfcalculators.pages.personal-loan-eligibility-calculator-uae'),
            'result' => null,
        ]);
    }

    public function personalLoanCalculate(Request $request)
    {
        $data = $request->validate([
            'monthly_salary' => 'required|numeric|min:1|max:100000000',
            'obligations' => 'nullable|numeric|min:0|max:100000000',
            'annual_rate' => 'required|numeric|min:0|max:60',
            'months' => 'required|integer|min:1|max:48',
        ]);

        $result = (new PersonalLoanUaeFeature)->calculate(
            (float) $data['monthly_salary'],
            (float) ($data['obligations'] ?? 0),
            (float) $data['annual_rate'],
            (int) $data['months'],
        );

        return view('gulfcalculators::personal-loan-uae', [
            'page' => config('gulfcalculators.pages.personal-loan-eligibility-calculator-uae'),
            'result' => $result,
        ]);
    }

    /* ---------------- RETT KSA ---------------- */

    public function rett()
    {
        return view('gulfcalculators::rett-ksa', [
            'page' => config('gulfcalculators.pages.rett-calculator-saudi-arabia'),
            'result' => null,
        ]);
    }

    public function rettCalculate(Request $request)
    {
        $data = $request->validate([
            'property_value' => 'required|numeric|min:1|max:100000000000',
            'first_home' => 'nullable|boolean',
        ]);

        $result = (new RettKsaFeature)->calculate(
            (float) $data['property_value'],
            $request->boolean('first_home'),
        );

        return view('gulfcalculators::rett-ksa', [
            'page' => config('gulfcalculators.pages.rett-calculator-saudi-arabia'),
            'result' => $result,
        ]);
    }
}
