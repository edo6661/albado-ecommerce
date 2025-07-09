<?php

use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\User\OrderController;
use Illuminate\Support\Facades\Route;
Route::get('/orders/{orderId}/track', [OrderTrackingController::class, 'show'])
    ->middleware('auth')
    ->name('orders.track');
Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
Route::post('/orders/{order}/resume-payment', [OrderController::class, 'resumePayment'])->name('orders.resume-payment');
