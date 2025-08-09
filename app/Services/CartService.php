<?php
namespace App\Services;
use App\Contracts\Repositories\CartRepositoryInterface;
use App\Contracts\Repositories\ProductRepositoryInterface;
use App\Contracts\Services\CartServiceInterface;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
class CartService implements CartServiceInterface
{
    public function __construct(
        protected CartRepositoryInterface $cartRepository,
        protected ProductRepositoryInterface $productRepository
    ) {}
    public function getOrCreateCart(int $userId): Cart
    {
        return $this->cartRepository->findOrCreateByUserId($userId);
    }
    public function getCartItemsByIds(int $userId, array $itemIds): Collection
    {
        return $this->cartRepository->getCartItemsByIds($userId, $itemIds);
    }
    public function addToCart(int $userId, int $productId, int $quantity = 1): CartItem
    {
        return DB::transaction(function () use ($userId, $productId, $quantity) {
            $product = $this->productRepository->findById($productId);
            if (!$product) {
                throw new \Exception('Produk tidak ditemukan.');
            }
            if (!$product->is_active) {
                throw new \Exception('Produk tidak aktif.');
            }
            if ($product->stock < $quantity) {
                throw new \Exception('Stok produk tidak mencukupi.');
            }
            $cart = $this->getOrCreateCart($userId);
            $existingItem = $cart->items()->where('product_id', $productId)->first();
            if ($existingItem) {
                $newQuantity = $existingItem->quantity + $quantity;
                if ($product->stock < $newQuantity) {
                    throw new \Exception('Stok produk tidak mencukupi.');
                }
                $price = $product->discount_price ?? $product->price;
                $this->cartRepository->updateItem($existingItem, [
                    'quantity' => $newQuantity,
                    'price' => $price
                ]);
                return $existingItem->fresh();
            }
            $price = $product->discount_price ?? $product->price;
            return $this->cartRepository->addItem($cart, [
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price
            ]);
        });
    }
    public function updateCartItem(int $userId, int $productId, int $quantity): bool
    {
        return DB::transaction(function () use ($userId, $productId, $quantity) {
            $cart = $this->cartRepository->findByUserId($userId);
            if (!$cart) {
                throw new \Exception('Keranjang tidak ditemukan.');
            }
            $item = $cart->items()->where('product_id', $productId)->first();
            if (!$item) {
                throw new \Exception('Item tidak ditemukan di keranjang.');
            }
            $product = $this->productRepository->findById($productId);
            if ($product->stock < $quantity) {
                throw new \Exception('Stok produk tidak mencukupi.');
            }
            if ($quantity <= 0) {
                return $this->cartRepository->removeItem($item);
            }
            $price = $product->discount_price ?? $product->price;
            return $this->cartRepository->updateItem($item, [
                'quantity' => $quantity,
                'price' => $price
            ]);
        });
    }
    public function removeFromCart(int $userId, int $productId): bool
    {
        return DB::transaction(function () use ($userId, $productId) {
            $cart = $this->cartRepository->findByUserId($userId);
            if (!$cart) {
                return false;
            }
            $item = $cart->items()->where('product_id', $productId)->first();
            if (!$item) {
                return false;
            }
            return $this->cartRepository->removeItem($item);
        });
    }
    public function clearCart(int $userId): bool
    {
        $cart = $this->cartRepository->findByUserId($userId);
        if (!$cart) {
            return false;
        }
        return $this->cartRepository->clearCart($cart);
    }
    public function getCartSummary(int $userId): array
    {
        $cart = $this->cartRepository->findByUserId($userId);
        if (!$cart) {
            return [
                'total_items' => 0,
                'total_quantity' => 0,
                'total_price' => 0,
                'items' => []
            ];
        }
        $items = $this->cartRepository->getCartItems($cart);
        return [
            'total_items' => $items->count(),
            'total_quantity' => $cart->total_quantity,
            'total_price' => $cart->total_price,
            'items' => $items
        ];
    }
    public function getPaginatedCartItems(int $userId, int $perPage = 15, ?int $cursor = null): array
    {
        $cartItems = $this->cartRepository->getPaginatedCartItems($userId, $perPage, $cursor);
        $hasNextPage = $cartItems->count() > $perPage;
        if ($hasNextPage) {
            $cartItems->pop(); 
        }
        $nextCursor = $hasNextPage && $cartItems->isNotEmpty() ? $cartItems->last()->id : null;
        $cartSummary = $this->getCartSummary($userId);
        return [
            'data' => $cartItems,           
            'has_next_page' => $hasNextPage, 
            'next_cursor' => $nextCursor,    
            'per_page' => $perPage,
            'summary' => $cartSummary          
        ];
    }
}