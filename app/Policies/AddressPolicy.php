<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AddressPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Address $address)
    {
        return $user->person_id == $address->person_id;
    }

    public function update(User $user, Address $address)
    {
        return $user->person_id == $address->person_id;
    }

    public function delete(User $user, Address $address)
    {
        return $user->person_id == $address->person_id;
    }
}
