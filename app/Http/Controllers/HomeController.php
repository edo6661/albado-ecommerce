<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CartServiceInterface;
use App\Contracts\Services\CategoryServiceInterface;
use App\Contracts\Services\MidtransServiceInterface;
use App\Contracts\Services\OrderServiceInterface;
use App\Contracts\Services\ProductServiceInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService,
        protected ProductServiceInterface $productService,
        protected CartServiceInterface $cartService,
        protected OrderServiceInterface $orderService,
        protected MidtransServiceInterface $midtransService
    ) {}

    public function index(Request $request): View
    {
        $categories = $this->categoryService->getRecentCategories(8);
        $featuredProducts = $this->productService->getRecentProducts(8);
        
        $page = $request->get('page', 1);
        $activeProducts = $this->productService->getPaginatedActiveProducts(12, $page);
        
        return view('home', compact('categories', 'featuredProducts', 'activeProducts'));
    }

    public function addToCart(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu',
                'redirect' => route('login')
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

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
                'cart_summary' => $cartSummary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    public function updateCartItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            $this->cartService->updateCartItem(
                Auth::id(),
                $request->product_id,
                $request->quantity
            );

            $cartSummary = $this->cartService->getCartSummary(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Quantity berhasil diperbarui',
                'cart_summary' => $cartSummary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function removeFromCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id'
        ]);

        try {
            $this->cartService->removeFromCart(Auth::id(), $request->product_id);

            $cartSummary = $this->cartService->getCartSummary(Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang',
                'cart_summary' => $cartSummary
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getCartSummary(): JsonResponse
    {
        $cartSummary = $this->cartService->getCartSummary(Auth::id());

        return response()->json([
            'success' => true,
            'cart_summary' => $cartSummary
        ]);
    }
    
}