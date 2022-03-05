<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use DatabaseMigrations;

    public function test_a_user_can_edit_its_profile()
    {
        $user = User::factory()->for(Person::factory())->create();

        $payload = [
            'email'     => 'new_email@gmail.com',
            'name'      => 'John Francis Doe',
            'birthAt'   => '1990-01-01',
            'phone'     => '5511985472265',
            'document'  => '81236821459',
        ];

        $this->actingAs($user, 'api')
            ->putJson(route('auth.profile.update'), $payload)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json
                    ->where('_id', $user->id)
                    ->where('email', $payload['email'])
                    ->hasAll('photo', 'createdAt', 'updatedAt')
                    ->has('person', fn (AssertableJson $json) => 
                        $json
                            ->where('_id', $user->person->id)
                            ->where('name', $payload['name'])
                            ->where('birthAt', $payload['birthAt'])
                            ->where('phone', $payload['phone'])
                            ->where('document', $payload['document'])
                            ->hasAll('createdAt', 'updatedAt')
                    )
                    ->etc()
            );
    }

    public function test_a_user_can_upload_a_photo()
    {
        $user = User::factory()->for(Person::factory())->create();
        
        Storage::fake('public');

        $payload = [
            'photo' => UploadedFile::fake()->image('photo.jpg'),
        ];

        $this->actingAs($user, 'api')
            ->post(route('auth.profile.upload_photo'), $payload)
            ->assertOk();

        $this->assertDatabaseHas(User::class, [
            '_id' => $user->_id,
            'photo' => $payload['photo']->hashName('photos/'),
        ]);

        Storage::disk('public')->assertExists($payload['photo']->hashName('photos/'));
    }
}
