<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class F5Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_get_auth_returns_401_without_valid_token(): void
    {
        $response = $this->getJson('/api/auth');
        $response->assertStatus(401);
    }

    public function test_endpoint_get_auth_returns_user_resource_with_valid_token(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/auth');

        $response->assertJsonStructure(['data' => ['id', 'name', 'email', 'created_at', 'is_verified']]);
    }
}
