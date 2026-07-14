<?php

use Illuminate\Support\Facades\Route;
use Modules\ReportingAnalytics\Http\Controllers\ReportingAnalyticsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('reportinganalytics', ReportingAnalyticsController::class)->names('reportinganalytics');
});
