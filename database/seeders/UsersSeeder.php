<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun ADMIN
        User::create([
            'username' => 'admin',
            'password' => Hash::make('password123'), // Password default
            'nama_lengkap' => 'Administrator Utama',
            'email' => 'admin@example.com',
            'no_telepon' => '081234567890',
            'alamat' => 'Jl. Admin Pusat No. 1, Jakarta',
            'role' => 'admin',
            'foto_profil' => 'default_admin.png',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Akun USER BIASA
        User::create([
            'username' => 'user1',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'no_telepon' => '08987654321',
            'alamat' => 'Jl. Warga Biasa No. 10, Bandung',
            'role' => 'user', // Role user
            'is_active' => true,
            'last_login' => Carbon::now()->subDays(1), // Login terakhir kemarin
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Akun USER NON-AKTIF (Untuk test gagal login)
        User::create([
            'username' => 'banned_user',
            'password' => Hash::make('password123'),
            'nama_lengkap' => 'User Bermasalah',
            'email' => 'banned@example.com',
            'role' => 'user',
            'is_active' => false, // Akun ini tidak aktif
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. (Opsional) Generate 10 User Random (Jika butuh banyak data)
        // Pastikan Anda sudah setup UserFactory jika ingin uncomment ini
        // \App\Models\User::factory(10)->create();
    }
}
