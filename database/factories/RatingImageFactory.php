<?php

namespace Database\Factories;

use App\Models\Rating;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RatingImage>
 */
class RatingImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'rating_id' => Rating::factory(),
            'path' => 'https://picsum.photos/640/480?random=' . fake()->numberBetween(1, 1000),
            'order' => fake()->numberBetween(1, 5),        
        ];
    }
}