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
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        User::updateOrCreate(
            ['email' => 'superadmin@admin.com'], // Cek berdasarkan email agar tidak duplikat
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'role' => 'admin',
                'password' => Hash::make('password'), // Silakan ganti password default ini
                'email_verified_at' => now(),
            ]
        );
    }
}
