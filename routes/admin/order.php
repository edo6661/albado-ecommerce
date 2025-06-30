<?php

use App\Http\Controllers\Admin\OrderController;
use Illuminate\Support\Facades\Route;

Route::patch('orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
Route::patch('orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
Route::get('orders/export/pdf', [OrderController::class, 'exportPdf'])->name('orders.export.pdf');
Route::resource('orders', OrderController::class)->except(['create', 'store','destroy']);