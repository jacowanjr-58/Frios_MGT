<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Marketing\PostTemplateController;
use App\Http\Controllers\Marketing\ScheduledPostController;

Route::middleware(['auth', 'role:corporate_admin'])->post('/marketing/template-library', [PostTemplateController::class, 'store']);
Route::middleware(['auth', 'role:franchise_admin'])->get('/marketing/select-template', [PostTemplateController::class, 'index']);


Route::get('/marketing/template/{id}/use', [PostTemplateController::class, 'useTemplate'])->name('franchise.marketing.use');
Route::get('/marketing/create-post', fn () => view('franchise_admin.marketing.template-customizer'))->name('franchise.marketing.create');


    // Franchise
Route::get('/marketing/scheduled-posts', [ScheduledPostController::class, 'indexFranchise'])->middleware('role:franchise_admin');
Route::delete('/marketing/post/{id}/cancel', [ScheduledPostController::class, 'cancel'])->middleware('role:franchise_admin');
Route::post('/marketing/post/{id}/reschedule', [ScheduledPostController::class, 'reschedule'])->middleware('role:franchise_admin');

// Corporate
Route::get('/corporate/scheduled-posts', [ScheduledPostController::class, 'indexCorporate'])->middleware('role:corporate_admin');
