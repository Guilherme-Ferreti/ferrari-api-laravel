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

            $this->assertDatabaseHas(Person::class, [
                '_id'       => $user->person->_id,
                'name'      => $payload['name'],
                'birth_at'  => $payload['birthAt'],
                'phone'     => $payload['phone'],
                'document'  => $payload['document'],
            ]);
    
            $this->assertDatabaseHas(User::class, [
                '_id'   => $user->_id,
                'email' => $payload['email'],
            ]);
    }

    public function test_a_user_can_upload_a_photo()
    {
        Storage::fake('public');

        $newPhoto = UploadedFile::fake()->image('new_photo.jpg');
        $oldPhoto = UploadedFile::fake()->image('old_photo.jpg');
        
        $user = User::factory()->for(Person::factory())->create([
            'photo' => $oldPhoto->hashName('photos/'),
        ]);

        $this->actingAs($user, 'api')
            ->post(route('auth.profile.upload_photo'), ['photo' => $newPhoto])
            ->assertOk();

        $this->assertDatabaseHas(User::class, [
            '_id' => $user->_id,
            'photo' => $newPhoto->hashName('photos/'),
        ]);

        Storage::disk('public')->assertExists($newPhoto->hashName('photos/'));
        Storage::disk('public')->assertMissing($oldPhoto->hashName('photos/'));
    }

    public function test_a_user_can_delete_its_photo()
    {
        Storage::fake('public');
        
        $photo = UploadedFile::fake()->image('photo.jpg');

        Storage::disk('public')->put('/photos', $photo);
        
        $user = User::factory()->for(Person::factory())->create([
            'photo' => $photo->hashName('photos/'),
        ]);

        $this->actingAs($user, 'api')
            ->delete(route('auth.profile.delete_photo'))
            ->assertOk();

        $this->assertDatabaseHas(User::class, [
            '_id' => $user->_id,
            'photo' => null,
        ]);

        Storage::disk('public')->assertMissing($photo->hashName('photos/'));
    }
}
