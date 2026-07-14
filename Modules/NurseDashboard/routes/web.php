<?php

use Illuminate\Support\Facades\Route;
use Modules\NurseDashboard\Http\Controllers\NurseDashboardController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('nursedashboards', NurseDashboardController::class)->names('nursedashboard');
});
