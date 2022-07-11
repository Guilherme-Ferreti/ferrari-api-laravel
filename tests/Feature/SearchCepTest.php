<?php

namespace Tests\Feature;

use App\Models\Person;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SearchCepTest extends TestCase
{
    public function test_a_cep_can_be_searched()
    {
        $user = User::factory()->for(Person::factory())->create();

        Http::fake();

        $this->actingAs($user)
            ->getJson(route('addresses.search_cep', ['cep' => '15370496']))
            ->assertOk();

        Http::assertSent(fn (Request $request) =>
            $request->url() === 'https://viacep.com.br/ws/15370496/json/'
        );
    }
}
