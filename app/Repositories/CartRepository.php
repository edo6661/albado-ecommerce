<?php

namespace App\Repositories;

use App\Contracts\Repositories\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Database\Eloquent\Collection;
class CartRepository implements CartRepositoryInterface
{
    public function __construct(protected Cart $model, protected CartItem $cartItemModel) {}

    public function findByUserId(int $userId): ?Cart
    {
        return $this->model->with(['items.product.category', 'items.product.images'])
                          ->where('user_id', $userId)
                          ->first();
    }

    public function create(array $data): Cart
    {
        return $this->model->create($data);
    }

    public function findOrCreateByUserId(int $userId): Cart
    {
        $cart = $this->findByUserId($userId);
        
        if (!$cart) {
            $cart = $this->create(['user_id' => $userId]);
        }
        
        return $cart;
    }

    public function addItem(Cart $cart, array $data): CartItem
    {
        return $cart->items()->create($data);
    }

    public function updateItem(CartItem $item, array $data): bool
    {
        return $item->update($data);
    }

    public function removeItem(CartItem $item): bool
    {
        return $item->delete();
    }

    public function clearCart(Cart $cart): bool
    {
        return $cart->items()->delete();
    }

    public function getCartItems(Cart $cart): Collection
    {
        return $cart->items()->with(['product.category', 'product.images'])->get();
    }
    public function getCartItemsByIds(int $userId, array $itemIds): Collection
    {
        return CartItem::with('product')
            ->whereHas('cart', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereIn('id', $itemIds)
            ->get();
    }
}