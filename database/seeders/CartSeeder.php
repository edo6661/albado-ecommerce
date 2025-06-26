<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() == 0) {
            $this->call(UserSeeder::class);
        }
        
        if (Product::count() == 0) {
            $this->call(ProductSeeder::class);
        }

        $users = User::all();
        $products = Product::where('is_active', true)->get();

        $users->random(ceil($users->count() * 0.7))->each(function ($user) use ($products) {
            $cart = Cart::factory()->create([
                'user_id' => $user->id,
            ]);

            $randomProducts = $products->random(rand(1, min(5, $products->count())));
            
            foreach ($randomProducts as $product) {
                CartItem::factory()->create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'price' => $product->discount_price ?? $product->price,
                    'quantity' => rand(1, 3),
                ]);
            }
        });
    }
}