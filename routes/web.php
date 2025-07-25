<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\User\MidtransController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/cart/add', [HomeController::class, 'addToCart'])->name('cart.add');

require __DIR__ . '/user/product.php';
require __DIR__ . '/user/category.php';

Route::middleware(['auth','verified'])->group(function () {
    require __DIR__ . '/shared/profile.php';
    require __DIR__ . '/user/cart.php';
    require __DIR__ . '/user/order.php';
    require __DIR__ . '/user/payment.php';
    require __DIR__ . '/user/rating.php';

    Route::middleware('admin')->group(function () {
        Route::name('admin.')->prefix('admin')->group(function () {
            require __DIR__ . '/admin/product.php';
            require __DIR__ . '/admin/category.php';
            require __DIR__ . '/admin/dashboard.php';
            require __DIR__ . '/admin/order.php';
            require __DIR__ . '/admin/transaction.php';
        });
    });
});

require __DIR__ . '/auth.php';
