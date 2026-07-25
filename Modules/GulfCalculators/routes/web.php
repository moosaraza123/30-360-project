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
};

Route::name('gulf.')->group($calculatorRoutes);
Route::prefix('ar')->name('gulf.ar.')->group($calculatorRoutes);
