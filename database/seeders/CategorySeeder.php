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
        $categories = [
            'Elektronik', 'Pakaian Pria', 'Pakaian Wanita', 'Buku & Alat Tulis',
            'Kesehatan & Kecantikan', 'Rumah Tangga', 'Olahraga & Outdoor',
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => Str::slug($category)],
                [
                    'name' => $category,
                    'image' => 'https://via.placeholder.com/150?text=' . Str::slug($category, ' '),
                    ]
            );
        }
    }
}
