<?php

use Illuminate\Support\Facades\Route;
use Modules\PatientDashboard\Http\Controllers\PatientDashboardController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patientdashboards', PatientDashboardController::class)->names('patientdashboard');
});
