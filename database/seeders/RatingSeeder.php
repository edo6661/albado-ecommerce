<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (User::count() == 0 || Product::count() == 0) {
            $this->command->warn('Please seed Users and Products first.');
            return;
        }

        $userIds = User::pluck('id');
        $productIds = Product::pluck('id');
        $combinations = [];

        foreach ($userIds as $userId) {
            foreach ($productIds as $productId) {
                $combinations[] = [
                    'user_id' => $userId,
                    'product_id' => $productId,
                ];
            }
        }

        $selectedCombinations = collect($combinations)->shuffle()->take(50);

        $this->command->getOutput()->progressStart($selectedCombinations->count());
        
        $selectedCombinations->each(function ($combination) {
            Rating::factory()->create($combination);
            $this->command->getOutput()->progressAdvance();
        });
        
        $this->command->getOutput()->progressFinish();
    }
}