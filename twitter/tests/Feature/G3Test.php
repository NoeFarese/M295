<?php

namespace Tests\Feature;

use App\Http\Resources\UserResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class G3Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_get_me_returns_current_user_with_valid_token(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/me');

        $response->assertExactJson(['data' => UserResource::make($user)->resolve()]);
    }

    public function test_endpoint_get_me_returns_401_without_valid_token(): void
    {
        $response = $this->getJson('/api/me');

        $response->assertStatus(401);
    }
}
