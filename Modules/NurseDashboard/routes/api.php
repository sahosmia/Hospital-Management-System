<?php

use Illuminate\Support\Facades\Route;
use Modules\NurseDashboard\Http\Controllers\NurseDashboardController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('nursedashboards', NurseDashboardController::class)->names('nursedashboard');
});
