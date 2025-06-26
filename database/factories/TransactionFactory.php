<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grossAmount = fake()->randomFloat(2, 50000, 2000000);
        $transactionTime = fake()->dateTimeBetween('-1 month', 'now');
        $status = fake()->randomElement(['pending', 'settlement', 'capture', 'deny', 'cancel', 'expire', 'failure']);
        
        return [
            'order_id' => Order::factory(),
            'transaction_id' => fake()->unique()->uuid(),
            'order_id_midtrans' => Transaction::generateMidtransOrderId(),
            'payment_type' => fake()->randomElement(['credit_card', 'bank_transfer', 'echannel', 'gopay', 'shopeepay', 'qris', 'cstore', 'akulaku']),
            'status' => $status,
            'gross_amount' => $grossAmount,
            'currency' => 'IDR',
            'transaction_time' => $transactionTime,
            'settlement_time' => $status === 'settlement' ? fake()->dateTimeBetween($transactionTime, 'now') : null,
            'midtrans_response' => [
                'status_code' => fake()->randomElement(['200', '201', '400', '404']),
                'status_message' => fake()->sentence(),
                'transaction_id' => fake()->uuid(),
                'gross_amount' => $grossAmount,
            ],
            'fraud_status' => fake()->optional(0.3)->randomElement(['accept', 'challenge', 'deny']),
            'status_message' => fake()->sentence(),
        ];
    }

  
    public function settlement(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'settlement',
            'settlement_time' => fake()->dateTimeBetween($attributes['transaction_time'] ?? '-1 day', 'now'),
            'fraud_status' => 'accept',
        ]);
    }

   
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'settlement_time' => null,
        ]);
    }

 
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failure',
            'settlement_time' => null,
        ]);
    }
}