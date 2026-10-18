<?php

use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ClientsController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProcessController;
use App\Http\Controllers\Public\ServicesController;
use App\Http\Controllers\Public\WorkController;
use Illuminate\Support\Facades\Route;

/**
 * Public-site routes. No admin route is declared here or anywhere in this
 * file: Filament's panel provider owns everything under /admin.
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/work', [WorkController::class, 'index'])->name('work.index');
Route::get('/work/{project:slug}', [WorkController::class, 'show'])->name('work.show');

Route::get('/services', [ServicesController::class, 'index'])->name('services');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/process', [ProcessController::class, 'index'])->name('process');
Route::get('/clients', [ClientsController::class, 'index'])->name('clients');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/contact/received', [ContactController::class, 'received'])->name('contact.received');