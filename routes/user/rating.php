<?php


use App\Http\Controllers\User\RatingController;
use Illuminate\Support\Facades\Route;

Route::prefix('ratings')->name('ratings.')->group(function () {
    Route::get('/', [RatingController::class, 'index'])->name('index');
    Route::get('/create/{product}', [RatingController::class, 'create'])->name('create');
    Route::post('/', [RatingController::class, 'store'])->name('store');
    Route::get('/{rating}', [RatingController::class, 'show'])->name('show');
    Route::get('/{rating}/edit', [RatingController::class, 'edit'])->name('edit');
    Route::put('/{rating}', [RatingController::class, 'update'])->name('update');
    Route::delete('/{rating}', [RatingController::class, 'destroy'])->name('destroy');
    Route::post('/check', [RatingController::class, 'check'])->name('check');
});

Route::get('/products/{product}/ratings', [RatingController::class, 'productRatings'])
    ->name('products.ratings');
