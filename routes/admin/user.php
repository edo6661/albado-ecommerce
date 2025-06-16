<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
// Route::get('/users', [UserController::class, 'index'])->name('users.index');
// Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');

// Route::resource('users', UserController::class)->except(['index', 'show']);
Route::resource('users', UserController::class);
