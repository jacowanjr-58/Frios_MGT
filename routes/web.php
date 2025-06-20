<?php

use App\Http\Controllers\DashboardController;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;

use App\Http\Controllers\FranchiseAdminControllers\AdminProfileController;
use App\Http\Controllers\FranchiseAdminControllers\FranchiseAdminController;
use App\Http\Controllers\FranchiseAdminControllers\OrderPopsController;
use App\Http\Controllers\FranchiseAdminControllers\StaffController;

use App\Http\Controllers\FranchiseManagerControllers\FranchiseManagerController;

use App\Http\Controllers\CorporateAdminControllers\OwnerController;
use App\Http\Controllers\CorporateAdminControllers\FgpItemsController;
use App\Http\Controllers\CorporateAdminControllers\FranchiseController;
use App\Http\Controllers\CorporateAdminControllers\ExpensesCategoryController;


use App\Http\Controllers\Franchise\EventController;
use App\Http\Controllers\Franchise\StripeController;
use App\Http\Controllers\Franchise\ExpenseController;
use App\Http\Controllers\Franchise\CustomerController;
use App\Http\Controllers\Franchise\InventoryController;
use App\Http\Controllers\Franchise\PaymentController;
use App\Http\Controllers\Franchise\InventoryLocationController;
use App\Http\Controllers\Franchise\InvoiceController;
use App\Http\Controllers\Franchise\AccountController;
use App\Http\Controllers\FranchiseStaffController\FranchiseStaffController;
use App\Http\Middleware\StripeMiddleware;
use Illuminate\Support\Facades\DB;


Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(StripeMiddleware::class)->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard')->middleware('auth');
    Route::get('/load-more-events', [DashboardController::class, 'loadMoreEvents'])->name('loadMoreEvents')->middleware('auth');
});

Route::middleware('auth')->group(function () {
    // General profile routes for corporate admins
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/{profile}', [AdminProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/{profile}/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{profile}', [AdminProfileController::class, 'update'])->name('profile.update');
});


Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);



Route::middleware(['auth', StripeMiddleware::class])->prefix('franchise')->name('franchise.')->group(function () {
    Route::get('/dashboard', [FranchiseAdminController::class, 'dashboard'])->name('dashboard')->middleware('permission:dashboard.view');

    Route::prefix('{franchisee}')->group(function () {
        // Profile routes
        Route::middleware('permission:profiles.view')->group(function () {
            Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
            Route::get('/profile/{profile}', [AdminProfileController::class, 'show'])->name('profile.show');
        });

        Route::get('/profile/create', [AdminProfileController::class, 'create'])->name('profile.create')->middleware('permission:profiles.create');
        Route::post('/profile', [AdminProfileController::class, 'store'])->name('profile.store')->middleware('permission:profiles.create');
        Route::get('/profile/{profile}/edit', [AdminProfileController::class, 'edit'])->name('profile.edit')->middleware('permission:profiles.edit');
        Route::put('/profile/{profile}', [AdminProfileController::class, 'update'])->name('profile.update')->middleware('permission:profiles.edit');
        Route::delete('/profile/{profile}', [AdminProfileController::class, 'destroy'])->name('profile.destroy')->middleware('permission:profiles.delete');

        // Staff routes
        Route::middleware('permission:staff.view')->group(function () {
            Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
        });

        Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create')->middleware('permission:staff.create');
        Route::post('/staff', [StaffController::class, 'store'])->name('staff.store')->middleware('permission:staff.create');
        Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit')->middleware('permission:staff.edit');
        Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update')->middleware('permission:staff.edit');
        Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy')->middleware('permission:staff.delete');
    });


    Route::prefix('{franchisee}')->group(function () {
        // Order pops routes
        Route::middleware('permission:orders.view')->group(function () {
            Route::get('/orderpops', [OrderPopsController::class, 'index'])->name('orderpops.index');
            Route::get('/orderpops/view', [OrderPopsController::class, 'viewOrders'])->name('orderpops.view');
            Route::get('/orderpops/confirm/page', [OrderPopsController::class, 'showConfirmPage'])->name('orderpops.confirm.page');
        });

        Route::get('/orderpops/create', [OrderPopsController::class, 'create'])->name('orderpops.create')->middleware('permission:orders.create');
        Route::post('/orderpops/store', [OrderPopsController::class, 'store'])->name('orderpops.store')->middleware('permission:orders.create');
        Route::get('/orderpops/{orderpops}/edit', [OrderPopsController::class, 'edit'])->name('orderpops.edit')->middleware('permission:orders.edit');
        Route::put('/orderpops/{orderpops}', [OrderPopsController::class, 'update'])->name('orderpops.update')->middleware('permission:orders.edit');
        Route::delete('/orderpops/{orderpops}', [OrderPopsController::class, 'destroy'])->name('orderpops.destroy')->middleware('permission:orders.delete');

        Route::post('/orderpops/confirm', [OrderPopsController::class, 'confirmOrder'])->name('orderpops.confirm')->middleware('permission:orders.create');
        Route::post('/orderpops/{order}/mark-delivered', [OrderPopsController::class, 'markDelivered'])
            ->name('orderpops.markDelivered')->middleware('permission:orders.edit');
    });


    Route::prefix('{franchisee}/events')->group(function () {
        // Events view routes
        Route::middleware('permission:events.view')->group(function () {
            Route::get('/', [EventController::class, 'index'])->name('events.index');
            Route::get('/calender', [EventController::class, 'eventCalender'])->name('events.calender');
            Route::get('/{id}/view', [EventController::class, 'view'])->name('events.view');
            Route::get('/report', [EventController::class, 'report'])->name('events.report');
            Route::get('/{event}/compare', [EventController::class, 'compare'])->name('events.compare');
        });

        Route::get('/create', [EventController::class, 'create'])->name('events.create')->middleware('permission:events.create');
        Route::post('/', [EventController::class, 'store'])->name('events.store')->middleware('permission:events.create');
        Route::post('/update-status', [EventController::class, 'updateStatus'])->name('updateStatus')->middleware('permission:events.edit');
        Route::post('/date', [EventController::class, 'date'])->name('events.date')->middleware('permission:events.edit');
    });

    // Expense
    Route::prefix('{franchisee}')->group(function () {
        // Expense view routes
        Route::middleware('permission:expenses.view')->group(function () {
            Route::get('expense', [ExpenseController::class, 'index'])->name('expense');
            Route::get('get-subcategories/{category_id}', [ExpenseController::class, 'getSubCategories'])->name('getSubCategories');
        });

        Route::get('expense-create', [ExpenseController::class, 'create'])->name('expense.create')->middleware('permission:expenses.create');
        Route::post('expense-store', [ExpenseController::class, 'store'])->name('expense.store')->middleware('permission:expenses.create');
        Route::get('expense/{id}/edit', [ExpenseController::class, 'edit'])->name('expense.edit')->middleware('permission:expenses.edit');
        Route::put('expense/{id}/update', [ExpenseController::class, 'update'])->name('expense.update')->middleware('permission:expenses.edit');
        Route::delete('expense/{id}/delete', [ExpenseController::class, 'delete'])->name('expense.delete')->middleware('permission:expenses.delete');
    });

    // Customer
    Route::prefix('{franchisee}')->group(function () {
        // Customer view routes
        Route::middleware('permission:customers.view')->group(function () {
            Route::get('customer', [CustomerController::class, 'index'])->name('customer');
            Route::get('customer/{id}/view', [CustomerController::class, 'view'])->name('customer.view');
        });

        Route::get('customer-create', [CustomerController::class, 'create'])->name('customer.create')->middleware('permission:customers.create');
        Route::post('customer-store', [CustomerController::class, 'store'])->name('customer.store')->middleware('permission:customers.create');
        Route::get('customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit')->middleware('permission:customers.edit');
        Route::put('customer/{id}/update', [CustomerController::class, 'update'])->name('customer.update')->middleware('permission:customers.edit');
        Route::delete('customer/{id}/delete', [CustomerController::class, 'delete'])->name('customer.delete')->middleware('permission:customers.delete');
    });

    Route::prefix('{franchisee}')->group(function () {
        // Payment routes
        Route::middleware('permission:payments.view')->group(function () {
            Route::get('transactions', [PaymentController::class, 'transaction'])->name('transaction');
            Route::get('pos/{id}/expense', [PaymentController::class, 'posExpense'])->name('pos.expense');
            Route::get('pos/expenses/{id}/download', [PaymentController::class, 'posDownloadPDF'])->name('expenses.pos.download');
            Route::get('pos/{id}/order', [PaymentController::class, 'posOrder'])->name('pos.order');
            Route::get('pos/order/{id}/download', [PaymentController::class, 'posOrderDownloadPDF'])->name('order.pos.download');
            Route::get('pos/{id}/event', [PaymentController::class, 'posEvent'])->name('pos.event');
            Route::get('pos/event/{id}/download', [PaymentController::class, 'posEventDownloadPDF'])->name('event.pos.download');
            Route::get('pos/invoice/{id}/download', [PaymentController::class, 'posInvoiceDownloadPDF'])->name('invoice.pos.download');
        });
    });
    // Location
    Route::prefix('{franchisee}/locations')->name('franchise.locations.')->group(function () {
        // Location view routes
        Route::middleware('permission:locations.view')->group(function () {
            Route::get('/', [InventoryLocationController::class, 'index'])->name('index');
        });

        Route::get('/create', [InventoryLocationController::class, 'create'])->name('create')->middleware('permission:locations.create');
        Route::post('/', [InventoryLocationController::class, 'store'])->name('store')->middleware('permission:locations.create');
        Route::get('/{location}/edit', [InventoryLocationController::class, 'edit'])->name('edit')->middleware('permission:locations.edit');
        Route::put('/{location}', [InventoryLocationController::class, 'update'])->name('update')->middleware('permission:locations.edit');
        Route::delete('/{location}', [InventoryLocationController::class, 'destroy'])->name('destroy')->middleware('permission:locations.delete');
    });

    // Invoice
    Route::prefix('{franchisee}')->group(function () {
        // Invoice view routes
        Route::middleware('permission:invoices.view')->group(function () {
            Route::get('invoice', [InvoiceController::class, 'index'])->name('invoice.index');
            Route::get('invoice/{id}/view', [InvoiceController::class, 'view'])->name('invoice.view');
            Route::get('invoice/{id}/show', [InvoiceController::class, 'show'])->name('invoice.show');
            Route::get('invoice/{id}/download', [InvoiceController::class, 'download'])->name('invoice.download');
        });

        Route::get('invoice/create', [InvoiceController::class, 'create'])->name('invoice.create')->middleware('permission:invoices.create');
        Route::post('invoice', [InvoiceController::class, 'store'])->name('invoice.store')->middleware('permission:invoices.create');
        Route::get('invoice/{id}/edit', [InvoiceController::class, 'edit'])->name('invoice.edit')->middleware('permission:invoices.edit');
        Route::put('invoice/{id}/update', [InvoiceController::class, 'update'])->name('invoice.update')->middleware('permission:invoices.edit');
        Route::delete('invoice/{id}/delete', [InvoiceController::class, 'destroy'])->name('invoice.delete')->middleware('permission:invoices.delete');
    });

    // Account
    // Route::resource('account', AccountController::class);


    Route::prefix('{franchisee}')->group(function () {
        // Expense Category routes
        Route::middleware('permission:expenses.categories')->group(function () {
            Route::get('expense-category', [ExpensesCategoryController::class, 'indexExpense'])->name('expense-category');
            Route::get('expense-category/create', [ExpensesCategoryController::class, 'createExpense'])->name('expense-category.create');
            Route::get('expense-category/{id}/edit', [ExpensesCategoryController::class, 'editExpense'])->name('expense-category.edit');
            Route::put('expense-category/{id}/update', [ExpensesCategoryController::class, 'updateExpense'])->name('expense-category.update');
            Route::post('expense-category/store', [ExpensesCategoryController::class, 'storeExpense'])->name('expense-category.store');
            Route::post('expense-sub-category/store', [ExpensesCategoryController::class, 'SubstoreExpense'])->name('expense-sub-category.store');
            Route::delete('expense-sub-category/{id}/delete', [ExpensesCategoryController::class, 'deleteExpense'])->name('expense-sub-category.delete');
        });
    });

});
Route::middleware(['auth'])->prefix('franchise')->name('franchise.')->group(function () {

    Route::get('/select', [FranchiseAdminController::class, 'selectFranchisee'])->name('select_franchisee')->middleware('permission:franchises.view');
    Route::post('/set-franchisee', [FranchiseAdminController::class, 'setFranchisee'])->name('set_franchisee')->middleware('permission:franchises.view');
    Route::get('/{franchisee}/dashboard', [FranchiseAdminController::class, 'dashboard'])->name('dashboard')->middleware('permission:dashboard.view');
});

Route::get('/payment/success', [OrderPopsController::class, 'success'])->name('payment.successs');

Route::get('/payment/cancel', function () {
    return 'Payment was cancelled.';
})->name('payment.cancell');



// Stripe Connect
Route::get('/stripe/onboard', [StripeController::class, 'createConnectedAccount'])->name('franchise.stripe.onboard');
Route::get('/stripe/refresh', [StripeController::class, 'refreshOnboarding'])->name('franchise.stripe.refresh');
Route::get('/stripe/return', [StripeController::class, 'returnOnboarding'])->name('franchise.stripe.return');

Route::get('/pay/{recipient}', [StripeController::class, 'showPayForm'])->name('franchise.pay.form');
Route::post('/pay/{recipient}/intent', [StripeController::class, 'createPaymentIntent'])->name('franchise.pay.intent');

// Stripe
Route::get('stripe', [PaymentController::class, 'stripe'])->name('franchise.stripe');
Route::post('stripes', [PaymentController::class, 'stripePost'])->name('franchise.stripe.post');


// Route::middleware(['auth', 'role:franchise_admin'])->prefix('franchise_admin')->name('franchise.')->group(function () {
//     Route::get('/franchise/dashboard', [FranchiseAdminController::class, 'dashboard']);

//     // Staff routes
//     Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
//     Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
//     Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
//     Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
//     Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
//     Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');


//     Route::post('/orderpops/confirm', [OrderPopsController::class, 'confirmOrder'])->name('orderpops.confirm');
//     Route::get('/orderpops/confirm/page', [OrderPopsController::class, 'showConfirmPage'])->name('orderpops.confirm.page');
//     Route::get('/orderpops/view', [OrderPopsController::class, 'viewOrders'])->name('orderpops.view');

// });


// Route::middleware(['auth', 'role:franchise_manager'])->prefix('franchise_manager')->name('franchise_manager.')->group(function () {
//     Route::get('/manager/dashboard', [FranchiseManagerController::class, 'dashboard']);

//     // Staff routes
//     Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
//     Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
//     Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
//     Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
//     Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
//     Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
// });




/*
 |--------------------------------------------------------------------------
 | Module Route Files
 |--------------------------------------------------------------------------
 |
 | Rather than individually writing each `require`, you can auto-load them:
 |
 */

foreach (glob(__DIR__ . '/modules/*.php') as $routeFile) {
    require $routeFile;
}


// TEMP ROUTE
Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return "Storage link created successfully!";
});
// Route::get('/linkstorage', function () {
//     $targetFolder = base_path() . '/storage/app/public';
//     $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/storage';
//     symlink($targetFolder, $linkFolder);
// });

Route::get('/linkstorage2', function () {
    $targetFolder = base_path() . '/storage/app/public';
    $linkFolder = $_SERVER['DOCUMENT_ROOT'] . '/public/storage';
    symlink($targetFolder, $linkFolder);
});
// Clear Cache facade value:
Route::get('/clear_cache', function () {
    $exitCode = Artisan::call('cache:clear');
    return '<h1>Cache facade value cleared</h1>';
});

// Reoptimized class loader:
Route::get('/optimize', function () {
    $exitCode = Artisan::call('optimize');
    return '<h1>Reoptimized class loader</h1>';
});

// Route cache:
Route::get('/route_cache', function () {
    $exitCode = Artisan::call('route:cache');
    return '<h1>Routes cached</h1>';
});

// Clear Route cache:
Route::get('/route_clear', function () {
    $exitCode = Artisan::call('route:clear');
    return '<h1>Route cache cleared</h1>';
});

// Clear View cache:
Route::get('/view_clear', function () {
    $exitCode = Artisan::call('view:clear');
    return '<h1>View cache cleared</h1>';
});

// Clear Config cache:
Route::get('/config_cache', function () {
    $exitCode = Artisan::call('config:cache');
    return '<h1>Config cache cleared</h1>';
});


// Thankyou
Route::get('/payment/success/{invoice}', [PaymentController::class, 'success'])->name('payment.success');
Route::get('/payment/cancel/{invoice}', [PaymentController::class, 'cancel'])->name('payment.cancel');

require __DIR__ . '/auth.php';




Route::prefix('franchise')->name('franchise.')->middleware(['auth'])->group(function () {
    Route::get('{franchisee}/dashboard', [FranchiseAdminController::class, 'dashboard'])->name('dashboard')->middleware('permission:dashboard.view');

    // Events routes
    Route::prefix('{franchisee}/events')->name('events.')->group(function () {
        // Events view routes
        Route::middleware('permission:events.view')->group(function () {
            Route::get('/', [EventController::class, 'index'])->name('index');
            Route::get('/calender', [EventController::class, 'eventCalender'])->name('calender');
            Route::get('/{id}/view', [EventController::class, 'view'])->name('view');
            Route::get('/report', [EventController::class, 'report'])->name('report');
            Route::get('/{event}/compare', [EventController::class, 'compare'])->name('compare');
        });

        Route::get('/create', [EventController::class, 'create'])->name('create')->middleware('permission:events.create');
        Route::post('/', [EventController::class, 'store'])->name('store')->middleware('permission:events.create');
        Route::post('/update-status', [EventController::class, 'updateStatus'])->name('updateStatus')->middleware('permission:events.edit');
        Route::post('/date', [EventController::class, 'date'])->name('date')->middleware('permission:events.edit');
    });

    // Flavors routes

    Route::get('{franchisee}/flavors', [FranchiseStaffController::class, 'flavors'])->name('flavors');
    Route::get('{franchisee}/flavors/detail', [FranchiseStaffController::class, 'flavorsDetail'])->name('flavors.detail');

    // Location routes
    Route::prefix('{franchisee}/locations')->name('locations.')->group(function () {
        // Location view routes
        Route::middleware('permission:locations.view')->group(function () {
            Route::get('/', [InventoryLocationController::class, 'index'])->name('index');
        });

        Route::get('/create', [InventoryLocationController::class, 'create'])->name('create')->middleware('permission:locations.create');
        Route::post('/', [InventoryLocationController::class, 'store'])->name('store')->middleware('permission:locations.create');
        Route::get('/{location}/edit', [InventoryLocationController::class, 'edit'])->name('edit')->middleware('permission:locations.edit');
        Route::put('/{location}', [InventoryLocationController::class, 'update'])->name('update')->middleware('permission:locations.edit');
        Route::delete('/{location}', [InventoryLocationController::class, 'destroy'])->name('destroy')->middleware('permission:locations.delete');
    });
});


