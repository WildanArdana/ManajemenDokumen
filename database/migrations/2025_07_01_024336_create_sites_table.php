<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Membuat tabel 'sites' dengan foreign key 'sub_system_id'.
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_system_id')->constrained()->onDelete('cascade'); // Foreign key ke tabel sub_systems
            $table->string('name');
            $table->text('address')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     * Menghapus tabel 'sites'.
     */
    public function down(): void
    {
        Schema::dropIfExists('sites');
    }
};