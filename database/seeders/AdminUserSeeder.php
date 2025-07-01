<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     * Membuat akun admin default.
     */
    public function run(): void
    {
        // Menggunakan firstOrCreate untuk membuat user hanya jika email belum ada.
        // Ini mencegah error "Duplicate entry" jika seeder dijalankan berkali-kali.
        User::firstOrCreate(
            ['email' => 'admin@example.com'], // Kriteria pencarian: cari user dengan email ini
            [
                'name' => 'Admin Project',
                'password' => Hash::make('password'), // Password default: 'password'
                'role' => 'admin', // Peran: admin
                'email_verified_at' => now(), // Tanggal verifikasi email (sesuai Breeze default)
            ]
        );
    }
}