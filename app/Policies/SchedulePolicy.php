<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchedulePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }
    public function view(User $user, $schedule)
    {
        return $user->person_id == $schedule->person_id || $user->isAdmin();
    }
}
