<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Category::count() == 0) {
            $this->call(CategorySeeder::class);
        }
        $categoryIds = Category::pluck('id');

        Product::factory(30)->make()->each(function ($product) use ($categoryIds) {
            $product->category_id = $categoryIds->random();
            $product->save();
        });
    }
}
