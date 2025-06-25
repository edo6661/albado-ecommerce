<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::delete('products/bulk-destroy', [ProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
Route::delete('products/{product}/images/{image}', [ProductController::class, 'deleteImage'])
    ->name('products.images.delete');
Route::resource('products', ProductController::class);