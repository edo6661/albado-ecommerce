<?php

use App\Http\Controllers\Admin\TransactionController;
use Illuminate\Support\Facades\Route;

Route::patch('transactions/{id}/status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
Route::resource('transactions', TransactionController::class);