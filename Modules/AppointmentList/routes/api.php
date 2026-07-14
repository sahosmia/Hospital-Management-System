<?php

use Illuminate\Support\Facades\Route;
use Modules\AppointmentList\Http\Controllers\AppointmentListController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('appointmentlists', AppointmentListController::class)->names('appointmentlist');
});
