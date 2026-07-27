<?php

use Illuminate\Support\Facades\Route;
use Modules\GulfCalculators\Http\Controllers\GulfCalculatorsController;

/*
| Gulf calculators — every page exists twice: English at the root and Arabic
| under /ar. The SetLocale middleware (global web group) switches the locale
| based on the /ar prefix; controllers and views are locale-agnostic.
*/

$calculatorRoutes = function () {
    Route::get('gratuity-calculator-uae', [GulfCalculatorsController::class, 'gratuityUae'])->name('gratuity.uae');
    Route::post('gratuity-calculator-uae', [GulfCalculatorsController::class, 'gratuityUaeCalculate'])->name('gratuity.uae.calculate');

    Route::get('end-of-service-calculator-saudi-arabia', [GulfCalculatorsController::class, 'gratuityKsa'])->name('gratuity.ksa');
    Route::post('end-of-service-calculator-saudi-arabia', [GulfCalculatorsController::class, 'gratuityKsaCalculate'])->name('gratuity.ksa.calculate');

    Route::get('vat-calculator-uae', [GulfCalculatorsController::class, 'vat'])->defaults('country', 'uae')->name('vat.uae');
    Route::post('vat-calculator-uae', [GulfCalculatorsController::class, 'vatCalculate'])->defaults('country', 'uae')->name('vat.uae.calculate');

    Route::get('vat-calculator-saudi-arabia', [GulfCalculatorsController::class, 'vat'])->defaults('country', 'ksa')->name('vat.ksa');
    Route::post('vat-calculator-saudi-arabia', [GulfCalculatorsController::class, 'vatCalculate'])->defaults('country', 'ksa')->name('vat.ksa.calculate');

    Route::get('zakat-calculator', [GulfCalculatorsController::class, 'zakat'])->name('zakat');
    Route::post('zakat-calculator', [GulfCalculatorsController::class, 'zakatCalculate'])->name('zakat.calculate');

    Route::get('gosi-calculator-saudi-arabia', [GulfCalculatorsController::class, 'gosi'])->name('gosi');
    Route::post('gosi-calculator-saudi-arabia', [GulfCalculatorsController::class, 'gosiCalculate'])->name('gosi.calculate');

    Route::get('salary-calculator-uae', [GulfCalculatorsController::class, 'salaryUae'])->name('salary.uae');
    Route::post('salary-calculator-uae', [GulfCalculatorsController::class, 'salaryUaeCalculate'])->name('salary.uae.calculate');

    Route::get('loan-calculator', [GulfCalculatorsController::class, 'loan'])->name('loan');
    Route::post('loan-calculator', [GulfCalculatorsController::class, 'loanCalculate'])->name('loan.calculate');

    Route::get('iqama-fees-calculator-saudi-arabia', [GulfCalculatorsController::class, 'iqama'])->name('iqama');
    Route::post('iqama-fees-calculator-saudi-arabia', [GulfCalculatorsController::class, 'iqamaCalculate'])->name('iqama.calculate');

    Route::get('overstay-fine-calculator-uae', [GulfCalculatorsController::class, 'overstay'])->name('overstay');
    Route::post('overstay-fine-calculator-uae', [GulfCalculatorsController::class, 'overstayCalculate'])->name('overstay.calculate');

    Route::get('corporate-tax-calculator-uae', [GulfCalculatorsController::class, 'corporateTax'])->name('corporate-tax');
    Route::post('corporate-tax-calculator-uae', [GulfCalculatorsController::class, 'corporateTaxCalculate'])->name('corporate-tax.calculate');

    Route::get('mortgage-affordability-calculator-uae', [GulfCalculatorsController::class, 'mortgage'])->name('mortgage');
    Route::post('mortgage-affordability-calculator-uae', [GulfCalculatorsController::class, 'mortgageCalculate'])->name('mortgage.calculate');

    Route::get('personal-loan-eligibility-calculator-uae', [GulfCalculatorsController::class, 'personalLoan'])->name('personal-loan');
    Route::post('personal-loan-eligibility-calculator-uae', [GulfCalculatorsController::class, 'personalLoanCalculate'])->name('personal-loan.calculate');

    Route::get('rett-calculator-saudi-arabia', [GulfCalculatorsController::class, 'rett'])->name('rett');
    Route::post('rett-calculator-saudi-arabia', [GulfCalculatorsController::class, 'rettCalculate'])->name('rett.calculate');
};

Route::name('gulf.')->group($calculatorRoutes);
Route::prefix('ar')->name('gulf.ar.')->group($calculatorRoutes);
