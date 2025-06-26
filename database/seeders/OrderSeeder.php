<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Product;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
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

        
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $orderProducts = $products->random(rand(1, 5));
            
            
            $subtotal = 0;
            $orderItems = [];
            
            foreach ($orderProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->discount_price ?? $product->price;
                $total = $price * $quantity;
                $subtotal += $total;
                
                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $price,
                    'quantity' => $quantity,
                    'total' => $total,
                ];
            }
            
            $tax = $subtotal * 0.11; 
            $grandTotal = $subtotal + $tax;
            
            
            $order = Order::factory()->create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $grandTotal,
                'status' => fake()->randomElement(['pending', 'paid', 'processing', 'shipped', 'delivered']),
            ]);
            
            
            foreach ($orderItems as $itemData) {
                OrderItem::factory()->create(array_merge($itemData, [
                    'order_id' => $order->id,
                ]));
            }
            
            
            if (in_array($order->status, ['paid', 'processing', 'shipped', 'delivered'])) {
                Transaction::factory()->settlement()->create([
                    'order_id' => $order->id,
                    'gross_amount' => $grandTotal,
                ]);
            } elseif ($order->status === 'pending') {
                
                if (rand(0, 1)) {
                    Transaction::factory()->pending()->create([
                        'order_id' => $order->id,
                        'gross_amount' => $grandTotal,
                    ]);
                }
            }
        }
        
        
        $this->createSpecialOrders($users, $products);
    }
    
    private function createSpecialOrders($users, $products)
    {
        
        for ($i = 0; $i < 5; $i++) {
            $user = $users->random();
            $product = $products->random();
            $price = $product->discount_price ?? $product->price;
            $subtotal = $price * 2;
            $tax = $subtotal * 0.11;
            $total = $subtotal + $tax;
            
            $order = Order::factory()->cancelled()->create([
                'user_id' => $user->id,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
            
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $price,
                'quantity' => 2,
                'total' => $price * 2,
            ]);
        }
        
        
        for ($i = 0; $i < 3; $i++) {
            $user = $users->random();
            $product = $products->random();
            $price = $product->discount_price ?? $product->price;
            $subtotal = $price;
            $tax = $subtotal * 0.11;
            $total = $subtotal + $tax;
            
            $order = Order::factory()->create([
                'user_id' => $user->id,
                'status' => 'failed',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
            ]);
            
            OrderItem::factory()->create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_price' => $price,
                'quantity' => 1,
                'total' => $price,
            ]);
            
            Transaction::factory()->failed()->create([
                'order_id' => $order->id,
                'gross_amount' => $total,
            ]);
        }
    }
}