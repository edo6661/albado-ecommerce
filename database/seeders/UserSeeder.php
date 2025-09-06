<?php
namespace Database\Seeders;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Administrator',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => UserRole::ADMIN,
            ]
        );
        $customers = [
            ['name' => 'Budi Santoso',     'email' => 'budi@example.com'],
            ['name' => 'Siti Rahmawati',   'email' => 'siti@example.com'],
            ['name' => 'Agus Pratama',     'email' => 'agus@example.com'],
            ['name' => 'Dewi Lestari',     'email' => 'dewi@example.com'],
            ['name' => 'Jamaluddin',       'email' => 'jamal@example.com'],
            ['name' => 'Rina Kartika',     'email' => 'rina@example.com'],
            ['name' => 'Andi Wijaya',      'email' => 'andi@example.com'],
            ['name' => 'Nabila Putri',     'email' => 'nabila@example.com'],
        ];
        foreach ($customers as $c) {
            User::updateOrCreate(
                ['email' => $c['email']],
                [
                    'name' => $c['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('password'),
                    'role' => UserRole::USER,
                ]
            );
        }
    }
}
