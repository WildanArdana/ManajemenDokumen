<?php

namespace App\Policies;

use App\Models\SubSystem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubSystemPolicy
{
    public function view(User $user, SubSystem $subSystem): bool
    {
        return true;
    }

    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }
}