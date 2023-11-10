<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Meter;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class MeterTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_get_meters_list_unauthorized(): void
    {
        $response = $this
            ->getJson('/api/meter');

        $response->assertUnauthorized();
    }

    public function test_getting_meters_empty_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this
            ->getJson('/api/meter');

        $response
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_getting_meters_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meters = Meter::factory(2)
            ->recycle($user)
            ->create();

        $response = $this
            ->getJson('/api/meter');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => $meters->map(fn(Meter $meter) => [
                    'id' => $meter->id,
                    'name' => $meter->name,
                    'user_id' => $meter->user_id,
                ])->toArray()
            ]);
    }

    public function test_cant_see_someone_else_meters_on_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()->create();

        $response = $this
            ->getJson('/api/meter');

        $response
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_creating_new_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);


        $response = $this
            ->postJson('/api/meter', [
                'name' => 'Test Meter'
            ]);

        $meter = Meter::first();

        $response
            ->assertCreated()
            ->assertExactJson([
                'data' => [
                    'id' => $meter->id,
                    'name' => 'Test Meter',
                    'user_id' => $meter->user_id,
                ]
            ]);
    }

    public function test_validation_when_creating_new_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this
            ->postJson('/api/meter', []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('name');
    }

    public function test_getting_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->getJson('/api/meter/'.$meter->id);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $meter->id,
                    'name' => $meter->name,
                    'user_id' => $meter->user_id,
                ]
            ]);
    }

    public function test_cant_get_someone_else_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->create();


        $response = $this
            ->getJson('/api/meter/'.$meter->id);

        $response
            ->assertForbidden();
    }

    public function test_cant_get_non_existing_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this
            ->getJson('/api/meter/123456');

        $response
            ->assertNotFound();
    }

    public function test_updating_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/meter/'.$meter->id, [
                'name' => 'New Name'
            ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $meter->id,
                    'name' => 'New Name',
                    'user_id' => $meter->user_id,
                ]
            ]);
    }

    public function test_nothing_happens_when_updating_meter_without_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/meter/'.$meter->id, [

            ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $meter->id,
                    'name' => $meter->name,
                    'user_id' => $meter->user_id,
                ]
            ]);
    }

    public function test_validation_when_updating_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/meter/'.$meter->id, [
                'name' => ''
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('name');
    }

    public function test_cant_update_someone_else_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->create();

        $response = $this
            ->putJson('/api/meter/'.$meter->id, [
                'name' => 'New Name'
            ]);

        $response
            ->assertForbidden();
    }

    public function test_destroy_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->deleteJson('/api/meter/'.$meter->id);

        $response
            ->assertNoContent();

        $this->assertDatabaseMissing('meters', [
            'id' => $meter->id,
        ]);
    }

    public function test_cant_destroy_someone_else_meter(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $meter = Meter::factory()
            ->create();

        $response = $this
            ->deleteJson('/api/meter/'.$meter->id);

        $response
            ->assertForbidden();
    }
}
