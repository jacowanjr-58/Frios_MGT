<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Franchise\InventoryReceiveController;
use App\Http\Controllers\Franchise\InventoryAdjustmentController;
use App\Http\Controllers\Franchise\InventoryAllocationController;
use App\Http\Controllers\Franchise\InventoryRemovalController;

Route::middleware(['auth', 'role:franchise_admin'])
    ->prefix('franchise/inventory')
    ->name('franchise.inventory.')
    ->group(function () {
        // Receive stock
        Route::get('receive', [InventoryReceiveController::class, 'receiveForm'])->name('receive.form');
        Route::post('receive', [InventoryReceiveController::class, 'receiveStore'])->name('receive.store');

        // Adjust inventory
        Route::get('adjust', [InventoryAdjustmentController::class, 'adjustForm'])->name('adjust.form');
        Route::post('adjust', [InventoryAdjustmentController::class, 'adjustUpdate'])->name('adjust.update');

        // Allocate inventory
        Route::get('locations', [InventoryAllocationController::class, 'inventoryLocations'])->name('locations');
        Route::post('allocate', [InventoryAllocationController::class, 'allocateInventory'])->name('allocate-inventory');

        // Remove / confirm removals
        Route::get('remove', [InventoryRemovalController::class, 'showRemovalQueue'])->name('remove');
        Route::post('remove/{id}/confirm', [InventoryRemovalController::class, 'confirm'])->name('remove.confirm');
        Route::delete('remove/{id}/cancel', [InventoryRemovalController::class, 'cancel'])->name('remove.cancel');
    });
