<?php

use App\Http\Controllers\User\PaymentController;
use Illuminate\Support\Facades\Route;
Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
Route::get('/checkout/summary/{order}', [PaymentController::class, 'checkoutSummary'])->name('checkout.summary');
Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/finish', [PaymentController::class, 'paymentFinish'])->name('payment.finish');
Route::get('/payment/unfinish', [PaymentController::class, 'paymentUnfinish'])->name('payment.unfinish');
Route::get('/payment/error', [PaymentController::class, 'paymentError'])->name('payment.error');  