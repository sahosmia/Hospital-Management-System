<?php

use Illuminate\Support\Facades\Route;
use Modules\RatingsReviews\Http\Controllers\RatingsReviewsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('ratingsreviews', RatingsReviewsController::class)->names('ratingsreviews');
});
