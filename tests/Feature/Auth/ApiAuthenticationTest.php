<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'device_name',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token'
            ]);
        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $this->assertDatabaseEmpty('personal_access_tokens');
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test');
        $token_id = $token->accessToken->id;
        $plainTextToken = $token->plainTextToken;
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $token_id]);

        $response = $this
            ->withToken($plainTextToken)
            ->postJson('/api/logout');

        $response->assertNoContent();
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token_id]);
    }
}
