<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\RatingController;
use App\Http\Controllers\Api\Profile\AddressController;
use App\Http\Controllers\Api\Profile\ProfileController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmailFromLink'])->name('api.verification.verify');
        Route::post('/refresh', [AuthController::class, 'refresh']); 
    });
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{slug}', [CategoryController::class, 'show']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/products/{id}/related', [ProductController::class, 'related']);
    Route::get('/products/{productId}/ratings', [RatingController::class, 'productRatings']);
});
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
        Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
    });
    Route::prefix('cart')->group(function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/items', [CartController::class, 'store']);
        Route::patch('/items/{productId}', [CartController::class, 'update']);
        Route::delete('/items/{productId}', [CartController::class, 'destroy']);
        Route::delete('/clear', [CartController::class, 'clear']);
    });
    Route::prefix('profile')->group(function () {
        Route::get('/', [ProfileController::class, 'show']);
        Route::patch('/', [ProfileController::class, 'update']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
    });
    Route::prefix('addresses')->group(function () {
        Route::get('/', [AddressController::class, 'index']);
        Route::post('/', [AddressController::class, 'store']);
        Route::get('/{id}', [AddressController::class, 'show']);
        Route::patch('/{id}', [AddressController::class, 'update']);
        Route::delete('/{id}', [AddressController::class, 'destroy']);
        Route::patch('/{id}/set-default', [AddressController::class, 'setDefault']);
    });
    Route::prefix('ratings')->group(function () {
        Route::get('/', [RatingController::class, 'index']); 
        Route::post('/', [RatingController::class, 'store']); 
        Route::get('/{id}', [RatingController::class, 'show']); 
        Route::patch('/{id}', [RatingController::class, 'update']); 
        Route::delete('/{id}', [RatingController::class, 'destroy']); 
        Route::post('/check-eligibility', [RatingController::class, 'checkRatingEligibility']); 
    });
    Route::get('/products/{productId}/my-rating', [RatingController::class, 'userProductRating']);
    Route::prefix('admin')->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [AdminProductController::class, 'index']);                    
            Route::post('/', [AdminProductController::class, 'store']);                   
            Route::get('/{id}', [AdminProductController::class, 'show']);                 
            Route::put('/{id}', [AdminProductController::class, 'update']);               
            Route::patch('/{id}', [AdminProductController::class, 'update']);             
            Route::delete('/{id}', [AdminProductController::class, 'destroy']);           
            Route::post('/bulk-delete', [AdminProductController::class, 'bulkDestroy']);  
            Route::delete('/{productId}/images/{imageId}', [AdminProductController::class, 'deleteImage']); 
            Route::post('/export-pdf', [AdminProductController::class, 'exportPdf']);     
            Route::get('/categories/list', [AdminProductController::class, 'categories']); 
            Route::get('/statistics/summary', [AdminProductController::class, 'statistics']); 
            Route::get('/filtered/search', [AdminProductController::class, 'filtered']);  
        });
    });
    Route::prefix('categories')->group(function () {
        Route::get('/', [AdminCategoryController::class, 'index']);                    
        Route::post('/', [AdminCategoryController::class, 'store']);                   
        Route::get('/{id}', [AdminCategoryController::class, 'show']);                 
        Route::put('/{id}', [AdminCategoryController::class, 'update']);               
        Route::patch('/{id}', [AdminCategoryController::class, 'update']);             
        Route::delete('/{id}', [AdminCategoryController::class, 'destroy']);           
        Route::post('/bulk-delete', [AdminCategoryController::class, 'bulkDestroy']);  
        Route::delete('/{categoryId}/image', [AdminCategoryController::class, 'deleteImage']); 
        Route::get('/statistics/summary', [AdminCategoryController::class, 'statistics']); 
        Route::get('/filtered/search', [AdminCategoryController::class, 'filtered']);  
        Route::get('/recent/list', [AdminCategoryController::class, 'recent']);        
    });
});