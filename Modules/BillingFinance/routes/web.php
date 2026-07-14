<?php

use Illuminate\Support\Facades\Route;
use Modules\BillingFinance\Http\Controllers\BillingFinanceController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('billingfinances', BillingFinanceController::class)->names('billingfinance');
});
