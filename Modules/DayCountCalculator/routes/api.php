<?php

use Illuminate\Support\Facades\Route;
use Modules\DayCountCalculator\Http\Controllers\DayCountCalculatorController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('daycountcalculators', DayCountCalculatorController::class)->names('daycountcalculator');
});
