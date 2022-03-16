<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Person;
use App\Models\PaymentSituation;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PaymentSituationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_all_payment_situations_are_retrieved()
    {
        PaymentSituation::factory(5)->create();

        $count = PaymentSituation::count();

        $this->getJson(route('payment_situations.index'))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->has($count)
                    ->first(fn (AssertableJson $json) =>
                        $json->hasAll('id', 'name', 'createdAt', 'updatedAt')
                    )
            );
    }

    public function test_a_payment_situation_can_be_retrieved()
    {
        $paymentSituation = PaymentSituation::factory()->create();

        $this->getJson(route('payment_situations.show', $paymentSituation))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('id', $paymentSituation->id)
                    ->where('name', $paymentSituation->name)
                    ->hasAll('createdAt', 'updatedAt')
            );
    }

    public function test_a_payment_situation_can_be_created()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();

        $payload = ['name' => 'Payment refused'];

        $route = route('payment_situations.store');

        $this->assertAdminsOnly($route);

        $this->actingAs($admin)->postJson($route, $payload)->assertCreated();

        $this->assertDatabaseHas(PaymentSituation::class, $payload);
    }

    public function test_a_payment_situation_can_be_updated()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $paymentSituation = PaymentSituation::factory()->create();

        $payload = ['name' => 'Payment refused'];

        $route = route('payment_situations.update', $paymentSituation);

        $this->assertAdminsOnly($route);

        $this->actingAs($admin)->putJson($route, $payload)->assertOk();

        $this->assertDatabaseHas(PaymentSituation::class, $payload);
    }

    public function test_a_payment_situation_can_be_deleted()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $paymentSituation = PaymentSituation::factory()->create();

        $route = route('payment_situations.destroy', $paymentSituation);

        $this->assertAdminsOnly($route);

        $this->actingAs($admin)->delete($route)->assertNoContent();

        $this->assertSoftDeleted($paymentSituation);
    }

    public function test_a_payment_situation_can_be_restored()
    {
        $admin = User::factory()->admin()->for(Person::factory())->create();
        $paymentSituation = PaymentSituation::factory()->create();

        $paymentSituation->delete();

        $route = route('payment_situations.restore', $paymentSituation);

        $this->assertAdminsOnly($route);

        $this->actingAs($admin)->postJson($route)->assertOk();

        $this->assertNotSoftDeleted(PaymentSituation::class, [
            '_id' => $paymentSituation->id,
        ]);
    }
}
