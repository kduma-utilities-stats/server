<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Reading;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class ReadingTest extends TestCase
{
    use RefreshDatabase;

    public function test_cant_get_readings_list_unauthorized(): void
    {
        $response = $this
            ->getJson('/api/reading');

        $response->assertUnauthorized();
    }

    public function test_getting_readings_empty_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this
            ->getJson('/api/reading');

        $response
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_getting_readings_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $readings = Reading::factory(2)
            ->recycle($user)
            ->create();

        $response = $this
            ->getJson('/api/reading');

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => $readings->map(fn(Reading $reading) => [
                    'id' => $reading->id,
                    'performed_on' => $reading->performed_on,
                    'user_id' => $reading->user_id,
                ])->toArray()
            ]);
    }

    public function test_cant_see_someone_else_readings_on_list(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()->create();

        $response = $this
            ->getJson('/api/reading');

        $response
            ->assertOk()
            ->assertExactJson(['data' => []]);
    }

    public function test_creating_new_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->freezeSecond();
        $response = $this
            ->postJson('/api/reading', [
                'performed_on' => now()
            ]);

        $reading = Reading::first();

        $response
            ->assertCreated()
            ->assertExactJson([
                'data' => [
                    'id' => $reading->id,
                    'performed_on' => now(),
                    'user_id' => $reading->user_id,
                ]
            ]);
    }

    public function test_creating_new_reading_with_default_date(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->freezeSecond();
        $response = $this
            ->postJson('/api/reading', []);

        $reading = Reading::first();

        $response
            ->assertCreated()
            ->assertExactJson([
                'data' => [
                    'id' => $reading->id,
                    'performed_on' => now(),
                    'user_id' => $reading->user_id,
                ]
            ]);
    }

    public function test_validation_when_creating_new_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this
            ->postJson('/api/reading', [
                'performed_on' => 'abcd',
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('performed_on');
    }

    public function test_getting_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->getJson('/api/reading/'.$reading->id);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $reading->id,
                    'performed_on' => $reading->performed_on,
                    'user_id' => $reading->user_id,
                ]
            ]);
    }

    public function test_cant_get_someone_else_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->create();


        $response = $this
            ->getJson('/api/reading/'.$reading->id);

        $response
            ->assertForbidden();
    }

    public function test_cant_get_non_existing_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this
            ->getJson('/api/reading/123456');

        $response
            ->assertNotFound();
    }

    public function test_updating_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $this->freezeSecond();
        $response = $this
            ->putJson('/api/reading/'.$reading->id, [
                'performed_on' => now()
            ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $reading->id,
                    'performed_on' => now(),
                    'user_id' => $reading->user_id,
                ]
            ]);
    }

    public function test_nothing_happens_when_updating_reading_without_fields(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/reading/'.$reading->id, [

            ]);

        $response
            ->assertOk()
            ->assertExactJson([
                'data' => [
                    'id' => $reading->id,
                    'performed_on' => $reading->performed_on,
                    'user_id' => $reading->user_id,
                ]
            ]);
    }

    public function test_validation_when_updating_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->putJson('/api/reading/'.$reading->id, [
                'performed_on' => 'New Name'
            ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrorFor('performed_on');
    }

    public function test_cant_update_someone_else_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->create();

        $response = $this
            ->putJson('/api/reading/'.$reading->id, [
                'performed_on' => now()
            ]);

        $response
            ->assertForbidden();
    }

    public function test_destroy_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->recycle($user)
            ->create();

        $response = $this
            ->deleteJson('/api/reading/'.$reading->id);

        $response
            ->assertNoContent();

        $this->assertDatabaseMissing('readings', [
            'id' => $reading->id,
        ]);
    }

    public function test_cant_destroy_someone_else_reading(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $reading = Reading::factory()
            ->create();

        $response = $this
            ->deleteJson('/api/reading/'.$reading->id);

        $response
            ->assertForbidden();
    }
}
