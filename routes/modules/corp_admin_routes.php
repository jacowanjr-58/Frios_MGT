<?php

use App\Http\Controllers\Franchise\CustomerController;
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
use App\Http\Controllers\Franchise\ExpenseController;

Route::middleware(['auth'])->group(function () {
    Route::get('/corporate/dashboard', [CorporateAdminController::class, 'dashboard'])->middleware('permission:dashboard.view');

    // Franchise routes
    Route::middleware('permission:franchises.view|franchises.create|franchises.edit|franchises.delete')->group(function () {
        Route::get('/franchise', [FranchiseController::class, 'index'])->name('franchise.index');
        Route::get('/franchise/filter-options', [FranchiseController::class, 'getFilterOptions'])->name('franchise.filter-options');

        Route::get('/franchise/create', [FranchiseController::class, 'create'])->name('franchise.create')->middleware('permission:franchises.create');
        Route::post('/franchise', [FranchiseController::class, 'store'])->name('franchise.store')->middleware('permission:franchises.create');
        Route::get('/franchise/{franchise}/edit', [FranchiseController::class, 'edit'])->name('franchise.edit')->middleware('permission:franchises.edit');
        Route::put('/franchise/{franchise}', [FranchiseController::class, 'update'])->name('franchise.update')->middleware('permission:franchises.edit');
        Route::delete('/franchise/{franchise}', [FranchiseController::class, 'destroy'])->name('franchise.destroy')->middleware('permission:franchises.delete');

        Route::get('/franchise/{franchise}', [FranchiseController::class, 'show'])->name('franchise.show');
    });


    // Owner routes
    Route::prefix('franchise/{franchise}')->middleware('permission:owners.view')->group(function () {
        Route::get('/owner', [OwnerController::class, 'index'])->name('owner.index');
        Route::get('/owner/create', [OwnerController::class, 'create'])->name('owner.create')->middleware('permission:owners.create');
        Route::post('/owner', [OwnerController::class, 'store'])->name('owner.store')->middleware('permission:owners.create');
        Route::get('/owner/{owner}/edit', [OwnerController::class, 'edit'])->name('owner.edit')->middleware('permission:owners.edit');
        Route::put('/owner/{owner}', [OwnerController::class, 'update'])->name('owner.update')->middleware('permission:owners.edit');
        Route::delete('/owner/{owner}', [OwnerController::class, 'destroy'])->name('owner.destroy')->middleware('permission:owners.delete');
    });

    Route::get('/fgpcategory', [FgpCategoryController::class, 'index'])->name('fgpcategory.index')->middleware('permission:flavor_category.view');
    Route::get('/fgpcategory/create', [FgpCategoryController::class, 'create'])->name('fgpcategory.create')->middleware('permission:flavor_category.create');
    Route::post('/fgpcategory', [FgpCategoryController::class, 'store'])->name('fgpcategory.store')->middleware('permission:flavor_category.create');
    Route::get('/fgpcategory/{fgpcategory}/edit', [FgpCategoryController::class, 'edit'])->name('fgpcategory.edit')->middleware('permission:flavor_category.edit');
    Route::put('/fgpcategory/{fgpcategory}', [FgpCategoryController::class, 'update'])->name('fgpcategory.update')->middleware('permission:flavor_category.edit');
    Route::delete('/fgpcategory/{fgpcategory}', [FgpCategoryController::class, 'destroy'])->name('fgpcategory.destroy')->middleware('permission:flavor_category.delete');
    

    // fgp items routes (Frios Flavors)
    Route::middleware('permission:frios_flavors.view')->group(function () {
        Route::get('/fgpitem', [FgpItemsController::class, 'index'])->name('fgpitem.index');
        Route::get('/fgpitem/create', [FgpItemsController::class, 'create'])->name('fgpitem.create');
        Route::post('/fgpitem', [FgpItemsController::class, 'store'])->name('fgpitem.store');
        Route::get('/fgpitem/{fgpitem}/edit', [FgpItemsController::class, 'edit'])->name('fgpitem.edit');
        Route::put('/fgpitem/{fgpitem}', [FgpItemsController::class, 'update'])->name('fgpitem.update');
        Route::delete('/fgpitem/{fgpitem}', [FgpItemsController::class, 'destroy'])->name('fgpitem.destroy');
    });

    // fgp Category routes
    // Route::prefix('franchise/{franchise}')->name('franchise.')->group(function () {
        Route::middleware('permission:frios_availability.view')->group(function () {
            Route::get('/fgpitemavailability', [FgpItemsController::class, 'availability'])->name('fgpitem.availability');
            Route::post('/fgpitem/update-status/{id}', [FgpItemsController::class, 'updateStatus'])->name('fgpitem.updateStatus');
            Route::post('/fgpitem/update-month/{id}', [FgpItemsController::class, 'updateMonth'])->name('fgpitem.updateMonth');
        });

       Route::post('/fgpitem/update-orderable', [FgpItemsController::class, 'updateOrderable'])->name('fgpitem.updateOrderable');
    // });

    // Additional charges routes
    Route::middleware('permission:additional_charges.view')->group(function () {
        Route::get('/additional-charges', [AdditionalChargesController::class, 'index'])->name('additional-charges.index');

        Route::get('/additional-charges/create', [AdditionalChargesController::class, 'create'])->name('additional-charges.create')->middleware('permission:additional_charges.create');
        Route::post('/additional-charges', [AdditionalChargesController::class, 'store'])->name('additional-charges.store')->middleware('permission:additional_charges.create');
        Route::get('/additional-charges/{additionalcharges}/edit', [AdditionalChargesController::class, 'edit'])->name('additional-charges.edit')->middleware('permission:additional_charges.edit');
        Route::put('/additional-charges/{additionalcharges}', [AdditionalChargesController::class, 'update'])->name('additional-charges.update')->middleware('permission:additional_charges.edit');
        Route::delete('/additional-charges/{additionalcharges}', [AdditionalChargesController::class, 'destroy'])->name('additional-charges.destroy')->middleware('permission:additional_charges.delete');
        Route::put('/additional-charges/status', [AdditionalChargesController::class, 'changeStatus'])->name('additional-charges.status')->middleware('permission:additional_charges.edit');
    });

    // View Orders routes
    Route::middleware('permission:orders.view')->group(function () {
        Route::get('/franchises/{franchise}/orders', [ViewOrdersController::class, 'index'])->name('franchise.orders');
        Route::get('/franchise/{franchise}/orderpops/edit', [OrderPopsController::class, 'edit'])->name('orderpops.edit');
        Route::get('/franchise/{franchise}/orders/detail', [ViewOrdersController::class, 'ordersDetail'])->name('franchise.orders.detail');

        Route::get('/orders/customersorder/{customer_id}', [ViewOrdersController::class, 'showCustomer'])->name('customersorder.show');
        
        
        // Route::get('orderpops', [ViewOrdersController::class, 'orderpops'])->name('orderpops');
        // Route::get('orderpops/confirm/page', [ViewOrdersController::class, 'confirmPage'])->name('confirm.page');
        // Route::get('get-customer/{customer_id}', [OrderPopsController::class, 'customer'])->name('orderpops.customer');
    });
    Route::prefix('franchises/{franchise}')->name('franchise.')->group(function () {
        Route::get('/orders/create', [ViewOrdersController::class, 'create'])->name('orders.create')->middleware('permission:orders.create');
        Route::post('/orders', [ViewOrdersController::class, 'store'])->name('orders.store')->middleware('permission:orders.create');
        Route::post('/orders/update-status', [ViewOrdersController::class, 'updateStatus'])->name('orders.updateStatus')->middleware('permission:orders.edit');
        // Route::post('/orderpops/confirm', [ViewOrdersController::class, 'confirmOrder'])->name('orderpops.confirm')->middleware('permission:orders.create');
        // Route::post('/orderpops/store', [ViewOrdersController::class, 'store'])->name('orderpops.store')->middleware('permission:orders.create');
        Route::get('/orders/{orders}/edit', [ViewOrdersController::class, 'edit'])->name('orders.edit')->middleware('permission:orders.edit');
        Route::put('/orders/{orders}', [ViewOrdersController::class, 'update'])->name('orders.update')->middleware('permission:orders.edit');
        Route::delete('/orders/{orders}', [ViewOrdersController::class, 'destroy'])->name('orders.destroy')->middleware('permission:orders.delete');
        Route::get('/orders/flavors', [ViewOrdersController::class, 'getFlavors'])->name('orders.flavors')->middleware('permission:orders.view');
    });
    // Event
    Route::middleware('permission:events.view')->group(function () {
        Route::get('/events/calender', [EventController::class, 'eventCalenderAdmin'])->name('events.calender');
        Route::get('/events/report', [EventController::class, 'eventReportAdmin'])->name('events.report');
        Route::get('/events/{id}/view', [EventController::class, 'viewAdmin'])->name('events.view');
    });

    // Expense Category
    Route::get('expense-category', [ExpensesCategoryController::class, 'index'])->name('expense-category');
    Route::get('expense-category/create', [ExpensesCategoryController::class, 'create'])->name('expense-category.create');
    Route::post('expense-category/store', [ExpensesCategoryController::class, 'store'])->name('expense-category.store');
    Route::post('expense-sub-category/store', [ExpensesCategoryController::class, 'Substore'])->name('expense-sub-category.store');
    Route::get('expense-category/{id}/edit', [ExpensesCategoryController::class, 'edit'])->name('expense-category.edit');
    Route::put('expense-category/{id}/update', [ExpensesCategoryController::class, 'update'])->name('expense-category.update');
    Route::delete('expense-category/{id}/delete', [ExpensesCategoryController::class, 'delete'])->name('expense-category.delete');
    Route::get('/expense-categories/view', [ExpensesCategoryController::class, 'viewExpensesCategories'])
    ->name('expense-categories.view');
    // Route::get('/franchise/{franchise}/expense', [ExpenseController::class, 'index'])->name('expense.franchise')->middleware('permission:expenses.by_franchisee');

    // Customer
    Route::middleware('permission:customers.by_franchisee')->prefix('franchise')->name('franchise.')->group(function () {
        Route::get('{franchise}/franchise_customer', [ExpensesCategoryController::class, 'customer'])->name('franchise_customer');
        Route::get('{franchise}/franchise_customer/{id}/view', [ExpensesCategoryController::class, 'customerView'])->name('franchise_customer.view');
    });

    Route::prefix('{franchise}')->group(function () {
        // Customer view routes
        Route::middleware('permission:customers.view')->group(function () {
            Route::get('customer', [CustomerController::class, 'index'])->name('customer');
            Route::get('customer/{id}/view', [CustomerController::class, 'view'])->name('customer.view');
            Route::get('customer-create', [CustomerController::class, 'create'])->name('customer.create')->middleware('permission:customers.create');
            Route::post('customer-store', [CustomerController::class, 'store'])->name('customer.store')->middleware('permission:customers.create');
            Route::get('customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit')->middleware('permission:customers.edit');
            Route::put('customer/{id}/update', [CustomerController::class, 'update'])->name('customer.update')->middleware('permission:customers.edit');
            Route::delete('customer/{id}/delete', [CustomerController::class, 'delete'])->name('customer.delete')->middleware('permission:customers.delete');
        });
    });


    Route::prefix('franchise/{franchise}')->name('franchise.')->group(function () {
        Route::get('/expenses_by_franchise', [ExpenseController::class, 'index'])->name('expenses_by_franchise');

        Route::get('/expenses_by_franchise-create', [ExpenseController::class, 'create'])->name('expenses_by_franchise-create')->middleware('permission:expenses.create');

        Route::post('/expenses_by_franchise-store', [ExpenseController::class, 'store'])->name('expenses_by_franchise-store')->middleware('permission:expenses.create');

        Route::get('/expenses_by_franchise/{id}/edit', [ExpenseController::class, 'edit'])->name('expenses_by_franchise-edit')->middleware('permission:expenses.edit');

        Route::put('/expenses_by_franchise/{id}/update', [ExpenseController::class, 'update'])->name('expenses_by_franchise-update')->middleware('permission:expenses.edit');

        Route::delete('/expenses_by_franchise/{id}/delete', [ExpenseController::class, 'delete'])->name('expenses_by_franchise-delete')->middleware('permission:expenses.delete');

        Route::get('/get-subcategories/{category_id}', [ExpenseController::class, 'getSubCategories'])->name('getSubCategories');
    });

    // Payment
    Route::middleware('permission:payments.view')->group(function () {
        Route::get('franchise/{franchise}/transactions', [CorpPaymentController::class, 'transaction'])->name('transaction');
        Route::get('pos/{id}/expense', [CorpPaymentController::class, 'posExpense'])->name('pos.expense');
        Route::get('pos/expenses/{id}/download', [CorpPaymentController::class, 'posDownloadPDF'])->name('expenses.pos.download');
        Route::get('pos/{id}/order', [CorpPaymentController::class, 'posOrder'])->name('pos.order');
        Route::get('pos/order/{id}/download', [CorpPaymentController::class, 'posOrderDownloadPDF'])->name('order.pos.download');
        Route::get('pos/{id}/event', [CorpPaymentController::class, 'posEvent'])->name('pos.event');
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
        Route::get('/{role}/show', [RolePermissionController::class, 'show'])->name('show')->middleware('permission:roles.view');
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

    Route::get('/orders/{franchise}/flavors', [ViewOrdersController::class, 'getFlavors'])->name('franchise.orders.flavors');

    Route::get('/orders/{franchise}/shipping-addresses', [ViewOrdersController::class, 'getShippingAddresses'])->name('franchise.orders.shippingAddresses');
});
