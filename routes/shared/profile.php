<?php

use App\Http\Controllers\Profile\AddressController;
use App\Http\Controllers\Profile\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::get('/addresses/json', [AddressController::class, 'getAddressesJson'])
    ->name('addresses.json');
    Route::patch('/addresses/set-default/{id}', [AddressController::class, 'setDefault'])
        ->name('addresses.set-default');
    Route::resource('addresses', AddressController::class)
        ->except(['show'])
        ->names('addresses');
});

