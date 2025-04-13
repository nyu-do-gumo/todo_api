<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_valid_credentials_returns_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'      => 'test@example.com',
            'password'   => 'secret123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'token'
                 ]);
    }

    public function test_login_with_invalid_credentials_returns_unauthorized()
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertExactJson([
                     'error' => 'Unauthorized'
                 ]);
    }
}
