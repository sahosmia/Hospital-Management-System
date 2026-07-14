<?php

use Illuminate\Support\Facades\Route;
use Modules\PatientManagement\Http\Controllers\PatientManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('patientmanagements', PatientManagementController::class)->names('patientmanagement');
});
