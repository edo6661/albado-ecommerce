<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;

Route::delete('categories/bulk-destroy', [CategoryController::class, 'bulkDestroy'])->name('categories.bulk-destroy');
Route::delete('categories/{category}/image', [CategoryController::class, 'deleteImage'])
    ->name('categories.image.delete');
Route::resource('categories', CategoryController::class);