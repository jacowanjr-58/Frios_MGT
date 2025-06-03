<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Franchise\InventoryController;
use App\Http\Controllers\Franchise\InventoryReceiveController;
use App\Http\Controllers\Franchise\InventoryAdjustmentController;
use App\Http\Controllers\Franchise\InventoryAllocationController;
use App\Http\Controllers\Franchise\InventoryRemovalController;
use App\Http\Controllers\Franchise\BulkInventoryAllocationController;

/*
|--------------------------------------------------------------------------
| Franchise Inventory Routes
|--------------------------------------------------------------------------
|
| These routes cover:
|  1. Master‐inventory CRUD (InventoryController)
|  2. Receiving new stock (InventoryReceiveController)
|  3. Manual adjustments (InventoryAdjustmentController)
|  4. Allocating to locations (InventoryAllocationController)
|  5. Confirming/cancelling removals (InventoryRemovalController)
|
| All are wrapped in the same prefix and middleware as before.
|
*/

Route::middleware(['auth', 'role:franchise_admin|franchise_manager'])
     ->prefix('franchise/inventory')
     ->name('franchise.inventory.')
     ->group(function () {

    /**
     * ----------------------------------------------------------------------
     * 1) Master Inventory CRUD (InventoryController)
     * ----------------------------------------------------------------------
     *   GET    /franchise/inventory               → index
     *   GET    /franchise/inventory/create        → create
     *   POST   /franchise/inventory/store         → store
     *   GET    /franchise/inventory/{inventory}   → show (optional—remove if unused)
     *   GET    /franchise/inventory/{inventory}/edit  → edit
     *   PUT    /franchise/inventory/{inventory}   → update
     *   DELETE /franchise/inventory/{inventory}   → destroy
     */
    Route::get('/', [InventoryController::class, 'index'])
         ->name('index');

    Route::get('create', [InventoryController::class, 'create'])
         ->name('create');

    Route::post('store', [InventoryController::class, 'store'])
         ->name('store');

    // (If you need a “show” page for a single master line, uncomment next two lines)
    // Route::get('{inventoryMaster}', [InventoryController::class, 'show'])
    //      ->name('show');

    Route::get('{inventoryMaster}/edit', [InventoryController::class, 'edit'])
         ->name('edit');

    Route::put('{inventoryMaster}', [InventoryController::class, 'update'])
         ->name('update');

    Route::delete('{inventoryMaster}', [InventoryController::class, 'destroy'])
         ->name('destroy');


    /**
     * ----------------------------------------------------------------------
     * 2) Receive New Stock (InventoryReceiveController)
     * ----------------------------------------------------------------------
     *   GET  /franchise/inventory/receive      → receiveForm
     *   POST /franchise/inventory/receive      → receiveStore
     */
    Route::get('receive', [InventoryReceiveController::class, 'receiveForm'])
         ->name('receive.form');

    Route::post('receive', [InventoryReceiveController::class, 'receiveStore'])
         ->name('receive.store');


    /**
     * ----------------------------------------------------------------------
     * 3) Manual Adjust / Onboard Inventory (InventoryAdjustmentController)
     * ----------------------------------------------------------------------
     *   GET  /franchise/inventory/adjust      → adjustForm
     *   POST /franchise/inventory/adjust      → adjustUpdate
     */
    Route::get('adjust', [InventoryAdjustmentController::class, 'adjustForm'])
         ->name('adjust.form');

    Route::post('adjust', [InventoryAdjustmentController::class, 'adjustUpdate'])
         ->name('adjust.update');


    /**
     * ----------------------------------------------------------------------
     * 4) Allocate Stock to Locations (InventoryAllocationController)
     * ----------------------------------------------------------------------
     *   GET  /franchise/inventory/locations   → inventoryLocations
     *   POST /franchise/inventory/allocate    → allocateInventory
     */
    Route::get('locations', [InventoryAllocationController::class, 'inventoryLocations'])
         ->name('locations');

    Route::post('allocate', [InventoryAllocationController::class, 'allocateInventory'])
         ->name('allocate-inventory');


    /**
     * ----------------------------------------------------------------------
     * 5) Removal Queue (InventoryRemovalController)
     * ----------------------------------------------------------------------
     *   GET    /franchise/inventory/remove                → showRemovalQueue
     *   POST   /franchise/inventory/remove/{id}/confirm   → confirm
     *   DELETE /franchise/inventory/remove/{id}/cancel    → cancel
     */
    Route::get('remove', [InventoryRemovalController::class, 'showRemovalQueue'])
         ->name('remove');

    Route::post('remove/{id}/confirm', [InventoryRemovalController::class, 'confirm'])
         ->name('remove.confirm');

    Route::delete('remove/{id}/cancel', [InventoryRemovalController::class, 'cancel'])
         ->name('remove.cancel');


         // Optional: enable for franchisees
    Route::get('/bulk-edit', [InventoryController::class, 'bulkEdit'])->name('bulk_edit');
    Route::post('/bulk-update', [InventoryController::class, 'bulkUpdate'])->name('bulk_update');

    Route::get('/bulk-allocation', [BulkInventoryAllocationController::class, 'index'])->name('bulk-allocation.index');
    Route::post('/bulk-allocation', [BulkInventoryAllocationController::class, 'allocate'])->name('bulk-allocation.store');

});
