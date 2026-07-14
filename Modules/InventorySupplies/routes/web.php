<?php

use Illuminate\Support\Facades\Route;
use Modules\InventorySupplies\Http\Controllers\InventorySuppliesController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('inventorysupplies', InventorySuppliesController::class)->names('inventorysupplies');
});
