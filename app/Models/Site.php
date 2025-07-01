<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function getProgressPercentageAttribute()
    {
        $requiredDocumentsCount = Document::count();
        if ($requiredDocumentsCount === 0) {
            return 0;
        }
        $uploadedDocumentsCount = $this->siteDocuments->unique('document_id')->count();
        return round(($uploadedDocumentsCount / $requiredDocumentsCount) * 100);
    }
}