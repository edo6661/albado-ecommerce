<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 50000, 2000000);
        $tax = $subtotal * 0.11; // 11% yah ppn bang
        $total = $subtotal + $tax;

        return [
            'order_number' => Order::generateOrderNumber(),
            'user_id' => User::factory(),
            'status' => fake()->randomElement(['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled', 'failed']),
            'subtotal' => $subtotal,
            'tax' => $tax,
            'total' => $total,
            'notes' => fake()->optional(0.4)->sentence(10),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}