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
        $this->authorize('viewAny', Contact::class);

        return ContactResource::collection(Contact::all());
    }

    public function myContacts()
    {
        $contacts = Contact::where('person_id', auth()->user()->person_id)->get();

        return ContactResource::collection($contacts);
    }

    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        $contact->load('person');

        return new ContactResource($contact);
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
