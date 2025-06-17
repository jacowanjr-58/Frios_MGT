<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FranchiseStaffController\FranchiseStaffController;
use App\Http\Controllers\FranchiseStaffController\PosController;
use App\Http\Controllers\FranchiseStaffController\SaleController;
use App\Http\Controllers\Franchise\PaymentController;

Route::middleware(['auth'])->group(function () {
    Route::get('/staff/dashboard', [FranchiseStaffController::class, 'dashboard'])->middleware('permission:dashboard.view');

    Route::prefix('franchise_staff')->name('franchise_staff.')->group(function (){

        // Events routes
        Route::middleware('permission:events.view')->group(function () {
            Route::get('calendar' , [FranchiseStaffController::class , 'calendar'])->name('events.calendar');
            Route::get('report' , [FranchiseStaffController::class , 'report'])->name('events.report');
            Route::get('events/{id}/view' , [FranchiseStaffController::class , 'eventView'])->name('events.view');
        });

        // Flavors routes
        Route::middleware('permission:flavors.view')->group(function () {
            Route::get('flavors' , [FranchiseStaffController::class , 'flavors'])->name('flavors');
            Route::get('/flavors/detail', [FranchiseStaffController::class, 'flavorsDetail'])->name('flavors.detail');
        });

        // Customer routes
        Route::middleware('permission:customers.view')->group(function () {
            Route::get('customer' , [FranchiseStaffController::class , 'index'])->name('customer');
            Route::get('customer/{id}/view' , [FranchiseStaffController::class , 'view'])->name('customer.view');
        });
        
        Route::get('customer-create' , [FranchiseStaffController::class , 'create'])->name('customer.create')->middleware('permission:customers.create');
        Route::post('customer-store' , [FranchiseStaffController::class , 'store'])->name('customer.store')->middleware('permission:customers.create');
        Route::get('customer/{id}/edit' , [FranchiseStaffController::class , 'edit'])->name('customer.edit')->middleware('permission:customers.edit');
        Route::put('customer/{id}/update' , [FranchiseStaffController::class , 'update'])->name('customer.update')->middleware('permission:customers.edit');
        Route::delete('customer/{id}/delete' , [FranchiseStaffController::class , 'delete'])->name('customer.delete')->middleware('permission:customers.delete');

        // Sales routes
        Route::middleware('permission:sales.view')->group(function () {
            Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
            Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
            Route::get('pos/sales/{id}/download', [PaymentController::class, 'posInvoiceDownloadPDF'])->name('sales.pos.download');
        });
        
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create')->middleware('permission:sales.create');
        Route::post('sales', [SaleController::class, 'store'])->name('sales.store')->middleware('permission:sales.create');
        Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit')->middleware('permission:sales.edit');
        Route::put('sales/{sale}', [SaleController::class, 'update'])->name('sales.update')->middleware('permission:sales.edit');
        Route::delete('sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy')->middleware('permission:sales.delete');

        // POS routes
        Route::get('pos' , [PosController::class , 'pos'])->name('pos')->middleware('permission:pos.view');
    });

});
