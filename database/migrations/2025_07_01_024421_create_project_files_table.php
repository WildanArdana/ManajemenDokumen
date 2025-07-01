<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Membuat tabel 'project_files' (file umum project).
     */
    public function up(): void
    {
        Schema::create('project_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Admin yang upload
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     * Menghapus tabel 'project_files'.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_files');
    }
};