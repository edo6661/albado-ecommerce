<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\CategoryController;

Route::resource('categories', CategoryController::class)
    ->only(['index', 'show']);