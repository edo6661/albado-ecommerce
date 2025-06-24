<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Product::count() == 0) {
            $this->call(ProductSeeder::class);
        }

        $products = Product::all();

        foreach ($products as $product) {
            ProductImage::factory(3)->create([
                'product_id' => $product->id,
            ]);
        }
    }
}
