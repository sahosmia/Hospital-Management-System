<?php

use Illuminate\Support\Facades\Route;
use Modules\BedAdmission\Http\Controllers\BedAdmissionController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('bedadmissions', BedAdmissionController::class)->names('bedadmission');
});
