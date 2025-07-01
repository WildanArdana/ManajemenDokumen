<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SitePolicy
{
    public function view(User $user, Site $site): bool
    {
        return true;
    }

    public function performActions(User $user, Site $site): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isEngineer()) {
            return $site->subSystem->project->assignedUsers->contains($user->id);
        }

        return false;
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }

    public function uploadDocument(User $user, Site $site): bool
    {
        return $this->performActions($user, $site);
    }

    public function addComment(User $user, Site $site): bool
    {
        return $this->performActions($user, $site);
    }

    public function deleteDocument(User $user, Site $site, \App\Models\SiteDocument $siteDocument): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->id === $siteDocument->uploaded_by) {
            return true;
        }
        return false;
    }
}