<?php

use Illuminate\Support\Facades\Route;
use Modules\ReviewForm\Http\Controllers\ReviewFormController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('reviewforms', ReviewFormController::class)->names('reviewform');
});
