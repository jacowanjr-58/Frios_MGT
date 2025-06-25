<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FranchiseStaffController\FranchiseStaffController;
use App\Http\Controllers\FranchiseStaffController\PosController;
use App\Http\Controllers\FranchiseStaffController\SaleController;
use App\Http\Controllers\Franchise\PaymentController;

Route::middleware(['auth'])->group(function () {
    Route::get('/staff/dashboard', [FranchiseStaffController::class, 'dashboard'])->middleware('permission:dashboard.view');

    Route::prefix('franchise/{franchise}')->name('franchise_staff.')->group(function (){

        // Events routes
        Route::middleware('permission:events.view')->group(function () {
            Route::get('calendar' , [FranchiseStaffController::class , 'calendar'])->name('events.calendar')->middleware('permission:events.view');
            Route::get('report' , [FranchiseStaffController::class , 'report'])->name('events.report')->middleware('permission:events.view');
            Route::get('events/{id}/view' , [FranchiseStaffController::class , 'eventView'])->name('events.view')->middleware('permission:events.view');
        });

        // Customer routes
        Route::middleware('permission:customers.view')->group(function () {
            Route::get('customer' , [FranchiseStaffController::class , 'index'])->name('customer')->middleware('permission:customers.view');
            Route::get('customer/{id}/view' , [FranchiseStaffController::class , 'view'])->name('customer.view')->middleware('permission:customers.view');
        });
        
        Route::get('customer-create' , [FranchiseStaffController::class , 'create'])->name('customer.create')->middleware('permission:customers.create');
        Route::post('customer-store' , [FranchiseStaffController::class , 'store'])->name('customer.store')->middleware('permission:customers.create');
        Route::get('customer/{id}/edit' , [FranchiseStaffController::class , 'edit'])->name('customer.edit')->middleware('permission:customers.edit');
        Route::put('customer/{id}/update' , [FranchiseStaffController::class , 'update'])->name('customer.update')->middleware('permission:customers.edit');
        Route::delete('customer/{id}/delete' , [FranchiseStaffController::class , 'delete'])->name('customer.delete')->middleware('permission:customers.delete');

        // POS routes
        Route::middleware('permission:pos.view')->group(function () {
            Route::get('pos' , [PosController::class , 'index'])->name('pos')->middleware('permission:pos.view');
        });

        // Sales routes
        Route::middleware('permission:sales.view')->group(function () {
            Route::get('sales', [SaleController::class, 'index'])->name('sales.index')->middleware('permission:sales.view');
            Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show')->middleware('permission:sales.view');
            Route::get('pos/sales/{id}/download', [PaymentController::class, 'posInvoiceDownloadPDF'])->name('sales.pos.download')->middleware('permission:sales.view');
        });
        
        Route::get('sales/create', [SaleController::class, 'create'])->name('sales.create')->middleware('permission:sales.create');
        Route::post('sales', [SaleController::class, 'store'])->name('sales.store')->middleware('permission:sales.create');
        Route::get('sales/{sale}/edit', [SaleController::class, 'edit'])->name('sales.edit')->middleware('permission:sales.edit');
        Route::put('sales/{sale}', [SaleController::class, 'update'])->name('sales.update')->middleware('permission:sales.edit');
        Route::delete('sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy')->middleware('permission:sales.delete');
    });

});
