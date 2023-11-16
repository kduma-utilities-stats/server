<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'device_name',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token'
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_new_users_cant_register_when_there_is_already_an_account(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'device_name',
        ]);

        $this->assertGuest();
        $response->assertForbidden();
        $this->assertDatabaseEmpty('personal_access_tokens');
    }
}
