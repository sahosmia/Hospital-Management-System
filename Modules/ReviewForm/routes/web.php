<?php

use Illuminate\Support\Facades\Route;
use Modules\ReviewForm\Http\Controllers\ReviewFormController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('reviewforms', ReviewFormController::class)->names('reviewform');
});
