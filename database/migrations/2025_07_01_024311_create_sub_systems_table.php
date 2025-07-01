<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Membuat tabel 'sub_systems'.
     */
    public function up(): void
    {
        Schema::create('sub_systems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel projects
            $table->string('name'); // Nama Sub System, e.g., "SS#1 MAMUJU - MAMASA"
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     * Menghapus tabel 'sub_systems'.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_systems');
    }
};