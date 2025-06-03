<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FranchiseStaffController\FranchiseStaffController;
use App\Http\Controllers\FranchiseStaffController\PosController;
use App\Http\Controllers\FranchiseStaffController\SaleController;
use App\Http\Controllers\Franchise\PaymentController;

Route::middleware(['auth', 'role:franchise_staff'])->group(function () {
    Route::get('/staff/dashboard', [FranchiseStaffController::class, 'dashboard']);

    Route::prefix('franchise_staff')->name('franchise_staff.')->group(function (){

        Route::get('calendar' , [FranchiseStaffController::class , 'calendar'])->name('events.calendar');
        Route::get('report' , [FranchiseStaffController::class , 'report'])->name('events.report');
        Route::get('events/{id}/view' , [FranchiseStaffController::class , 'eventView'])->name('events.view');
        Route::get('flavors' , [FranchiseStaffController::class , 'flavors'])->name('flavors');
        Route::get('/flavors/detail', [FranchiseStaffController::class, 'flavorsDetail'])->name('flavors.detail');


        Route::get('customer' , [FranchiseStaffController::class , 'index'])->name('customer');
        Route::get('customer-create' , [FranchiseStaffController::class , 'create'])->name('customer.create');
        Route::post('customer-store' , [FranchiseStaffController::class , 'store'])->name('customer.store');
        Route::get('customer/{id}/edit' , [FranchiseStaffController::class , 'edit'])->name('customer.edit');
        Route::get('customer/{id}/view' , [FranchiseStaffController::class , 'view'])->name('customer.view');
        Route::put('customer/{id}/update' , [FranchiseStaffController::class , 'update'])->name('customer.update');
        Route::delete('customer/{id}/delete' , [FranchiseStaffController::class , 'delete'])->name('customer.delete');

        // Sale
        Route::resource('sales', SaleController::class);
        Route::get('pos/sales/{id}/download', [PaymentController::class, 'posInvoiceDownloadPDF'])->name('sales.pos.download');


        // Transaction
        Route::get('pos' , [PosController::class , 'pos'])->name('pos');
    });

});
