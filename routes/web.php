<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware('auth')->group(function () {
    Route::middleware('admin')->group(function () {
        require __DIR__ . '/admin/user.php';
        require __DIR__ . '/admin/dashboard.php';
    });
});

require __DIR__ . '/auth.php';