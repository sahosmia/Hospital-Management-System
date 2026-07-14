<?php

use Illuminate\Support\Facades\Route;
use Modules\DoctorAppointment\Http\Controllers\DoctorAppointmentController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('doctorappointments', DoctorAppointmentController::class)->names('doctorappointment');
});
