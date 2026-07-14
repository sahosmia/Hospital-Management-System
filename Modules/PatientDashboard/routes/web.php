<?php

use Illuminate\Support\Facades\Route;
use Modules\PatientDashboard\Http\Controllers\PatientDashboardController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('patientdashboards', PatientDashboardController::class)->names('patientdashboard');
});
