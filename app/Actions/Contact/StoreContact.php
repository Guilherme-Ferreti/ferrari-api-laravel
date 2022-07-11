<?php

namespace App\Actions\Contact;

use App\Models\Contact;
use App\Models\Person;
use App\Models\User;

class StoreContact
{
    public function __invoke(array $attributes): Contact
    {
        $attributes['person_id'] = $this->findPersonIdByEmail($attributes['email']);

        if (is_null($attributes['person_id'])) {
            $attributes['person_id'] = Person::create(['name' => $attributes['name']])->id;
        }

        return Contact::create($attributes);
    }

    private function findPersonIdByEmail(string $email): ?string
    {
        if ($user = User::firstWhere('email', $email)) {
            return $user->person_id;
        }

        if ($contact = Contact::firstWhere('email', $email)) {
            return $contact->person_id;
        }

        return null;
    }
}
