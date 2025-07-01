<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Membuat tabel 'documents' (master data jenis dokumen).
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // BABT, Commissioning Test, etc.
            $table->string('slug')->unique(); // babt, commissioning-test, etc.
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     * Menghapus tabel 'documents'.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};