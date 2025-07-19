<?php
// app/Http/Controllers/Api/CartController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\CartServiceInterface;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartSummaryResource;
use App\Http\Requests\Api\AddToCartRequest;
use App\Http\Requests\Api\UpdateCartItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        protected CartServiceInterface $cartService
    ) {}

    /**
     * Display cart contents
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $cartSummary = $this->cartService->getCartSummary(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diambil',
                'data' => new CartSummaryResource($cartSummary)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data keranjang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add product to cart
     *
     * @param AddToCartRequest $request
     * @return JsonResponse
     */
    public function store(AddToCartRequest $request): JsonResponse
    {
        try {
            $cartItem = $this->cartService->addToCart(
                Auth::id(),
                $request->product_id,
                $request->quantity
            );

            $cartSummary = $this->cartService->getCartSummary(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'data' => [
                    'cart_item' => new CartResource($cartItem),
                    'cart_summary' => new CartSummaryResource($cartSummary)
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update cart item quantity
     *
     * @param UpdateCartItemRequest $request
     * @param int $productId
     * @return JsonResponse
     */
    public function update(UpdateCartItemRequest $request, int $productId): JsonResponse
    {
        try {
            $this->cartService->updateCartItem(
                Auth::id(),
                $productId,
                $request->quantity
            );

            $cartSummary = $this->cartService->getCartSummary(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Quantity berhasil diperbarui',
                'data' => new CartSummaryResource($cartSummary)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove product from cart
     *
     * @param int $productId
     * @return JsonResponse
     */
    public function destroy(int $productId): JsonResponse
    {
        try {
            $this->cartService->removeFromCart(Auth::id(), $productId);

            $cartSummary = $this->cartService->getCartSummary(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'data' => new CartSummaryResource($cartSummary)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Clear all cart items
     *
     * @return JsonResponse
     */
    public function clear(): JsonResponse
    {
        try {
            $this->cartService->clearCart(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan',
                'data' => new CartSummaryResource([
                    'total_items' => 0,
                    'total_quantity' => 0,
                    'total_price' => 0,
                    'items' => []
                ])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengosongkan keranjang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get cart summary
     *
     * @return JsonResponse
     */
    public function summary(): JsonResponse
    {
        try {
            $cartSummary = $this->cartService->getCartSummary(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Ringkasan keranjang berhasil diambil',
                'data' => new CartSummaryResource($cartSummary)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil ringkasan keranjang',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}