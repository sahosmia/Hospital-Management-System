<?php

use Illuminate\Support\Facades\Route;
use Modules\SurgeryOT\Http\Controllers\SurgeryOTController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('surgeryots', SurgeryOTController::class)->names('surgeryot');
});
