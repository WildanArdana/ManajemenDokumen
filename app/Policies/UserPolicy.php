<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    public function updateDashboardInfo(User $user): Response
    {
        return $user->isAdmin()
                    ? Response::allow()
                    : Response::deny('Anda tidak memiliki izin untuk memperbarui informasi dashboard.');
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id && !(User::where('role', 'admin')->count() <= 1 && $model->isAdmin());
    }
}