<?php

use Illuminate\Support\Facades\Route;
use Modules\AdminDashboard\Http\Controllers\AdminDashboardController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('admindashboards', AdminDashboardController::class)->names('admindashboard');
});
