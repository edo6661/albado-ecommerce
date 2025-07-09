<?php

use App\Http\Controllers\ShippingController;
use App\Http\Controllers\User\PaymentController;
use Illuminate\Support\Facades\Route;
Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::get('/checkout/summary/{order}', [PaymentController::class, 'checkoutSummary'])->name('checkout.summary');
Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('payment.callback'); 
Route::get('/payment/finish', [PaymentController::class, 'paymentFinish'])->name('payment.finish'); 
Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping'])
    ->middleware('auth')
    ->name('shipping.calculate');