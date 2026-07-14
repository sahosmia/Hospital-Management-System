<?php

use Illuminate\Support\Facades\Route;
use Modules\InventorySupplies\Http\Controllers\InventorySuppliesController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('inventorysupplies', InventorySuppliesController::class)->names('inventorysupplies');
});
