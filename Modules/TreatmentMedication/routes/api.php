<?php

use Illuminate\Support\Facades\Route;
use Modules\TreatmentMedication\Http\Controllers\TreatmentMedicationController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('treatmentmedications', TreatmentMedicationController::class)->names('treatmentmedication');
});
