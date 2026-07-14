<?php

use Illuminate\Support\Facades\Route;
use Modules\TreatmentMedication\Http\Controllers\TreatmentMedicationController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('treatmentmedications', TreatmentMedicationController::class)->names('treatmentmedication');
});
