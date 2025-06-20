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
use App\Http\Controllers\CorporateAdminControllers\RolePermissionController;
use App\Http\Controllers\CorporateAdminControllers\UserManagementController;
use App\Http\Controllers\Franchise\EventController;

Route::middleware(['auth'])->group(function () {
    Route::get('/corporate/dashboard', [CorporateAdminController::class, 'dashboard'])->middleware('permission:dashboard.view');

     // Franchise routes
     Route::middleware('permission:franchises.view|franchises.create|franchises.edit|franchises.delete')->group(function () {
         Route::get('/franchisee', [FranchiseController::class, 'index'])->name('franchise.index');

         Route::get('/franchisee/create', [FranchiseController::class, 'create'])->name('franchise.create')->middleware('permission:franchises.create');
         Route::post('/franchisee', [FranchiseController::class, 'store'])->name('franchise.store')->middleware('permission:franchises.create');
         Route::get('/franchisee/{franchise}/edit', [FranchiseController::class, 'edit'])->name('franchise.edit')->middleware('permission:franchises.edit');
         Route::put('/franchisee/{franchise}', [FranchiseController::class, 'update'])->name('franchise.update')->middleware('permission:franchises.edit');
         Route::delete('/franchisee/{franchise}', [FranchiseController::class, 'destroy'])->name('franchise.destroy')->middleware('permission:franchises.delete');
    
         Route::get('/franchisee/{franchise}', [FranchiseController::class, 'show'])->name('franchise.show');
     });
     
   
    // Owner routes
    Route::middleware('permission:owners.view')->group(function () {
        Route::get('/owner', [OwnerController::class, 'index'])->name('owner.index');

        Route::get('/owner/create', [OwnerController::class, 'create'])->name('owner.create')->middleware('permission:owners.create');
        Route::post('/owner', [OwnerController::class, 'store'])->name('owner.store')->middleware('permission:owners.create');
        Route::get('/owner/{owner}/edit', [OwnerController::class, 'edit'])->name('owner.edit')->middleware('permission:owners.edit');
        Route::put('/owner/{owner}', [OwnerController::class, 'update'])->name('owner.update')->middleware('permission:owners.edit');
        Route::delete('/owner/{owner}', [OwnerController::class, 'destroy'])->name('owner.destroy')->middleware('permission:owners.delete');
    });
    
  
    // fgp Category routes
    Route::prefix('franchise/{franchisee}')->name('franchise.')->group(function () {
        Route::middleware('permission:frios_flavors.categories')->group(function () {
            Route::get('/fgpcategory', [FgpCategoryController::class, 'index'])->name('fgpcategory.index');
            Route::get('/fgpcategory/create', [FgpCategoryController::class, 'create'])->name('fgpcategory.create');
            Route::post('/fgpcategory', [FgpCategoryController::class, 'store'])->name('fgpcategory.store');
            Route::get('/fgpcategory/{fgpcategory}/edit', [FgpCategoryController::class, 'edit'])->name('fgpcategory.edit');
            Route::put('/fgpcategory/{fgpcategory}', [FgpCategoryController::class, 'update'])->name('fgpcategory.update');
            Route::delete('/fgpcategory/{fgpcategory}', [FgpCategoryController::class, 'destroy'])->name('fgpcategory.destroy');
        });

        // fgp items routes (Frios Flavors)
        Route::middleware('permission:frios_flavors.view')->group(function () {
            Route::get('/fgpitem', [FgpItemsController::class, 'index'])->name('fgpitem.index');
        });
        
        Route::middleware('permission:frios_availability.view')->group(function () {
            Route::get('/fgpitemavailability', [FgpItemsController::class, 'availability'])->name('fgpitem.availability');
            Route::post('/fgpitem/update-status/{id}', [FgpItemsController::class, 'updateStatus'])->name('fgpitem.updateStatus')->middleware('permission:frios_availability.update');
            Route::post('/fgpitem/update-month/{id}', [FgpItemsController::class, 'updateMonth'])->name('fgpitem.updateMonth')->middleware('permission:frios_availability.update');
        });
        
        Route::get('/fgpitem/create', [FgpItemsController::class, 'create'])->name('fgpitem.create')->middleware('permission:frios_flavors.create');
        Route::post('/fgpitem', [FgpItemsController::class, 'store'])->name('fgpitem.store')->middleware('permission:frios_flavors.create');
        Route::get('/fgpitem/{fgpitem}/edit', [FgpItemsController::class, 'edit'])->name('fgpitem.edit')->middleware('permission:frios_flavors.edit');
        Route::put('/fgpitem/{fgpitem}', [FgpItemsController::class, 'update'])->name('fgpitem.update')->middleware('permission:frios_flavors.edit');
        Route::delete('/fgpitem/{fgpitem}', [FgpItemsController::class, 'destroy'])->name('fgpitem.destroy')->middleware('permission:frios_flavors.delete');
        Route::post('/fgpitem/update-orderable', [FgpItemsController::class, 'updateOrderable'])->name('fgpitem.updateOrderable');
        Route::post('/fgpitem/update-status/{id}', [FgpItemsController::class, 'updateStatus'])->name('fgpitem.updateStatus');
        Route::post('/fgpitem/update-month/{id}', [FgpItemsController::class, 'updateMonth']);
    });

    // Additional charges routes
    Route::middleware('permission:additional_charges.view')->group(function () {
        Route::get('/additionalcharges', [AdditionalChargesController::class, 'index'])->name('additionalcharges.index');
        
        Route::get('/additionalcharges/create', [AdditionalChargesController::class, 'create'])->name('additionalcharges.create')->middleware('permission:additional_charges.create');
        Route::post('/additionalcharges', [AdditionalChargesController::class, 'store'])->name('additionalcharges.store')->middleware('permission:additional_charges.create');
        Route::get('/additionalcharges/{additionalcharges}/edit', [AdditionalChargesController::class, 'edit'])->name('additionalcharges.edit')->middleware('permission:additional_charges.edit');
        Route::put('/additionalcharges/{additionalcharges}', [AdditionalChargesController::class, 'update'])->name('additionalcharges.update')->middleware('permission:additional_charges.edit');
        Route::delete('/additionalcharges/{additionalcharges}', [AdditionalChargesController::class, 'destroy'])->name('additionalcharges.destroy')->middleware('permission:additional_charges.delete');
        Route::put('/additional-charges/status', [AdditionalChargesController::class, 'changeStatus'])->middleware('permission:additional_charges.edit');
    });

    // View Orders routes
    Route::middleware('permission:franchise_orders.view')->group(function () {
        Route::get('/vieworders', [ViewOrdersController::class, 'index'])->name('vieworders.index');
        Route::get('/vieworders/detail', [ViewOrdersController::class, 'viewordersDetail'])->name('vieworders.detail');
        Route::get('/vieworders/customersorder/{customer_id}', [ViewOrdersController::class, 'showCustomer'])->name('customersorder.show');
        Route::get('orderpops' , [ViewOrdersController::class , 'orderposps'])->name('orderposps');
        Route::get('orderpops/confirm/page' , [ViewOrdersController::class , 'confirmPage'])->name('confirm.page');
        Route::get('get-customer/{customer_id}', [OrderPopsController::class, 'customer'])->name('orderpops.customer');
    });
    
    Route::get('/vieworders/create', [ViewOrdersController::class, 'create'])->name('vieworders.create')->middleware('permission:franchise_orders.create');
    Route::post('/vieworders', [ViewOrdersController::class, 'store'])->name('vieworders.store')->middleware('permission:franchise_orders.create');
    Route::post('/vieworders/update-status', [ViewOrdersController::class, 'updateStatus'])->name('vieworders.updateStatus')->middleware('permission:franchise_orders.edit');
    Route::post('/orderpops/confirm', [ViewOrdersController::class, 'confirmOrder'])->name('orderpops.confirm')->middleware('permission:franchise_orders.create');
    Route::post('/orderpops/store', [ViewOrdersController::class, 'store'])->name('orderpops.store')->middleware('permission:franchise_orders.create');
    Route::get('/vieworders/{orderId}/edit', [ViewOrdersController::class, 'edit'])->name('vieworders.edit')->middleware('permission:franchise_orders.edit');
    Route::put('/vieworders/{vieworders}', [ViewOrdersController::class, 'update'])->name('vieworders.update')->middleware('permission:franchise_orders.edit');
    Route::delete('/vieworders/{vieworders}', [ViewOrdersController::class, 'destroy'])->name('vieworders.destroy')->middleware('permission:franchise_orders.delete');

    // Event
    Route::middleware('permission:events.view')->group(function () {
        Route::get('/events/calender', [EventController::class, 'eventCalenderAdmin'])->name('events.calender');
        Route::get('/events/report', [EventController::class, 'eventReportAdmin'])->name('events.report');
        Route::get('/events/{id}/view', [EventController::class, 'viewAdmin'])->name('events.view');
    });

    // Expense Category
    Route::middleware('permission:expenses.categories')->group(function () {
        Route::get('expense-category' , [ExpensesCategoryController::class , 'index'])->name('expense-category');
        Route::get('expense-category/create' , [ExpensesCategoryController::class , 'create'])->name('expense-category.create');
        Route::post('expense-category/store' , [ExpensesCategoryController::class , 'store'])->name('expense-category.store');
        Route::post('expense-sub-category/store' , [ExpensesCategoryController::class , 'Substore'])->name('expense-sub-category.store');
        Route::get('expense-category/{id}/edit' , [ExpensesCategoryController::class , 'edit'])->name('expense-category.edit');
        Route::put('expense-category/{id}/update' , [ExpensesCategoryController::class , 'update'])->name('expense-category.update');
        Route::delete('expense-sub-category/{id}/delete' , [ExpensesCategoryController::class , 'delete'])->name('expense-sub-category.delete');
    });

    Route::get('expense' , [ExpensesCategoryController::class , 'expense'])->name('expense.franchisee')->middleware('permission:expenses.by_franchisee');

    // Customer
    Route::middleware('permission:customers.by_franchisee')->prefix('franchise')->name('franchise.')->group(function () {
        Route::get('{franchisee}/franchise_customer' , [ExpensesCategoryController::class , 'customer'])->name('franchise_customer');
        Route::get('{franchisee}/franchise_customer/{id}/view' , [ExpensesCategoryController::class , 'customerView'])->name('franchise_customer.view');
    });

    // Payment
    Route::middleware('permission:payments.view')->group(function () {
        Route::get('franchise/{franchisee}/transactions' , [CorpPaymentController::class , 'transaction'])->name('transaction');
        Route::get('pos/{id}/expense' , [CorpPaymentController::class , 'posExpense'])->name('pos.expense');
        Route::get('pos/expenses/{id}/download', [CorpPaymentController::class, 'posDownloadPDF'])->name('expenses.pos.download');
        Route::get('pos/{id}/order' , [CorpPaymentController::class , 'posOrder'])->name('pos.order');
        Route::get('pos/order/{id}/download', [CorpPaymentController::class, 'posOrderDownloadPDF'])->name('order.pos.download');
        Route::get('pos/{id}/event' , [CorpPaymentController::class , 'posEvent'])->name('pos.event');
        Route::get('pos/event/{id}/download', [CorpPaymentController::class, 'posEventDownloadPDF'])->name('event.pos.download');
    });

    // Roles & Permissions Management (Restricted to corporate_admin only)
    Route::prefix('roles')->name('roles.')->middleware('permission:roles.view|roles.create|roles.edit|roles.delete|permissions.view')->group(function () {
        Route::get('/', [RolePermissionController::class, 'index'])->name('index');
        Route::get('/create', [RolePermissionController::class, 'create'])->name('create')->middleware('permission:roles.create');
        Route::post('/', [RolePermissionController::class, 'store'])->name('store')->middleware('permission:roles.create');
        Route::get('/{role}', [RolePermissionController::class, 'show'])->name('show')->middleware('permission:roles.view');
        Route::get('/{role}/edit', [RolePermissionController::class, 'edit'])->name('edit')->middleware('permission:roles.edit');
        Route::put('/{role}', [RolePermissionController::class, 'update'])->name('update')->middleware('permission:roles.edit');
        Route::delete('/{role}', [RolePermissionController::class, 'destroy'])->name('destroy')->middleware('permission:roles.delete');
        Route::get('/{role}/permissions', [RolePermissionController::class, 'getPermissions'])->name('permissions')->middleware('permission:permissions.view');
    });

    // User Management (Restricted to corporate_admin only)
    Route::prefix('users')->name('users.')->middleware('permission:users.view|users.create|users.edit|users.delete')->group(function () {
        Route::get('/', [UserManagementController::class, 'index'])->name('index');
        Route::get('/create', [UserManagementController::class, 'create'])->name('create')->middleware('permission:users.create');
        Route::post('/', [UserManagementController::class, 'store'])->name('store')->middleware('permission:users.create');
        Route::get('/{user}', [UserManagementController::class, 'show'])->name('show')->middleware('permission:users.view');
        Route::get('/{user}/edit', [UserManagementController::class, 'edit'])->name('edit')->middleware('permission:users.edit');
        Route::put('/{user}', [UserManagementController::class, 'update'])->name('update')->middleware('permission:users.edit');
        Route::delete('/{user}', [UserManagementController::class, 'destroy'])->name('destroy')->middleware('permission:users.delete');
    });
});
