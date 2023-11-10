<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\Reading;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Value;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ReadingValueTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_get_values_list_unauthorized(): void
    {
        $reading = Reading::factory()
            ->create();

        $response = $this
            ->getJson('/api/reading/'.$reading->id.'/value');

        $response->assertUnauthorized();
    }

    public function test_getting_values_empty_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->getJson('/api/reading/'.$reading->id.'/value');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [],
            ]);
    }

    public function test_cant_get_someone_else_values_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->create();

        $response = $this
            ->getJson('/api/reading/'.$reading->id.'/value');

        $response
            ->assertForbidden();
    }

    public function test_getting_values_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $values = Value::factory(2)
            ->recycle($reading)
            ->create();

        $response = $this
            ->getJson('/api/reading/'.$reading->id.'/value');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => $values->map(fn(Value $value) => [
                    'id' => $value->id,
                    'reading_id' => $value->reading_id,
                    'counter_id' => $value->counter_id,
                    'value' => $value->value,
                ])->toArray()
            ]);
    }

    public function test_cant_see_someone_else_values_on_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $value = Value::factory()->create();

        $response = $this
            ->getJson('/api/reading/'.$reading->id.'/value');

        $response
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_creating_new_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $counter = Counter::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->postJson('/api/reading/'.$reading->id.'/value', [
                'counter_id' => $counter->id,
                'value' => 1234,
            ]);

        $value = Value::first();

        $response
            ->assertCreated()
            ->assertExactJson(['data' => [
                'id' => $value->id,
                'counter_id' => $counter->id,
                'value' => 1234,
                'reading_id' => $value->reading_id,
            ]]);
    }

    public function test_validation_when_creating_new_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->postJson('/api/reading/'.$reading->id.'/value', [
                'counter_id' => '',
                'value' => '',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('counter_id')
            ->assertJsonValidationErrorFor('value');
    }

    public function test_getting_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $value = Value::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->getJson('/api/value/'.$value->id);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $value->id,
                    'counter_id' => $value->counter_id,
                    'value' => $value->value,
                    'reading_id' => $value->reading_id,
                ]
            ]);
    }

    public function test_cant_get_someone_else_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $value = Value::factory()
            ->create();

        $response = $this
            ->getJson('/api/value/'.$value->id);

        $response
            ->assertForbidden();
    }

    public function test_cant_get_non_existing_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this
            ->getJson('/api/value/123456');

        $response
            ->assertNotFound();
    }

    public function test_updating_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $value = Value::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/value/'.$value->id, [
                'value' => 666
            ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $value->id,
                    'counter_id' => $value->counter_id,
                    'value' => 666,
                    'reading_id' => $value->reading_id,
                ]
            ]);
    }

    public function test_nothing_happens_when_updating_value_without_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $value = Value::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/value/'.$value->id, [

            ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $value->id,
                    'counter_id' => $value->counter_id,
                    'value' => $value->value,
                    'reading_id' => $value->reading_id,
                ]
            ]);
    }

    public function test_validation_when_updating_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $value = Value::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/value/'.$value->id, [
                'value' => ''
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('value');
    }

    public function test_cant_update_someone_else_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $value = Value::factory()
            ->create();

        $response = $this
            ->putJson('/api/value/'.$value->id, [
                'value' => 222
            ]);

        $response
            ->assertForbidden();
    }

    public function test_destroy_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $value = Value::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->deleteJson('/api/value/'.$value->id);

        $response
            ->assertNoContent();

        $this->assertDatabaseMissing('values', [
            'id' => $value->id,
        ]);
    }

    public function test_cant_destroy_someone_else_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $value = Value::factory()
            ->create();

        $response = $this
            ->deleteJson('/api/value/'.$value->id);

        $response
            ->assertForbidden();
    }
}
