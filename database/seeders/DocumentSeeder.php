<?php

namespace Database\Seeders; // INI HARUS PERSIS "Database\Seeders"

use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder // INI HARUS PERSIS "DocumentSeeder"
{
    /**
     * Jalankan database seeds.
     * Mengisi tabel 'documents' dengan jenis dokumen yang dibutuhkan.
     */
    public function run(): void
    {
        $documents = [
            'BABT',
            'Commissioning Test',
            'Uji Terima',
            'As Plan Drawing',
            'Serial Number',
            'Lampiran Foto',
            'Other',
        ];

        foreach ($documents as $doc) {
            Document::firstOrCreate(
                ['name' => $doc], // Kriteria pencarian
                ['slug' => Str::slug($doc)] // Data yang akan dibuat jika tidak ditemukan
            );
        }
    }
}