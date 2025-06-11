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
use App\Http\Middleware\StripeMiddleware;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(StripeMiddleware::class)->group(function () {

    Route::get('/dashboard', [DashboardController::class , 'dashboard'])->name('dashboard')->middleware('auth');
    Route::get('/load-more-events', [DashboardController::class , 'loadMoreEvents'])->name('loadMoreEvents')->middleware('auth');
});

Route::middleware('auth')->group(function () {


    // Profile routes
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/create', [AdminProfileController::class, 'create'])->name('profile.create');
    Route::get('/profile/{profile}', [AdminProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [AdminProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/{profile}/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{profile}', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/{profile}', [AdminProfileController::class, 'destroy'])->name('profile.destroy');

});


Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);



Route::middleware(['auth', 'role:franchise_admin|franchise_manager' , StripeMiddleware::class])->prefix('franchise')->name('franchise.')->group(function () {
    Route::get('/dashboard', [FranchiseAdminController::class, 'dashboard'])->name('dashboard');

    // Staff routes
    Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    // Order pops routes
    Route::get('/orderpops', [OrderPopsController::class, 'index'])->name('orderpops.index');
    Route::get('/orderpops/create', [OrderPopsController::class, 'create'])->name('orderpops.create');
    Route::post('/orderpops/store', [OrderPopsController::class, 'store'])->name('orderpops.store');
    Route::get('/orderpops/{orderpops}/edit', [OrderPopsController::class, 'edit'])->name('orderpops.edit');
    Route::put('/orderpops/{orderpops}', [OrderPopsController::class, 'update'])->name('orderpops.update');
    Route::delete('/orderpops/{orderpops}', [OrderPopsController::class, 'destroy'])->name('orderpops.destroy');

    Route::post('/orderpops/confirm', [OrderPopsController::class, 'confirmOrder'])->name('orderpops.confirm');
    Route::get('/orderpops/confirm/page', [OrderPopsController::class, 'showConfirmPage'])->name('orderpops.confirm.page');
     Route::get('/orderpops/view', [OrderPopsController::class, 'viewOrders'])->name('orderpops.view');
    Route::post('/orderpops/{order}/mark-delivered', [OrderPopsController::class, 'markDelivered'])
    ->name('orderpops.markDelivered');



    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/calender', [EventController::class, 'eventCalender'])->name('events.calender');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::get('/events/{id}/view', [EventController::class, 'view'])->name('events.view');
    Route::get('/events/report', [EventController::class, 'report'])->name('events.report');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::post('/events/update-status', [EventController::class, 'updateStatus'])->name('updateStatus');
    Route::get('/events/{event}/compare', [EventController::class, 'compare'])->name('events.compare');
    Route::post('/events/date', [EventController::class, 'date'])->name('events.date');

    // Expense
    Route::get('expense' , [ExpenseController::class , 'index'])->name('expense');
    Route::get('expense-create' , [ExpenseController::class , 'create'])->name('expense.create');
    Route::post('expense-store' , [ExpenseController::class , 'store'])->name('expense.store');
    Route::get('expense/{id}/edit' , [ExpenseController::class , 'edit'])->name('expense.edit');
    Route::put('expense/{id}/update' , [ExpenseController::class , 'update'])->name('expense.update');
    Route::delete('expense/{id}/delete' , [ExpenseController::class , 'delete'])->name('expense.delete');
    Route::get('/get-subcategories/{category_id}', [ExpenseController::class, 'getSubCategories'])->name('getSubCategories');

    // Customer
    Route::get('customer' , [CustomerController::class , 'index'])->name('customer');
    Route::get('customer-create' , [CustomerController::class , 'create'])->name('customer.create');
    Route::post('customer-store' , [CustomerController::class , 'store'])->name('customer.store');
    Route::get('customer/{id}/edit' , [CustomerController::class , 'edit'])->name('customer.edit');
    Route::get('customer/{id}/view' , [CustomerController::class , 'view'])->name('customer.view');
    Route::put('customer/{id}/update' , [CustomerController::class , 'update'])->name('customer.update');
    Route::delete('customer/{id}/delete' , [CustomerController::class , 'delete'])->name('customer.delete');

    // Payment
    Route::get('transactions' , [PaymentController::class , 'transaction'])->name('transaction');
    Route::get('pos/{id}/expense' , [PaymentController::class , 'posExpense'])->name('pos.expense');
    Route::get('pos/expenses/{id}/download', [PaymentController::class, 'posDownloadPDF'])->name('expenses.pos.download');
    Route::get('pos/{id}/order' , [PaymentController::class , 'posOrder'])->name('pos.order');
    Route::get('pos/order/{id}/download', [PaymentController::class, 'posOrderDownloadPDF'])->name('order.pos.download');
    Route::get('pos/{id}/event' , [PaymentController::class , 'posEvent'])->name('pos.event');
    Route::get('pos/event/{id}/download', [PaymentController::class, 'posEventDownloadPDF'])->name('event.pos.download');
    Route::get('pos/invoice/{id}/download', [PaymentController::class, 'posInvoiceDownloadPDF'])->name('invoice.pos.download');

    // Location
    Route::resource('locations', InventoryLocationController::class);

    // Invoice
    Route::resource('invoice', InvoiceController::class);

    // Account
    // Route::resource('account', AccountController::class);



    // Expense Category
    Route::get('expense-category' , [ExpensesCategoryController::class , 'indexExpense'])->name('expense-category');
    Route::get('expense-category/create' , [ExpensesCategoryController::class , 'createExpense'])->name('expense-category.create');
    Route::get('expense-category/{id}/edit' , [ExpensesCategoryController::class , 'editExpense'])->name('expense-category.edit');
    Route::put('expense-category/{id}/update' , [ExpensesCategoryController::class , 'updateExpense'])->name('expense-category.update');
    Route::post('expense-category/store' , [ExpensesCategoryController::class , 'storeExpense'])->name('expense-category.store');
    Route::post('expense-sub-category/store' , [ExpensesCategoryController::class , 'SubstoreExpense'])->name('expense-sub-category.store');
    Route::delete('expense-sub-category/{id}/delete' , [ExpensesCategoryController::class , 'deleteExpense'])->name('expense-sub-category.delete');


});

Route::get('/payment/success', [OrderPopsController::class , 'success'])->name('payment.successs');

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
    Route::get('stripe' , [PaymentController::class , 'stripe'])->name('franchise.stripe');
    Route::post('stripes' , [PaymentController::class , 'stripePost'])->name('franchise.stripe.post');


// Route::middleware(['auth', 'role:franchise_admin'])->prefix('franchise_admin')->name('franchise.')->group(function () {
//     Route::get('/franchise/dashboard', [FranchiseAdminController::class, 'dashboard']);

//     // Staff routes
//     Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
//     Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
//     Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
//     Route::get('/staff/{staff}/edit', [StaffController::class, 'edit'])->name('staff.edit');
//     Route::put('/staff/{staff}', [StaffController::class, 'update'])->name('staff.update');
//     Route::delete('/staff/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

//     // Order pops routes
//     Route::get('/orderpops', [OrderPopsController::class, 'index'])->name('orderpops.index');
//     Route::get('/orderpops/create', [OrderPopsController::class, 'create'])->name('orderpops.create');
//     Route::post('/orderpops/store', [OrderPopsController::class, 'store'])->name('orderpops.store');
//     Route::get('/orderpops/{orderpops}/edit', [OrderPopsController::class, 'edit'])->name('orderpops.edit');
//     Route::put('/orderpops/{orderpops}', [OrderPopsController::class, 'update'])->name('orderpops.update');
//     Route::delete('/orderpops/{orderpops}', [OrderPopsController::class, 'destroy'])->name('orderpops.destroy');


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

require __DIR__.'/auth.php';


Route::get('/test-reset-migrations', function () {
    // Truncate the migrations table
    DB::table('migrations')->truncate();

    // Insert the predefined rows
    DB::table('migrations')->insert([
        ['id' => 1, 'migration' => '0001_01_01_000000_create_users_table', 'batch' => 1],
        ['id' => 2, 'migration' => '0001_01_01_000001_create_cache_table', 'batch' => 1],
        ['id' => 3, 'migration' => '0001_01_01_000002_create_jobs_table', 'batch' => 1],
        ['id' => 4, 'migration' => '2025_03_16_125620_create_permission_tables', 'batch' => 1],
        ['id' => 5, 'migration' => '2025_03_16_202809_create_franchisees_table', 'batch' => 1],
        ['id' => 6, 'migration' => '2025_03_16_202821_create_fgp_items_table', 'batch' => 1],
        ['id' => 7, 'migration' => '2025_03_16_202829_create_fgp_categories_table', 'batch' => 1],
        ['id' => 8, 'migration' => '2025_03_16_202835_create_fgp_orders_table', 'batch' => 1],
        ['id' => 9, 'migration' => '2025_03_16_202841_create_inventories_table', 'batch' => 1],
        ['id' => 10, 'migration' => '2025_03_16_202848_create_locations_table', 'batch' => 1],
        ['id' => 11, 'migration' => '2025_03_16_202902_create_order_invoices_table', 'batch' => 1],
        ['id' => 12, 'migration' => '2025_03_16_202908_create_order_items_table', 'batch' => 1],
        ['id' => 13, 'migration' => '2025_03_16_202913_create_payments_table', 'batch' => 1],
        ['id' => 14, 'migration' => '2025_03_16_202917_create_events_table', 'batch' => 1],
        ['id' => 15, 'migration' => '2025_03_16_202924_create_customers_table', 'batch' => 1],
        ['id' => 16, 'migration' => '2025_03_16_202931_create_sales_table', 'batch' => 1],
        ['id' => 17, 'migration' => '2025_03_16_202936_create_expenses_table', 'batch' => 1],
        ['id' => 18, 'migration' => '2025_03_16_202942_create_expense_categories_table', 'batch' => 1],
        ['id' => 19, 'migration' => '2025_03_24_020235_create_fgp_category_fgp_item_table', 'batch' => 1],
        ['id' => 20, 'migration' => '2025_03_27_221051_create_additionalcharges_table', 'batch' => 1],
        ['id' => 21, 'migration' => '2025_04_17_171838_create_inventory_allocations_table', 'batch' => 1],
        ['id' => 22, 'migration' => '2025_04_23_163520_create_franchise_events_table', 'batch' => 1],
        ['id' => 23, 'migration' => '2025_04_23_163525_create_franchise_event_items_table', 'batch' => 1],
        ['id' => 24, 'migration' => '2025_04_29_171010_create_fgp_order_details_table', 'batch' => 1],
        ['id' => 25, 'migration' => '2025_05_03_160308_create_expense_sub_categories_table', 'batch' => 1],
        ['id' => 26, 'migration' => '2025_05_10_030920_create_expense_transactions_table', 'batch' => 1],
        ['id' => 27, 'migration' => '2025_05_10_043326_create_order_transactions_table', 'batch' => 1],
        ['id' => 28, 'migration' => '2025_05_10_051550_create_event_transactions_table', 'batch' => 1],
        ['id' => 29, 'migration' => '2025_05_11_102611_create_invoices_table', 'batch' => 1],
        ['id' => 30, 'migration' => '2025_05_11_102618_create_invoice_items_table', 'batch' => 1],
        ['id' => 31, 'migration' => '2025_05_15_002647_create_accounts_table', 'batch' => 1],
        ['id' => 32, 'migration' => '2025_05_19_184945_create_stripes_table', 'batch' => 1],
        ['id' => 33, 'migration' => '2025_05_19_193751_create_invoice_transactions_table', 'batch' => 1],
        ['id' => 34, 'migration' => '2025_05_23_000001_add_direction_to_invoices_table', 'batch' => 1],
        ['id' => 35, 'migration' => '2025_05_23_204650_add_shipping_columns_to_fgp_orders_table', 'batch' => 1],
        ['id' => 36, 'migration' => '2025_05_24_035357_add_order_num_to_invoices_table', 'batch' => 1],
        ['id' => 37, 'migration' => '2025_05_24_040046_add_order_num_to_order_transactions_table', 'batch' => 1],
        ['id' => 38, 'migration' => '2025_05_24_042247_add_fields_to_invoices_table', 'batch' => 1],
        ['id' => 39, 'migration' => '2025_06_02_205632_add_franchise__i_d__f_g_p__orders', 'batch' => 1],
        ['id' => 40, 'migration' => '2025_06_03_003738_fgp_itemnullable_add_custom_item', 'batch' => 1],
        ['id' => 41, 'migration' => '2025_06_04_000000_add_franchisee_id_foreign_to_users_table', 'batch' => 1],
        ['id' => 42, 'migration' => '2025_06_02_000000_create_inventory_master_table', 'batch' => 2],
        ['id' => 43, 'migration' => '2025_06_02_000100_create_inventory_transactions_table', 'batch' => 3],
        ['id' => 44, 'migration' => '2025_06_02_000300_create_inventory_removal_queue_table', 'batch' => 4],
    ]);

    return 'Migrations table reset successfully.';
});
