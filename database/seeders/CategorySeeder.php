<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'  => 'Palm Sugar (Arenga)',
                'image' => 'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777164/cylinder_pjxrc9.png',
            ],
            [
                'name'  => 'Coconut Sugar',
                'image' => 'https://res.cloudinary.com/dbxzxfyw3/image/upload/v1756777166/moulded-sugar-coconut-1_ivoyrm.png',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'image' => $cat['image']]
            );
        }
    }
}
