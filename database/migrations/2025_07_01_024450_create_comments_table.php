<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Membuat tabel 'comments'.
     */
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->timestamps(); // created_at akan menjadi tanggal dan waktu komentar
        });
    }

    /**
     * Batalkan migrasi.
     * Menghapus tabel 'comments'.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};