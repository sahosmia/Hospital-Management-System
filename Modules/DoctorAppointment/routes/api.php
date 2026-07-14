<?php

use Illuminate\Support\Facades\Route;
use Modules\DoctorAppointment\Http\Controllers\DoctorAppointmentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('doctorappointments', DoctorAppointmentController::class)->names('doctorappointment');
});
