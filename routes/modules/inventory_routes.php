<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Franchise\InventoryController;
use App\Http\Controllers\Franchise\InventoryReceiveController;
use App\Http\Controllers\Franchise\InventoryAdjustmentController;
use App\Http\Controllers\Franchise\InventoryAllocationController;
use App\Http\Controllers\Franchise\InventoryRemovalController;

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

Route::middleware(['auth'])
    ->prefix('franchise/{franchise}/inventory')
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

        // View inventory routes
        Route::middleware('permission:inventory.view')->group(function () {
            Route::get('/', [InventoryController::class, 'index'])
                ->name('index')->middleware('permission:inventory.view');
        });

        Route::middleware('permission:inventory.create')->group(function () {
            Route::get('create', [InventoryController::class, 'create'])
                ->name('create');

            Route::post('store', [InventoryController::class, 'store'])
                ->name('store');
        });

        // (If you need a "show" page for a single master line, uncomment next two lines)
        // Route::get('{inventoryMaster}', [InventoryController::class, 'show'])
        //      ->name('show')->middleware('permission:inventory.view');

        Route::middleware('permission:inventory.edit')->group(function () {
            Route::get('{inventoryMaster}/edit', [InventoryController::class, 'edit'])
                ->name('edit');

            Route::put('{inventoryMaster}', [InventoryController::class, 'update'])
                ->name('update');
        });

        Route::middleware('permission:inventory.delete')->group(function () {
            Route::delete('{inventoryMaster}', [InventoryController::class, 'destroy'])
                ->name('destroy');
        });


        /**
         * ----------------------------------------------------------------------
         * 2) Receive New Stock (InventoryAllocationController)
         * ----------------------------------------------------------------------
         *
         */

        // Show the "Confirm Delivery" form
        Route::get('{order}/confirm_delivery', [InventoryAllocationController::class, 'showConfirmDelivery'])
            ->name('confirm_delivery')->middleware('permission:inventory.receive');

        // Handle the form submission
        Route::post('{order}/confirm_delivery', [InventoryAllocationController::class, 'postConfirmDelivery'])
            ->name('confirm_delivery.store')->middleware('permission:inventory.receive');

        /* Route::get('receive', [InventoryReceiveController::class, 'receiveForm'])
         ->name('receive.form');

    Route::post('receive', [InventoryReceiveController::class, 'receiveStore'])
         ->name('receive.store'); */


        /**
         * ----------------------------------------------------------------------
         * 3) Manual Adjust / Onboard Inventory (InventoryAdjustmentController)
         * ----------------------------------------------------------------------
         *   GET  /franchise/inventory/adjust      → adjustForm
         *   POST /franchise/inventory/adjust      → adjustUpdate
         */
        Route::get('adjust', [InventoryAdjustmentController::class, 'adjustForm'])
            ->name('adjust.form')->middleware('permission:inventory.adjust');

        Route::post('adjust', [InventoryAdjustmentController::class, 'adjustUpdate'])
            ->name('adjust.update')->middleware('permission:inventory.adjust');


        /**
         * ----------------------------------------------------------------------
         * 4) Allocate Stock to Locations (InventoryAllocationController)
         * ----------------------------------------------------------------------
         *   GET  /franchise/inventory/locations   → inventoryLocations
         *   POST /franchise/inventory/allocate    → allocateInventory
         */
        Route::get('locations', [InventoryAllocationController::class, 'inventoryLocations'])
            ->name('locations')->middleware('permission:inventory.allocate');

        Route::post('allocate', [InventoryAllocationController::class, 'allocateInventory'])
            ->name('allocate-inventory')->middleware('permission:inventory.allocate');


        /**
         * ----------------------------------------------------------------------
         * 5) Removal Queue (InventoryRemovalController)
         * ----------------------------------------------------------------------
         *   GET    /franchise/inventory/remove                → showRemovalQueue
         *   POST   /franchise/inventory/remove/{id}/confirm   → confirm
         *   DELETE /franchise/inventory/remove/{id}/cancel    → cancel
         */
        Route::middleware('permission:inventory.remove')->group(function () {
            Route::get('remove', [InventoryRemovalController::class, 'showRemovalQueue'])
                ->name('remove');

            Route::post('remove/{id}/confirm', [InventoryRemovalController::class, 'confirm'])
                ->name('remove.confirm');

            Route::delete('remove/{id}/cancel', [InventoryRemovalController::class, 'cancel'])
                ->name('remove.cancel');
        });


        /**
         * ----------------------------------------------------------------------
         * 6) Bulk Price Update (InventoryAdjustmentlController)
         * ----------------------------------------------------------------------
         *
         *
         */
        Route::middleware('permission:inventory.bulk_price')->group(function () {
            Route::get('bulk-price', [InventoryAdjustmentController::class, 'showBulkPriceForm'])
                ->name('bulk_price.form');

            Route::post('bulk-price', [InventoryAdjustmentController::class, 'updateBulkPrice'])
                ->name('bulk_price.update');
        });
    });
