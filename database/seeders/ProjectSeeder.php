<?php

namespace Database\Seeders;

use App\Models\Project; // Pastikan ini ada
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon; // Untuk tanggal

class ProjectSeeder extends Seeder
{
    /**
     * Jalankan database seeds.
     * Mengisi tabel 'projects' dengan data dummy.
     */
    public function run(): void
    {
        Project::firstOrCreate(
            ['name' => 'Proyek Percontohan A'],
            [
                'description' => 'Ini adalah deskripsi untuk proyek percontohan A. Fokus pada pengembangan fitur inti.',
                'start_date' => Carbon::parse('2025-01-15'),
                'end_date' => Carbon::parse('2025-06-30'),
            ]
        );

        Project::firstOrCreate(
            ['name' => 'Proyek Inovasi B'],
            [
                'description' => 'Proyek ini bertujuan untuk memperkenalkan teknologi baru dan inovasi.',
                'start_date' => Carbon::parse('2025-03-01'),
                'end_date' => Carbon::parse('2025-09-15'),
            ]
        );

        Project::firstOrCreate(
            ['name' => 'Proyek Peningkatan Kualitas C'],
            [
                'description' => 'Fokus pada peningkatan kualitas produk dan pengurangan bug.',
                'start_date' => Carbon::parse('2025-05-10'),
                'end_date' => Carbon::parse('2025-12-31'),
            ]
        );

        Project::firstOrCreate(
            ['name' => 'Proyek Eksternal D'],
            [
                'description' => 'Proyek ini dikerjakan untuk klien eksternal dengan persyaratan khusus.',
                'start_date' => Carbon::parse('2025-06-01'),
                'end_date' => Carbon::parse('2026-01-31'),
            ]
        );
    }
}