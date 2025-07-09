<?php

namespace Database\Seeders;

use App\Models\Rating;
use App\Models\RatingImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RatingImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        if (Rating::count() == 0) {
            $this->command->warn('No ratings found. Please run RatingSeeder first.');
            return;
        }
        
        $ratings = Rating::all();

        $this->command->getOutput()->progressStart($ratings->count());

        foreach ($ratings as $rating) {
            
            if (fake()->boolean(30)) {
                $imageCount = rand(1, 3); 

                for ($i = 1; $i <= $imageCount; $i++) {
                    RatingImage::factory()->create([
                        'rating_id' => $rating->id,
                        'order' => $i,
                    ]);
                }
            }
            $this->command->getOutput()->progressAdvance();
        }

        $this->command->getOutput()->progressFinish();
    }
}