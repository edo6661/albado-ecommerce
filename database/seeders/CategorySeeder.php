<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $realCategories = [
            [
                'name' => 'Elektronik',
                'image' => 'https://aseranishi.com/wp-content/uploads/2023/11/rekomendasi-jenis-alat-elektronik-rumah-tangga.jpg',
            ],
            [
                'name' => 'Pakaian Pria',
                'image' => 'https://ryusei.co.id/cdn/shop/articles/20210526-162649-0000-fed8da28f424252529171158ad96a3c9-2ca914993e09475498519a2e2901fc08_600x400_553bcf31-3b4f-4501-a443-5cd0c08614e3.png?v=1644919539&width=2048',
            ],
            [
                'name' => 'Pakaian Wanita',
                'image' => 'https://travistory.com/wp-content/uploads/2018/10/fashion-wanita.jpg',
            ],
            [
                'name' => 'Buku & Alat Tulis',
                'image' => 'https://cdn.linkumkm.id/uploads/library/5/4/9/3/8/54938_840x576.jpg',
            ],
            [
                'name' => 'Kesehatan & Kecantikan',
                'image' => 'https://d1vbn70lmn1nqe.cloudfront.net/prod/wp-content/uploads/2021/06/20073455/kecantikan.jpg.webp',
            ],
            [
                'name' => 'Peralatan Rumah',
                'image' => 'https://www.intiland.com/wp-content/uploads/2024/04/Alat-Elektronik-Rumah-Tangga-1.jpg',
            ],
            [
                'name' => 'Olahraga & Outdoor',
                'image' => 'https://i0.wp.com/rsum.bandaacehkota.go.id/wp-content/uploads/2025/02/lari.webp?fit=1279%2C853&ssl=1',
            ],
        ];

        foreach ($realCategories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name' => $category['name'],
                    'image' => $category['image'],
                ]
            );
        }
    }
}
