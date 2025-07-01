<?php

namespace App\Models;

// Hapus atau komentari baris ini karena tidak digunakan lagi:
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable // <--- HAPUS "implements MustVerifyEmail" DI SINI
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            // Hapus atau komentari baris ini:
            // 'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEngineer()
    {
        return $this->role === 'engineer';
    }

    public function assignedProjects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function projectFiles()
    {
        return $this->hasMany(ProjectFile::class, 'uploaded_by');
    }

    public function siteDocuments()
    {
        return $this->hasMany(SiteDocument::class, 'uploaded_by');
    }
}