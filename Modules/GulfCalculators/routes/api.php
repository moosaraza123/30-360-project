<?php

use Illuminate\Support\Facades\Route;
use Modules\GulfCalculators\Http\Controllers\GulfCalculatorsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('gulfcalculators', GulfCalculatorsController::class)->names('gulfcalculators');
});
