<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BillController;

Route::get('/', [BillController::class, 'index'])->name('bills.index');

Route::resource('products', ProductController::class);
Route::resource('bills', BillController::class)->except(['destroy']);
Route::get('/bills/{bill}/edit', [BillController::class, 'edit'])->name('bills.edit');
Route::put('/bills/{bill}', [BillController::class, 'update'])->name('bills.update');
