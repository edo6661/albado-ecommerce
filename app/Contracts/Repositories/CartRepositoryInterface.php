<?php

namespace App\Contracts\Repositories;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;

interface CartRepositoryInterface
{
    public function findByUserId(int $userId): ?Cart;
    public function create(array $data): Cart;
    public function findOrCreateByUserId(int $userId): Cart;
    public function addItem(Cart $cart, array $data): CartItem;
    public function updateItem(CartItem $item, array $data): bool;
    public function removeItem(CartItem $item): bool;
    public function clearCart(Cart $cart): bool;
    public function getCartItems(Cart $cart): Collection;
    public function getCartItemsByIds(int $userId, array $itemIds): Collection;
    public function getPaginatedCartItems(int $userId, int $perPage = 15, ?int $cursor = null): Collection;
}