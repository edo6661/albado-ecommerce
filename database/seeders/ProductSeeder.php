<?php
namespace Database\Seeders;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class ProductSeeder extends Seeder
{
    public function run(): void
    {
        if (Category::count() === 0) {
            $this->call(CategorySeeder::class);
        }
        $palmId    = Category::where('slug', Str::slug('Palm Sugar (Arenga)'))->value('id');
        $coconutId = Category::where('slug', Str::slug('Coconut Sugar'))->value('id');
        $products = [
            [
                'category_id'    => $palmId,
                'name'           => 'Cylinder Palm Sugar',
                'slug'           => Str::slug('Cylinder Palm Sugar'),
                'description'    => 'Traditional Indonesian solid palm sugar formed through a drying and molding process. Dense texture with caramel-like sweetness.',
                'price'          => 55000,
                'discount_price' => 49000,   
                'stock'          => 120,
                'is_active'      => true,
            ],
            [
                'category_id'    => $palmId,
                'name'           => 'Liquid Palm Sugar',
                'slug'           => Str::slug('Liquid Palm Sugar'),
                'description'    => 'Premium liquid palm sugar (syrup-like) from high-quality Arenga sap; natural sweetness and distinctive aroma.',
                'price'          => 900000,
                'discount_price' => 850000,  
                'stock'          => 30,
                'is_active'      => true,
            ],
            [
                'category_id'    => $palmId,
                'name'           => 'Powder Palm Sugar',
                'slug'           => Str::slug('Powder Palm Sugar'),
                'description'    => 'Finely ground palm sugar; smooth texture, dissolves easily; lower GI vs refined sugar.',
                'price'          => 60000,
                'discount_price' => 54000,   
                'stock'          => 150,
                'is_active'      => true,
            ],
            [
                'category_id'    => $coconutId,
                'name'           => 'Coconut Sugar Moulded',
                'slug'           => Str::slug('Coconut Sugar Moulded'),
                'description'    => 'Moulded coconut sugar with sweet-savory caramel taste and brownish hue. Ideal for everyday cooking.',
                'price'          => 50000,
                'discount_price' => 45500,   
                'stock'          => 90,
                'is_active'      => true,
            ],
            [
                'category_id'    => $coconutId,
                'name'           => 'Coconut Sugar Powder',
                'slug'           => Str::slug('Coconut Sugar Powder'),
                'description'    => 'Finely milled coconut sugar; smooth & easily soluble; natural sweet caramel taste.',
                'price'          => 65000,
                'discount_price' => 58500,   
                'stock'          => 140,
                'is_active'      => true,
            ],
        ];
        foreach ($products as $p) {
            if (!is_null($p['discount_price']) && $p['discount_price'] >= $p['price']) {
                $p['discount_price'] = null;
            }
            Product::updateOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }
    }
}
