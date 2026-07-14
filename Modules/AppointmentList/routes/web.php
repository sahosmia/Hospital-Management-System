<?php

use Illuminate\Support\Facades\Route;
use Modules\AppointmentList\Http\Controllers\AppointmentListController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('appointmentlists', AppointmentListController::class)->names('appointmentlist');
});
