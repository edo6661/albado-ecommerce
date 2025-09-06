<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        if (Product::count() === 0) {
            $this->call(ProductSeeder::class);
        }

         $map = [
            'cylinder-palm-sugar' => [
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777164/cylinder_pjxrc9.png',
            ],
            'liquid-palm-sugar' => [
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777165/liquid_hbkhyv.png',
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777165/liquid-palm-sugar-2-bg-removed_houpdr.png',
            ],
            'powder-palm-sugar' => [
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777164/powder_yi5uco.png',
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777164/powder-2_n7hcdl.png',
            ],
            'coconut-sugar-moulded' => [
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777166/moulded-sugar-coconut-1_ivoyrm.png',
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777166/moulded-sugar-coconut-2_ddsxad.png',
            ],
            'coconut-sugar-powder' => [
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777164/powder-sugar-coconut-1_fcvd4a.png',
                'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777164/powder-sugar-coconut-2_wh4dae.png',
            ],
        ];

        foreach ($map as $slug => $urls) {
            $product = Product::where('slug', $slug)->first();
            if (!$product) continue;

            
            $product->images()->delete();

            $order = 1;
            foreach ($urls as $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'path'      => $url,
                    'order'     => $order++,
                ]);
            }
        }
    }
}
