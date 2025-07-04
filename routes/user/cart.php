<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/cart/summary', [HomeController::class, 'getCartSummary'])->name('cart.summary');
Route::post('/cart/update', [HomeController::class, 'updateCartItem'])->name('cart.update');
Route::post('/cart/remove', [HomeController::class, 'removeFromCart'])->name('cart.remove');