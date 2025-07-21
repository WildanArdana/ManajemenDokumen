<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use App\Models\SiteDocument;
use Illuminate\Auth\Access\Response;

class SitePolicy
{
    /**
     * Menentukan apakah user dapat mengelola site (membuat, memperbarui, menghapus).
     * Hanya admin yang bisa.
     */
    public function manage(User $user): bool
    {
        return $user->isAdmin(); // Ini harus mengembalikan true jika user adalah admin
    }

    /**
     * Menentukan apakah pengguna dapat melihat site.
     */
    public function view(User $user, Site $site): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $site->subSystem->project->assignedUsers->contains($user->id);
    }

    /**
     * Menentukan apakah pengguna dapat melakukan tindakan umum pada site (upload/komen).
     */
    public function performActions(User $user, Site $site): bool
    {
        // Admin selalu bisa
        if ($user->isAdmin()) {
            return true;
        }

        // Engineer hanya jika dia ditugaskan ke project dari site ini
        if ($user->isEngineer()) {
            return $site->subSystem->project->assignedUsers->contains($user->id);
        }

        return false;
    }

    /**
     * Menentukan apakah pengguna dapat mengunggah dokumen untuk site.
     */
    public function uploadDocument(User $user, Site $site): bool
    {
        return $this->performActions($user, $site);
    }

    /**
     * Menentukan apakah pengguna dapat menambahkan komentar pada site.
     */
    public function addComment(User $user, Site $site): bool
    {
        return $this->performActions($user, $site);
    }

    /**
     * Menentukan apakah pengguna dapat menghapus dokumen site.
     */
    public function deleteDocument(User $user, Site $site, SiteDocument $siteDocument): bool
    {
        // Admin bisa menghapus dokumen apapun
        if ($user->isAdmin()) {
            return true;
        }
        // Engineer yang mengunggah dokumen bisa menghapusnya
        if ($user->id === $siteDocument->uploaded_by) {
            return true;
        }
        return false;
    }
}
