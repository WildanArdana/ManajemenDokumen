<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan aplikasi database seeds.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
        $this->call(DocumentSeeder::class); // Jika Anda punya DocumentSeeder
        $this->call(ProjectSeeder::class); // <--- TAMBAHKAN BARIS INI
    }
}