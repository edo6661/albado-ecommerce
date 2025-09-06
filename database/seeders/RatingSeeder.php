<?php
namespace Database\Seeders;
use App\Models\Product;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Database\Seeder;
class RatingSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() === 0 || Product::count() === 0) {
            $this->command->warn('Please seed Users and Products first.');
            return;
        }
        $users = User::whereNotIn('email', ['admin@gmail.com'])->get();
        $products = Product::all();
        $reviews = [
            5 => [
                'Kualitasnya mantap, rasa karamelnya kerasa banget.',
                'Sangat rekomendasi! Aromanya natural dan bersih.',
                'Manisnya pas dan mudah larut saat diseduh.',
            ],
            4 => [
                'Bagus dan sesuai deskripsi. Packaging aman.',
                'Rasanya enak, cocok buat kopi dan dessert.',
                'Tekstur halus, repeat order lagi.',
            ],
            3 => [
                'Oke, tapi pengiriman agak lama.',
                'Rasa enak, cuma sedikit menggumpal.',
            ],
        ];
        Rating::query()->delete();
        foreach ($products as $product) {
            $count = rand(6, 12);
            $pickedUsers = $users->shuffle()->take($count);
            foreach ($pickedUsers as $user) {
                $star = [5,5,4,4,4,3][array_rand([5,5,4,4,4,3])]; 
                $text = $reviews[$star][array_rand($reviews[$star])];
                Rating::create([
                    'user_id'    => $user->id,
                    'product_id' => $product->id,
                    'rating'     => $star,
                    'review'     => $text,
                ]);
            }
        }
    }
}
