<?php

use Illuminate\Support\Facades\Route;
use Modules\DayCountCalculator\Http\Controllers\CalculatorController;
use Modules\DayCountCalculator\Http\Controllers\ComparisonController;
use Modules\DayCountCalculator\Http\Controllers\SubscriberController;
use Modules\DayCountCalculator\Http\Controllers\AdminController;
use Modules\DayCountCalculator\Http\Controllers\SitemapController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your module.
|
*/

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// ads.txt — required by AdSense; served only when a client ID is configured
Route::get('/ads.txt', function () {
    $client = config('daycountcalculator.adsense_client');
    abort_unless((bool) $client, 404);

    $publisherId = str_starts_with($client, 'ca-') ? substr($client, 3) : $client;

    return response("google.com, {$publisherId}, DIRECT, f08c47fec0942fa0\n", 200)
        ->header('Content-Type', 'text/plain');
})->name('ads.txt');

Route::prefix('calculator')->name('calculator.')->group(function () {
    // Main calculator
    Route::get('/', [CalculatorController::class, 'index'])->name('index');
    Route::post('/calculate', [CalculatorController::class, 'calculate'])->name('calculate');
    Route::get('/history', [CalculatorController::class, 'history'])->name('history');

    // Educational pages (convention names contain slashes, e.g. "30/360 US")
    Route::get('/learn/{conventionType}', [CalculatorController::class, 'educate'])
        ->where('conventionType', '.*')
        ->name('educate');

    // Saved calculations (authenticated users)
    Route::middleware('auth')->group(function () {
        Route::post('/save/{calculationId}', [CalculatorController::class, 'save'])->name('save');
        Route::get('/saved', [CalculatorController::class, 'savedCalculations'])->name('saved');
    });
});

Route::prefix('comparison')->name('comparison.')->group(function () {
    // Comparison tool
    Route::get('/', [ComparisonController::class, 'index'])->name('index');
    Route::post('/calculate', [ComparisonController::class, 'compare'])->name('calculate');
    Route::post('/export/{format}', [ComparisonController::class, 'export'])
        ->middleware('throttle:10,1')
        ->name('export');
});

Route::prefix('subscribe')->name('subscribe.')->group(function () {
    // Email subscription (throttled: these endpoints trigger outbound mail)
    Route::post('/', [SubscriberController::class, 'subscribe'])
        ->middleware('throttle:5,1')
        ->name('subscribe');
    Route::get('/verify/{token}', [SubscriberController::class, 'verify'])->name('verify');
    Route::get('/unsubscribe/{email}/{token}', [SubscriberController::class, 'unsubscribe'])->name('unsubscribe');
    Route::post('/resubscribe', [SubscriberController::class, 'resubscribe'])
        ->middleware('throttle:5,1')
        ->name('resubscribe');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin panel routes (protected by auth and admin middleware)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/calculations', [AdminController::class, 'calculations'])->name('calculations');
    Route::get('/subscribers', [AdminController::class, 'subscribers'])->name('subscribers');
    Route::get('/export/{type}', [AdminController::class, 'export'])->name('export');
});
