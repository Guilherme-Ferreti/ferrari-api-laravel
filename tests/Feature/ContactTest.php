<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use DatabaseMigrations;

    public function test_all_contacts_are_retrieved()
    {
        Contact::factory(5)->for(Person::factory())->create();

        $admin = User::factory()->admin()->for(Person::factory())->create();

        $route = route('contacts.index');

        $this->assertAdminsOnly($route, 'get');

        $this->actingAs($admin)
            ->getJson($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has(5)
                    ->first(fn (AssertableJson $json) =>
                        $json->hasAll('id', 'email', 'message', 'personId', 'createdAt', 'updatedAt')
                    )
            );
    }

    public function test_all_user_contacts_are_retrieved()
    {
        Contact::factory(10)->for(Person::factory())->create();

        $user = User::factory()->for(Person::factory())->create();
        Contact::factory(5)->for($user->person)->create();

        $route = route('contacts.my_contacts');

        $this->assertAuthenticatedOnly($route, 'get');

        $this->actingAs($user)
            ->getJson($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has(5)
                    ->first(fn (AssertableJson $json) =>
                        $json->hasAll('id', 'email', 'message', 'personId', 'createdAt', 'updatedAt')
                    )
            );
    }

    public function test_a_contact_can_be_retrieved_by_its_owner()
    {
        $user = User::factory()->for(Person::factory())->create();
        $contact = Contact::factory()->for($user->person)->create();

        $route = route('contacts.show', $contact);

        $this->assertAuthenticatedOnly($route, 'get');

        $this->actingAs($user)
            ->getJson($route)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('id', $contact->id)
                    ->where('email', $contact->email)
                    ->where('message', $contact->message)
                    ->where('personId', $contact->person_id)
                    ->hasAll('person', 'createdAt', 'updatedAt')
                    ->etc()
            );
    }

    public function test_a_contact_can_be_retrieved_by_an_admin()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $user = User::factory()->for(Person::factory())->create();
        $contact = Contact::factory()->for($user->person)->create();

        $this->actingAs($admin)->getJson(route('contacts.show', $contact))->assertOk();
    }

    public function test_a_contact_can_be_created()
    {
        $payload = [
            'name' => 'Joseph Doe',
            'email' => 'josephdoe@gmail.com',
            'message' => 'Hi there!',
        ];

        $this->postJson(route('contacts.store'), $payload)->assertCreated();

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

    public function test_a_contact_can_be_deleted_by_its_owner()
    {
        $user = User::factory()->for(Person::factory())->create();
        $contact = Contact::factory()->for($user->person)->create();

        $route = route('contacts.destroy', $contact);

        $this->assertAuthenticatedOnly($route, 'delete');

        $this->actingAs($user)->deleteJson($route)->assertNoContent();

        $this->assertModelMissing($contact);
    }

    public function test_a_contact_can_be_deleted_by_an_admin()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $contact = Contact::factory()->for($admin->person)->create();

        $route = route('contacts.destroy', $contact);

        $this->assertAuthenticatedOnly($route, 'delete');

        $this->actingAs($admin)->deleteJson($route)->assertNoContent();

        $this->assertModelMissing($contact);
    }
}
