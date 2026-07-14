<?php

use Illuminate\Support\Facades\Route;
use Modules\PatientManagement\Http\Controllers\PatientManagementController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('patientmanagements', PatientManagementController::class)->names('patientmanagement');
});
