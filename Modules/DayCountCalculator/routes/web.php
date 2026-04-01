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

Route::prefix('calculator')->name('calculator.')->group(function () {
    // Main calculator
    Route::get('/', [CalculatorController::class, 'index'])->name('index');
    Route::post('/calculate', [CalculatorController::class, 'calculate'])->name('calculate');
    Route::get('/history', [CalculatorController::class, 'history'])->name('history');

    // Educational pages
    Route::get('/learn/{conventionType}', [CalculatorController::class, 'educate'])->name('educate');

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
    Route::post('/export/{format}', [ComparisonController::class, 'export'])->name('export');
});

Route::prefix('subscribe')->name('subscribe.')->group(function () {
    // Email subscription
    Route::post('/', [SubscriberController::class, 'subscribe'])->name('subscribe');
    Route::get('/verify/{token}', [SubscriberController::class, 'verify'])->name('verify');
    Route::get('/unsubscribe/{email}/{token}', [SubscriberController::class, 'unsubscribe'])->name('unsubscribe');
    Route::post('/resubscribe', [SubscriberController::class, 'resubscribe'])->name('resubscribe');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Admin panel routes (protected by auth and admin middleware)
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/calculations', [AdminController::class, 'calculations'])->name('calculations');
    Route::get('/subscribers', [AdminController::class, 'subscribers'])->name('subscribers');
    Route::get('/export/{type}', [AdminController::class, 'export'])->name('export');
});
