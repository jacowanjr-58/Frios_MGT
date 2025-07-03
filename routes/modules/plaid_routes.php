<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PLAID\PlaidController;

Route::middleware(['auth'])->group(function () {
    Route::get('/plaid/link-token', [PlaidController::class, 'createLinkToken']);
    Route::post('/plaid/exchange', [PlaidController::class, 'exchangePublicToken']);
    Route::post('/plaid/fetch', [PlaidController::class, 'fetchTransactions']);


    Route::get('/franchise/{franchise}/transactions', [PlaidController::class, 'index'])->name('transactions.index');
    Route::get('/franchise/{franchise}/transactions/categorize', [PlaidController::class, 'showCategorize'])->name('transactions.categorize');
    Route::get('/franchise/{franchise}/transactions/pnl', [PlaidController::class, 'pnl'])->name('transactions.pnl');
    Route::post('/franchise/{franchise}/transactions/categorize', [PlaidController::class, 'saveCategorize'])->name('transactions.categorize.save');

    Route::get('/transactions/create', [PlaidController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [PlaidController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}/edit', [PlaidController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [PlaidController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [PlaidController::class, 'destroy'])->name('transactions.destroy');
});
