<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->sentence(3);
        $price = fake()->randomFloat(2, 10000, 5000000);

        return [
            'category_id' => Category::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(3),
            'price' => $price,
            'discount_price' => fake()->optional(0.3)->randomFloat(2, 5000, $price - 1000), 
            'stock' => fake()->numberBetween(0, 200),
            'is_active' => fake()->boolean(90), 

        ];
    }
}
