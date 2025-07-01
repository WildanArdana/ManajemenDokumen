<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     * Membuat tabel 'site_documents' (dokumen yang diupload engineer per site).
     */
    public function up(): void
    {
        Schema::create('site_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade'); // Engineer yang upload
            $table->timestamp('uploaded_at')->useCurrent(); // Tanggal dan waktu upload
            $table->timestamps();
        });
    }

    /**
     * Batalkan migrasi.
     * Menghapus tabel 'site_documents'.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_documents');
    }
};