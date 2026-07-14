<?php

use Illuminate\Support\Facades\Route;
use Modules\RatingsReviews\Http\Controllers\RatingsReviewsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('ratingsreviews', RatingsReviewsController::class)->names('ratingsreviews');
});
