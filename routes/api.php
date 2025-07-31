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
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderTrackingController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Api\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/payment/callback', [PaymentController::class, 'paymentCallback'])->name('api.payment.callback');
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
        Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmailFromLink'])->name('api.verification.verify');
        // Route::post('/refresh', [AuthController::class, 'refresh']); 
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
        Route::post('/verify-email', action: [AuthController::class, 'verifyEmail']);
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

    Route::prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('api.orders.index');
        Route::get('/{order}', [OrderController::class, 'show'])->name('api.orders.show');
        Route::post('/{order}/resume-payment', [OrderController::class, 'resumePayment'])->name('api.orders.resume-payment');
        Route::get('/{orderId}/track', [OrderTrackingController::class, 'show'])->name('api.orders.track');
    });
    Route::prefix('payment')->group(function () {
        Route::post('/checkout', [PaymentController::class, 'checkout'])->name('api.payment.checkout');
        Route::get('/orders/{order}/summary', [PaymentController::class, 'checkoutSummary'])->name('api.payment.checkout-summary');
        Route::get('/status', [PaymentController::class, 'paymentStatus'])->name('api.payment.status');
        Route::post('/calculate-shipping', [PaymentController::class, 'calculateShipping'])->name('api.payment.calculate-shipping');
        Route::post('/orders/{order}/resume', [PaymentController::class, 'resumePayment'])->name('api.payment.resume');
    });
    Route::prefix('admin')->middleware(['api.admin'])->group(function () {
        Route::prefix('products')->group(function () {
            Route::get('/', [AdminProductController::class, 'index']);                    
            Route::post('/', [AdminProductController::class, 'store']);                   
            Route::get('/{id}', [AdminProductController::class, 'show']);                 
            Route::patch('/{id}', [AdminProductController::class, 'update']);             
            Route::delete('/{id}', [AdminProductController::class, 'destroy']);           
            Route::post('/bulk-delete', [AdminProductController::class, 'bulkDestroy']);  
            Route::delete('/{productId}/images/{imageId}', [AdminProductController::class, 'deleteImage']); 
            Route::post('/export-pdf', [AdminProductController::class, 'exportPdf']);     
            Route::get('/statistics/summary', [AdminProductController::class, 'statistics']); 
            Route::get('/filtered/search', [AdminProductController::class, 'filtered']);  
        });
        Route::prefix('categories')->group(function () {
            Route::get('/', [AdminCategoryController::class, 'index']);                    
            Route::post('/', [AdminCategoryController::class, 'store']);               
            Route::get('/{id}', [AdminCategoryController::class, 'show']);                 
            Route::patch('/{id}', [AdminCategoryController::class, 'update']);             
            Route::delete('/{id}', [AdminCategoryController::class, 'destroy']);           
            Route::post('/bulk-delete', [AdminCategoryController::class, 'bulkDestroy']);  
            Route::delete('/{categoryId}/image', [AdminCategoryController::class, 'deleteImage']); 
            Route::get('/statistics/summary', [AdminCategoryController::class, 'statistics']); 
            Route::get('/filtered/search', [AdminCategoryController::class, 'filtered']);  
            Route::get('/recent/list', [AdminCategoryController::class, 'recent']);        
        });
        Route::prefix('orders')->group(function () {
            Route::get('/', [AdminOrderController::class, 'index']);                      
            Route::get('/statistics', [AdminOrderController::class, 'statistics']);      
            Route::get('/filtered', [AdminOrderController::class, 'filtered']);          
            Route::get('/users', [AdminOrderController::class, 'users']);                
            Route::get('/status-options', [AdminOrderController::class, 'statusOptions']); 
            Route::post('/export-pdf', [AdminOrderController::class, 'exportPdf']);      
            Route::get('/{id}', [AdminOrderController::class, 'show']);                  
            Route::get('/{id}/edit', [AdminOrderController::class, 'edit']);             
            Route::put('/{id}', [AdminOrderController::class, 'update']);                
            Route::patch('/{id}', [AdminOrderController::class, 'update']);              
            Route::patch('/{id}/status', [AdminOrderController::class, 'updateStatus']); 
            Route::patch('/{id}/cancel', [AdminOrderController::class, 'cancel']);       
        });
        Route::prefix('transactions')->group(function () {
            Route::get('/', [AdminTransactionController::class, 'index']);                        
            Route::get('/statistics', [AdminTransactionController::class, 'statistics']);        
            Route::get('/filtered', [AdminTransactionController::class, 'filtered']);            
            Route::get('/status-options', [AdminTransactionController::class, 'statusOptions']); 
            Route::post('/export-pdf', [AdminTransactionController::class, 'exportPdf']);        
            Route::get('/orders/{orderId}', [AdminTransactionController::class, 'byOrder']);     
            Route::get('/payment-types/{paymentType}', [AdminTransactionController::class, 'byPaymentType']); 
            Route::get('/{id}', [AdminTransactionController::class, 'show']);                    
            Route::get('/{id}/edit', [AdminTransactionController::class, 'edit']);               
            Route::put('/{id}', [AdminTransactionController::class, 'update']);                  
            Route::patch('/{id}', [AdminTransactionController::class, 'update']);                
            Route::patch('/{id}/status', [AdminTransactionController::class, 'updateStatus']);   
            Route::delete('/{id}', [AdminTransactionController::class, 'destroy']);              
        });
    });
}); 