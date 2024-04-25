<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class G5Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_delete_me_returns_401_without_valid_token(): void
    {
        $response = $this->deleteJson('/api/me');
        $response->assertStatus(401);
    }

    public function test_endpoint_delete_me_returns_200_with_valid_token(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $response = $this->deleteJson('/api/me');
        $response->assertStatus(200);
    }

    public function test_endpoint_delete_me_deletes_user_in_database(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());
        $this->deleteJson('/api/me');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id
        ]);
    }
}
