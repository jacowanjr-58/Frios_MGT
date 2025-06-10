<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CorporateAdminControllers\CorporateAdminController;
use App\Http\Controllers\CorporateAdminControllers\OwnerController;
use App\Http\Controllers\FranchiseAdminControllers\StaffController;
use App\Http\Controllers\CorporateAdminControllers\FgpItemsController;
use App\Http\Controllers\CorporateAdminControllers\FranchiseController;
use App\Http\Controllers\CorporateAdminControllers\ExpensesCategoryController;
use App\Http\Controllers\FranchiseAdminControllers\OrderPopsController;
// Franchise Admin Controller
use App\Http\Controllers\CorporateAdminControllers\ViewOrdersController;
use App\Http\Controllers\CorporateAdminControllers\FgpCategoryController;


use App\Http\Controllers\CorporateAdminControllers\AdditionalChargesController;
use App\Http\Controllers\CorporateAdminControllers\PaymentController as CorpPaymentController;
use App\Http\Controllers\Franchise\EventController;

Route::middleware(['auth', 'role:corporate_admin'])->prefix('corporate_admin')->name('corporate_admin.')->group(function () {
    Route::get('/corporate/dashboard', [CorporateAdminController::class, 'dashboard']);

     // Franchise routes
     Route::get('/franchisee', [FranchiseController::class, 'index'])->name('franchise.index');
     Route::get('/franchisee/create', [FranchiseController::class, 'create'])->name('franchise.create');
     Route::post('/franchisee', [FranchiseController::class, 'store'])->name('franchise.store');
     Route::get('/franchisee/{franchise}', [FranchiseController::class, 'show'])->name('franchise.show');
     Route::get('/franchisee/{franchise}/edit', [FranchiseController::class, 'edit'])->name('franchise.edit');
     Route::put('/franchisee/{franchise}', [FranchiseController::class, 'update'])->name('franchise.update');
     Route::delete('/franchisee/{franchise}', [FranchiseController::class, 'destroy'])->name('franchise.destroy');

    // Owner routes
    Route::get('/owner', [OwnerController::class, 'index'])->name('owner.index');
    Route::get('/owner/create', [OwnerController::class, 'create'])->name('owner.create');
    Route::post('/owner', [OwnerController::class, 'store'])->name('owner.store');
    Route::get('/owner/{owner}', [OwnerController::class, 'edit'])->name('owner.show');
    Route::get('/owner/{owner}/edit', [OwnerController::class, 'edit'])->name('owner.edit');
    Route::put('/owner/{owner}', [OwnerController::class, 'update'])->name('owner.update');
    Route::delete('/owner/{owner}', [OwnerController::class, 'destroy'])->name('owner.destroy');

    // fgp Category routes
    Route::get('/fgpcategory', [FgpCategoryController::class, 'index'])->name('fgpcategory.index');
    Route::get('/fgpcategory/create', [FgpCategoryController::class, 'create'])->name('fgpcategory.create');
    Route::post('/fgpcategory', [FgpCategoryController::class, 'store'])->name('fgpcategory.store');
    Route::get('/fgpcategory/{fgpcategory}/edit', [FgpCategoryController::class, 'edit'])->name('fgpcategory.edit');
    Route::put('/fgpcategory/{fgpcategory}', [FgpCategoryController::class, 'update'])->name('fgpcategory.update');
    Route::delete('/fgpcategory/{fgpcategory}', [FgpCategoryController::class, 'destroy'])->name('fgpcategory.destroy');

    // fgp items routes
    Route::get('/fgpitem', [FgpItemsController::class, 'index'])->name('fgpitem.index');
    Route::get('/fgpitem/create', [FgpItemsController::class, 'create'])->name('fgpitem.create');
    Route::post('/fgpitem', [FgpItemsController::class, 'store'])->name('fgpitem.store');
    Route::get('/fgpitem/{fgpitem}/edit', [FgpItemsController::class, 'edit'])->name('fgpitem.edit');
    Route::put('/fgpitem/{fgpitem}', [FgpItemsController::class, 'update'])->name('fgpitem.update');
    Route::delete('/fgpitem/{fgpitem}', [FgpItemsController::class, 'destroy'])->name('fgpitem.destroy');
    Route::post('/fgpitem/update-orderable', [FgpItemsController::class, 'updateOrderable'])->name('fgpitem.updateOrderable');
    Route::get('/fgpitemavailability', [FgpItemsController::class, 'availability'])->name('fgpitem.availability');
    Route::post('/fgpitem/update-status/{id}', [FgpItemsController::class, 'updateStatus'])->name('fgpitem.updateStatus');
    Route::post('/fgpitem/update-month/{id}', [FgpItemsController::class, 'updateMonth']);

    // Additional charges routes
    Route::put('/additional-charges/status', [AdditionalChargesController::class, 'changeStatus']);

    Route::get('/additionalcharges', [AdditionalChargesController::class, 'index'])->name('additionalcharges.index');
    Route::get('/additionalcharges/create', [AdditionalChargesController::class, 'create'])->name('additionalcharges.create');
    Route::post('/additionalcharges', [AdditionalChargesController::class, 'store'])->name('additionalcharges.store');
    Route::get('/additionalcharges/{additionalcharges}/edit', [AdditionalChargesController::class, 'edit'])->name('additionalcharges.edit');
    Route::put('/additionalcharges/{additionalcharges}', [AdditionalChargesController::class, 'update'])->name('additionalcharges.update');
    Route::delete('/additionalcharges/{additionalcharges}', [AdditionalChargesController::class, 'destroy'])->name('additionalcharges.destroy');

    // View Orders routes
    Route::get('/vieworders', [ViewOrdersController::class, 'index'])->name('vieworders.index');
    Route::get('/vieworders/create', [ViewOrdersController::class, 'create'])->name('vieworders.create');
    Route::post('/vieworders', [ViewOrdersController::class, 'store'])->name('vieworders.store');
    Route::get('/vieworders/detail', [ViewOrdersController::class, 'viewordersDetail'])->name('vieworders.detail');
    Route::get('/vieworders/{orderId}/edit', [ViewOrdersController::class, 'edit'])->name('vieworders.edit');
    Route::put('/vieworders/{vieworders}', [ViewOrdersController::class, 'update'])->name('vieworders.update');
    Route::delete('/vieworders/{vieworders}', [ViewOrdersController::class, 'destroy'])->name('vieworders.destroy');
    Route::get('/vieworders', [ViewOrdersController::class, 'index'])->name('vieworders.index');
    Route::post('/vieworders/update-status', [ViewOrdersController::class, 'updateStatus'])->name('vieworders.updateStatus');
    Route::get('/vieworders/customersorder/{customer_id}', [ViewOrdersController::class, 'showCustomer'])->name('customersorder.show');


    Route::get('orderpops' , [ViewOrdersController::class , 'orderposps'])->name('orderposps');
    Route::get('orderpops/confirm/page' , [ViewOrdersController::class , 'confirmPage'])->name('confirm.page');
    Route::post('/orderpops/confirm', [ViewOrdersController::class, 'confirmOrder'])->name('orderpops.confirm');
    Route::post('/orderpops/store', [ViewOrdersController::class, 'store'])->name('orderpops.store');
    Route::get('get-customer/{customer_id}', [OrderPopsController::class, 'customer'])->name('orderpops.customer');

    // Event
    Route::get('/events/calender', [EventController::class, 'eventCalenderAdmin'])->name('events.calender');
    Route::get('/events/report', [EventController::class, 'eventReportAdmin'])->name('events.report');
    Route::get('/events/{id}/view', [EventController::class, 'viewAdmin'])->name('events.view');


    // Expense Category
    Route::get('expense-category' , [ExpensesCategoryController::class , 'index'])->name('expense-category');
    Route::get('expense-category/create' , [ExpensesCategoryController::class , 'create'])->name('expense-category.create');
    Route::get('expense-category/{id}/edit' , [ExpensesCategoryController::class , 'edit'])->name('expense-category.edit');
    Route::put('expense-category/{id}/update' , [ExpensesCategoryController::class , 'update'])->name('expense-category.update');
    Route::post('expense-category/store' , [ExpensesCategoryController::class , 'store'])->name('expense-category.store');
    Route::post('expense-sub-category/store' , [ExpensesCategoryController::class , 'Substore'])->name('expense-sub-category.store');
    Route::delete('expense-sub-category/{id}/delete' , [ExpensesCategoryController::class , 'delete'])->name('expense-sub-category.delete');

    Route::get('expense' , [ExpensesCategoryController::class , 'expense'])->name('expense.franchisee');

    // Customer
    Route::get('customer' , [ExpensesCategoryController::class , 'customer'])->name('customer');
    Route::get('customer/{id}/view' , [ExpensesCategoryController::class , 'customerView'])->name('customer.view');

    // Payment
    Route::get('transactions' , [CorpPaymentController::class , 'transaction'])->name('transaction');
    Route::get('pos/{id}/expense' , [CorpPaymentController::class , 'posExpense'])->name('pos.expense');
    Route::get('pos/expenses/{id}/download', [CorpPaymentController::class, 'posDownloadPDF'])->name('expenses.pos.download');
    Route::get('pos/{id}/order' , [CorpPaymentController::class , 'posOrder'])->name('pos.order');
    Route::get('pos/order/{id}/download', [CorpPaymentController::class, 'posOrderDownloadPDF'])->name('order.pos.download');
    Route::get('pos/{id}/event' , [CorpPaymentController::class , 'posEvent'])->name('pos.event');
    Route::get('pos/event/{id}/download', [CorpPaymentController::class, 'posEventDownloadPDF'])->name('event.pos.download');
});
