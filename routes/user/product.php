<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\ProductController;

Route::resource('products', ProductController::class)
    ->only(['index', 'show']);