<?php

use Illuminate\Support\Facades\Route;
use Modules\AuthUserManagement\Http\Controllers\AuthUserManagementController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('authusermanagements', AuthUserManagementController::class)->names('authusermanagement');
});
