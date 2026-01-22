<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'id' => 1, // Kita paksa ID 1 agar cocok dengan fallback controller
            'full_name' => 'Super Admin',
            'username' => 'admin',
            'email' => 'admin@mathery.com',
            'password' => Hash::make('password123'), // Jangan lupa di-hash
            'role' => 'admin',
        ]);
    }
}
