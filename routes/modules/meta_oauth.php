<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Marketing\MetaOAuthController;


// Can add permission middleware; Franchises can connect their Meta accounts to manage their Facebook pages and posts.
// This route allows the franchise to connect their Meta account and handle the OAuth callback.
Route::middleware(['auth'])->group(function () {
    Route::get('/marketing/meta/connect', [MetaOAuthController::class, 'redirectToMeta'])->name('meta.connect');
    Route::get('/marketing/meta/callback', [MetaOAuthController::class, 'handleCallback'])->name('meta.callback');
});
