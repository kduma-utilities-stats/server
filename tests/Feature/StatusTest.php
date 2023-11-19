<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class StatusTest extends TestCase
{
    public function test_status_unauthenticated(): void
    {
        $response = $this->get('/api/status');

        $response
            ->assertOk()
            ->assertExactJson([
                'version' => config('app.version', 'develop'),
                'laravel' => app()->version(),
                'authenticated' => false,
                'user' => null,
            ]);
    }
    public function test_status_authenticated(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->get('/api/status');

        $response
            ->assertOk()
            ->assertExactJson([
                'version' => config('app.version', 'develop'),
                'laravel' => app()->version(),
                'authenticated' => true,
                'user' => [
                    'id' => $user->prefixed_id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
    }
}
