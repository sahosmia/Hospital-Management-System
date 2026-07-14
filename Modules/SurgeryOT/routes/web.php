<?php

use Illuminate\Support\Facades\Route;
use Modules\SurgeryOT\Http\Controllers\SurgeryOTController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('surgeryots', SurgeryOTController::class)->names('surgeryot');
});
