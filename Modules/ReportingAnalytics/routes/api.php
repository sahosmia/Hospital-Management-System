<?php

use Illuminate\Support\Facades\Route;
use Modules\ReportingAnalytics\Http\Controllers\ReportingAnalyticsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('reportinganalytics', ReportingAnalyticsController::class)->names('reportinganalytics');
});
