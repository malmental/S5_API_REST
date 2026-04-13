<?php

namespace App\Policies;

use App\Models\Incidence;
use App\Models\User;

class IncidencePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Incidence $incidence): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Incidence $incidence): bool
    {
        return $user->isAdmin() || (int) $incidence->user_id === (int) $user->id;
    }

    public function delete(User $user, Incidence $incidence): bool
    {
        return $user->isAdmin() || (int) $incidence->user_id === (int) $user->id;
    }
}
