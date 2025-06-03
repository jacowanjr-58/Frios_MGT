<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FranchiseStaffController\PosController;
use App\Http\Controllers\FranchiseStaffController\SaleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\GoogleController;
// Corporate Admin Controllers
use App\Http\Controllers\CorporateAdminControllers\OwnerController;
use App\Http\Controllers\FranchiseAdminControllers\StaffController;
use App\Http\Controllers\CorporateAdminControllers\FgpItemsController;
use App\Http\Controllers\CorporateAdminControllers\FranchiseController;
use App\Http\Controllers\CorporateAdminControllers\ExpensesCategoryController;
use App\Http\Controllers\FranchiseAdminControllers\OrderPopsController;
// Franchise Admin Controller
use App\Http\Controllers\CorporateAdminControllers\ViewOrdersController;
use App\Http\Controllers\CorporateAdminControllers\FgpCategoryController;
use App\Http\Controllers\FranchiseAdminControllers\AdminProfileController;
use App\Http\Controllers\FranchiseStaffController\FranchiseStaffController;
// Franchise Manager Controllers
use App\Http\Controllers\CorporateAdminControllers\CorporateAdminController;
// Franchise Staff Controllers
use App\Http\Controllers\FranchiseAdminControllers\FranchiseAdminController;
use App\Http\Controllers\CorporateAdminControllers\AdditionalChargesController;
use App\Http\Controllers\CorporateAdminControllers\PaymentController as CorpPaymentController;

use App\Http\Controllers\Franchise\EventController;
use App\Http\Controllers\Franchise\StripeController;
use App\Http\Controllers\Franchise\ExpenseController;
use App\Http\Controllers\Franchise\CustomerController;
use App\Http\Controllers\Franchise\InventoryController;
use App\Http\Controllers\Franchise\PaymentController;
use App\Http\Controllers\Franchise\LocationController;
use App\Http\Controllers\Franchise\InvoiceController;
use App\Http\Controllers\Franchise\AccountController;
use App\Http\Controllers\FranchiseManagerControllers\FranchiseManagerController;
use App\Http\Middleware\StripeMiddleware;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(StripeMiddleware::class)->group(function () {

    Route::get('/dashboard', [DashboardController::class , 'dashboard'])->name('dashboard')->middleware('auth');
    Route::get('/load-more-events', [DashboardController::class , 'loadMoreEvents'])->name('loadMoreEvents')->middleware('auth');
});

Route::middleware('auth')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    // Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profile routes
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/create', [AdminProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile', [AdminProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/{profile}/edit', [AdminProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/{profile}', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/{profile}', [AdminProfileController::class, 'destroy'])->name('profile.destroy');

});


Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);


Route::middleware(['auth', 'role:corporate_admin'])->prefix('corporate_admin')->name('corporate_admin.')->group(function () {
    Route::get('/corporate/dashboard', [CorporateAdminController::class, 'dashboard']);

     // Franchise routes
     Route::get('/franchisee', [FranchiseController::class, 'index'])->name('franchise.index');
     Route::get('/franchisee/create', [FranchiseController::class, 'create'])->name('franchise.create');
     Route::post('/franchisee', [FranchiseController::class, 'store'])->name('franchise.store');
     Route::get('/franchisee/{franchise}/edit', [FranchiseController::class, 'edit'])->name('franchise.edit');
     Route::put('/franchisee/{franchise}', [FranchiseController::class, 'update'])->name('franchise.update');
     Route::delete('/franchisee/{franchise}', [FranchiseController::class, 'destroy'])->name('franchise.destroy');

    // Owner routes
    Route::get('/owner', [OwnerController::class, 'index'])->name('owner.index');
    Route::get('/owner/create', [OwnerController::class, 'create'])->name('owner.create');
    Route::post('/owner', [OwnerController::class, 'store'])->name('owner.store');
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


    //Route::resource('inventory', \App\Http\Controllers\Franchise\InventoryController::class);
    //Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    //Route::get('/inventory/detail', [InventoryController::class, 'inventoryDetail'])->name('inventory.detail');
    //Route::get('/inventory-locations', [InventoryController::class, 'inventoryLocations'])->name('inventory.locations');
    //Route::post('/allocate-inventory', [InventoryController::class, 'allocateInventory'])->name('allocate-inventory');
    //Route::post('/update-quantity', [InventoryController::class, 'updateQuantity'])->name('updateQuantity');
    //Route::post('/remove-item', [InventoryController::class, 'removeItem'])->name('removeItem');

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
    Route::resource('locations', LocationController::class);

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
