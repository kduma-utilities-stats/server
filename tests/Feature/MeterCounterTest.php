<?php

namespace Tests\Feature;

use App\Models\Meter;
use App\Models\Value;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Counter;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class MeterCounterTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_get_counters_list_unauthorized(): void
    {
        $meter = Meter::factory()
            ->create();

        $response = $this
            ->getJson('/api/meter/'.$meter->prefixed_id.'/counter');

        $response->assertUnauthorized();
    }

    public function test_getting_counters_empty_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->getJson('/api/meter/'.$meter->prefixed_id.'/counter');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [],
            ]);
    }

    public function test_cant_get_someone_else_counters_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->create();

        $response = $this
            ->getJson('/api/meter/'.$meter->prefixed_id.'/counter');

        $response
            ->assertForbidden();
    }

    public function test_getting_counters_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $counters = Counter::factory(2)
            ->recycle($meter)
            ->create();

        $response = $this
            ->getJson('/api/meter/'.$meter->prefixed_id.'/counter');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => $counters->map(fn(Counter $counter) => [
                    'id' => $counter->prefixed_id,
                    'name' => $counter->name,
                    'barcode' => $counter->barcode,
                    'meter_id' => $counter->meter->prefixed_id,
                ])->toArray()
            ]);
    }

    public function test_cant_see_someone_else_counters_on_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $counter = Counter::factory()->create();

        $response = $this
            ->getJson('/api/meter/'.$meter->prefixed_id.'/counter');

        $response
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_creating_new_counter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->postJson('/api/meter/'.$meter->prefixed_id.'/counter', [
                'name' => 'Test Counter',
                'barcode' => '1234',
            ]);

        $counter = Counter::first();

        $response
            ->assertCreated()
            ->assertExactJson(['data' => [
                'id' => $counter->prefixed_id,
                'name' => 'Test Counter',
                'barcode' => '1234',
                'meter_id' => $counter->meter->prefixed_id,
            ]]);
    }

//    public function test_validation_when_creating_new_counter(): void
//    {
//        $user = User::factory()->create();
//        Sanctum::actingAs($user);
//
//        $meter = Meter::factory()
//            ->recycle($user)
//            ->create();
//
//        $response = $this
//            ->postJson('/api/meter/'.$meter->prefixed_id.'/counter', [
//                'name' => '',
//                'barcode' => '',
//            ]);
//
//        $response
//            ->assertUnprocessable()
//            ->assertJsonValidationErrorFor('name');
//    }

    public function test_getting_counter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $counter = Counter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->getJson('/api/counter/'.$counter->prefixed_id);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $counter->prefixed_id,
                    'name' => $counter->name,
                    'barcode' => $counter->barcode,
                    'meter_id' => $counter->meter->prefixed_id,
                ]
            ]);
    }

    public function test_cant_get_someone_else_counter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $counter = Counter::factory()
            ->create();

        $response = $this
            ->getJson('/api/counter/'.$counter->prefixed_id);

        $response
            ->assertForbidden();
    }

    public function test_cant_get_non_existing_counter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this
            ->getJson('/api/counter/123456');

        $response
            ->assertNotFound();
    }

    public function test_updating_counter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $counter = Counter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/counter/'.$counter->prefixed_id, [
                'name' => 'New Name'
            ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $counter->prefixed_id,
                    'name' => 'New Name',
                    'barcode' => $counter->barcode,
                    'meter_id' => $counter->meter->prefixed_id,
                ]
            ]);
    }

    public function test_nothing_happens_when_updating_counter_without_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $counter = Counter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/counter/'.$counter->prefixed_id, [

            ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $counter->prefixed_id,
                    'name' => $counter->name,
                    'barcode' => $counter->barcode,
                    'meter_id' => $counter->meter->prefixed_id,
                ]
            ]);
    }

//    public function test_validation_when_updating_counter(): void
//    {
//        $user = User::factory()->create();
//        Sanctum::actingAs($user);
//
//        $counter = Counter::factory()
//            ->recycle($user)
//            ->create();
//
//        $response = $this
//            ->putJson('/api/counter/'.$counter->prefixed_id, [
//                'name' => '',
//                'barcode' => ''
//            ]);
//
//        $response
//            ->assertUnprocessable()
//            ->assertJsonValidationErrorFor('name');
//    }

    public function test_cant_update_someone_else_counter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $counter = Counter::factory()
            ->create();

        $response = $this
            ->putJson('/api/counter/'.$counter->prefixed_id, [
                'name' => 'New Name'
            ]);

        $response
            ->assertForbidden();
    }

    public function test_destroy_counter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $counter = Counter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->deleteJson('/api/counter/'.$counter->prefixed_id);

        $response
            ->assertNoContent();

        $this->assertDatabaseMissing('counters', [
            'prefixed_id' => $counter->prefixed_id,
        ]);
    }

    public function test_cant_destroy_counter_with_values(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $counter = Counter::factory()
            ->recycle($user)
            ->create();

        Value::factory()
            ->recycle($counter)
            ->create();

        $response = $this
            ->deleteJson('/api/counter/'.$counter->prefixed_id);

        $response
            ->assertNotAcceptable();

        $this->assertDatabaseHas('counters', [
            'prefixed_id' => $counter->prefixed_id,
        ]);
    }

    public function test_cant_destroy_someone_else_counter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $counter = Counter::factory()
            ->create();

        $response = $this
            ->deleteJson('/api/counter/'.$counter->prefixed_id);

        $response
            ->assertForbidden();
    }
}
