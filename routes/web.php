<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');
Route::middleware(['auth','verified'])->group(function () {
    require __DIR__ . '/shared/profile.php';
    Route::middleware('admin')->group(function () {
        require __DIR__ . '/admin/user.php';
        require __DIR__ . '/admin/dashboard.php';
    });
});

require __DIR__ . '/auth.php';