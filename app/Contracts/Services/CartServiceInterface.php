<?php

namespace App\Contracts\Services;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Collection;

interface CartServiceInterface
{
    public function getOrCreateCart(int $userId): Cart;
    public function addToCart(int $userId, int $productId, int $quantity = 1): CartItem;
    public function updateCartItem(int $userId, int $productId, int $quantity): bool;
    public function removeFromCart(int $userId, int $productId): bool;
    public function clearCart(int $userId): bool;
    public function getCartSummary(int $userId): array;
    public function getCartItemsByIds(int $userId, array $itemIds): Collection;
}