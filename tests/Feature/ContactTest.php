<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Person;
use App\Models\Contact;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ContactTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_contact_can_be_created()
    {
        $payload = [
            'name' => 'Joseph Doe',
            'email' => 'josephdoe@gmail.com',
            'message' => 'Hi there!',
        ];

        $this->postJson(route('contacts.store'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas(Contact::class, [
            'email' => 'josephdoe@gmail.com',
            'message' => 'Hi there!',
        ]);

        $this->assertDatabaseCount(Person::class, 1);
        $this->assertDatabaseHas(Person::class, [
            'name' => 'Joseph Doe',
        ]);
    }

    public function test_creating_a_contact_with_an_existing_user_uses_that_user_person_id()
    {
        $user = User::factory()->for(Person::factory())->create();

        $payload = [
            'name' => 'Joseph Doe',
            'email' => $user->email,
            'message' => 'Hi there!',
        ];

        $this->postJson(route('contacts.store'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas(Contact::class, [
            'email' => $user->email,
            'message' => 'Hi there!',
            'person_id' => $user->person_id,
        ]);

        $this->assertDatabaseCount(Person::class, 1);
        $this->assertDatabaseHas(Person::class, [
            'name' => $user->person->name,
        ]);
    }

    public function test_creating_two_contacts_with_same_email_creates_only_one_person()
    {
        $payload = [
            'name' => 'Joseph Doe',
            'email' => 'josephdoe@gmail.com',
            'message' => 'Hi there!',
        ];

        $this->postJson(route('contacts.store'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas(Contact::class, [
            'email' => 'josephdoe@gmail.com',
            'message' => 'Hi there!',
        ]);

        $this->assertDatabaseCount(Person::class, 1);
        $this->assertDatabaseHas(Person::class, [
            'name' => 'Joseph Doe',
        ]);

        $payload = [
            'name' => 'Joseph Doe',
            'email' => 'josephdoe@gmail.com',
            'message' => 'Hello again!',
        ];

        $this->postJson(route('contacts.store'), $payload)
            ->assertCreated();

        $this->assertDatabaseHas(Contact::class, [
            'email' => 'josephdoe@gmail.com',
            'message' => 'Hello again!',
        ]);

        $this->assertDatabaseCount(Person::class, 1);
        $this->assertDatabaseHas(Person::class, [
            'name' => 'Joseph Doe',
        ]);
    }
}