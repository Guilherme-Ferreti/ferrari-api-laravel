<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Person;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Resources\ContactResource;

class ContactController extends Controller
{
    public function index()
    {
        $this->authorize('view', Contact::class);

        return ContactResource::collection(Contact::all());
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|string|email|max:255',
           'message' => 'required|string|max:65000', 
        ]);

        $user = User::firstWhere('email', $attributes['email']);

        if (! $user) {
            $contact = Contact::firstWhere('email', $attributes['email']);

            if (! $contact) {
                $person = Person::create([
                    'name' => $attributes['name'],
                ]);

                $attributes['person_id'] = $person->id;
            } else {
                $attributes['person_id'] = $contact->person_id;
            }
        } else {
            $attributes['person_id'] = $user->person_id;
        }

        $contact = Contact::create($attributes);

        return $this->respondCreated(new ContactResource($contact));
    }
}
