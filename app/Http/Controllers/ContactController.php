<?php

namespace App\Http\Controllers;

use App\Actions\Contact\StoreContact;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Request;

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

    public function store(Request $request, StoreContact $storeContact)
    {
        $attributes = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|string|email|max:255',
            'message' => 'required|string|max:65000',
        ]);

        $contact = $storeContact($attributes);

        return $this->respondCreated(new ContactResource($contact));
    }

    public function destroy(Contact $contact)
    {
        $this->authorize('delete', $contact);

        $contact->delete();

        return $this->respondNoContent();
    }
}
