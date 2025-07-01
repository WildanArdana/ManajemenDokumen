<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Membuat tabel 'project_user' untuk relasi many-to-many antara projects dan users.
     */
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Opsional: Untuk mencegah duplikasi penugasan
            $table->unique(['project_id', 'user_id']);
        });
    }

    /**
     * Batalkan migrasi.
     * Menghapus tabel 'project_user'.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};