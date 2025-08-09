<?php
// app/Http/Controllers/Api/CartController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\Services\CartServiceInterface;
use App\Http\Resources\CartResource;
use App\Http\Resources\CartSummaryResource;
use App\Http\Requests\Api\AddToCartRequest;
use App\Http\Requests\Api\Cart\CartIndexRequest;
use App\Http\Requests\Api\UpdateCartItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct(
        protected CartServiceInterface $cartService
    ) {}
  
    public function index(CartIndexRequest $request): JsonResponse
    {
        try {
            $validated = $request->getValidatedData();
            $perPage = $validated['per_page'] ?? 15;
            $cursor = $validated['cursor'] ?? null;
            
            $result = $this->cartService->getPaginatedCartItems(Auth::id(), $perPage, $cursor);
            
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diambil',
                'data' => CartResource::collection($result['data']),
                'pagination' => [
                    'has_next_page' => (bool) $result['has_next_page'],
                    'next_cursor' => $result['next_cursor'] ? (int) $result['next_cursor'] : null,
                    'per_page' => (int) $result['per_page'],
                    'current_cursor' => $cursor ? (int) $cursor : null
                ],
                'summary' => [
                    'total_items' => $result['summary']['total_items'],
                    'total_quantity' => $result['summary']['total_quantity'],
                    'total_price' => $result['summary']['total_price']
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data keranjang',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

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
                'message' => 'Gagal mengambil data keranjang',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
    
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
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }
}