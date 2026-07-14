<?php

use Illuminate\Support\Facades\Route;
use Modules\BillingFinance\Http\Controllers\BillingFinanceController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('billingfinances', BillingFinanceController::class)->names('billingfinance');
});
