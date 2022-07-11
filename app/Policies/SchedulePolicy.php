<?php

namespace App\Policies;

use App\Models\Schedule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

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

    public function markAsCompleted(User $user, Schedule $schedule)
    {
        if ($schedule->isCompleted()) {
            return Response::deny(__('Schedule already completed.'));
        }

        return $user->isAdmin();
    }
}
