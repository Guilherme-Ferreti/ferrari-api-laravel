<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimeOptionPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function delete(User $user)
    {
        return $user->isAdmin();
    }

    public function restore(User $user)
    {
        return $user->isAdmin();
    }
}
