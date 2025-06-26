<?php

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::get('orders/statistics', [OrderController::class, 'statistics'])->name('orders.statistics');
Route::get('orders/export', [OrderController::class, 'export'])->name('orders.export');

Route::resource('orders', OrderController::class)->except(['create', 'store']);