<?php

use Illuminate\Support\Facades\Route;
use Modules\BedAdmission\Http\Controllers\BedAdmissionController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('bedadmissions', BedAdmissionController::class)->names('bedadmission');
});
