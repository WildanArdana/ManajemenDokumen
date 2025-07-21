<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Site extends Model
{
    use HasFactory;

    protected $fillable = ['sub_system_id', 'name', 'address'];

    public function subSystem()
    {
        return $this->belongsTo(SubSystem::class);
    }

    public function siteDocuments()
    {
        return $this->hasMany(SiteDocument::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Accessor untuk menghitung persentase upload dokumen di Site ini.
     * Dihitung berdasarkan jumlah dokumen unik yang diupload dibandingkan total jenis dokumen yang dibutuhkan.
     */
    public function getProgressPercentageAttribute()
    {
        // Ini akan menghitung dari SEMUA jenis dokumen di tabel 'documents'
        $requiredDocumentsCount = Document::count();
        if ($requiredDocumentsCount === 0) {
            return 0;
        }
        $uploadedDocumentsCount = $this->siteDocuments->unique('document_id')->count();
        return round(($uploadedDocumentsCount / $requiredDocumentsCount) * 100);
    }

    /**
     * Mengecek apakah semua file wajib (BABT, Comm Test, UT) sudah diupload.
     */
    public function allRequiredFilesUploaded(): bool
    {
        // Ambil ID dari dokumen wajib
        $requiredDocumentIds = Document::whereIn('slug', ['babt', 'commissioning-test', 'uji-terima'])->pluck('id')->toArray();

        // Ambil ID dokumen yang sudah diupload untuk site ini
        $uploadedDocumentIds = $this->siteDocuments->pluck('document_id')->unique()->toArray();

        // Cek apakah semua dokumen wajib sudah ada di antara yang diupload
        foreach ($requiredDocumentIds as $docId) {
            if (!in_array($docId, $uploadedDocumentIds)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Mengecek status deadline site.
     * Mengembalikan 'ontime', 'overdue', atau 'no_deadline'.
     */
    public function getDeadlineStatusAttribute(): string
    {
        $deadline = $this->subSystem->project->end_date; // Ambil deadline dari project
        if (is_null($deadline)) {
            return 'no_deadline';
        }

        $deadline = Carbon::parse($deadline);
        // Terlambat jika saat ini sudah lewat deadline DAN belum semua file wajib diupload
        if (Carbon::now()->isAfter($deadline) && !$this->allRequiredFilesUploaded()) {
            return 'overdue';
        }
        return 'ontime'; // Tepat waktu atau belum lewat deadline
    }

    /**
     * Menghitung keterlambatan dalam hari jika sudah melewati deadline dan belum lengkap.
     */
    public function getDaysOverdueAttribute(): int
    {
        $deadline = $this->subSystem->project->end_date;
        // Hanya hitung keterlambatan jika deadline ada, sudah lewat, dan belum semua file wajib diupload
        if (!is_null($deadline) && Carbon::now()->isAfter($deadline) && !$this->allRequiredFilesUploaded()) {
            return Carbon::parse($deadline)->diffInDays(Carbon::now());
        }
        return 0; // Tidak terlambat
    }
}