<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class G4Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_put_me_returns_200_with_valid_token(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->putJson('/api/me', [
            'name' => 'New Name',
            'email' => fake()->email,
        ]);

        $response->assertStatus(200);
    }

    public function test_endpoint_put_me_returns_200_without_valid_token(): void
    {
        $response = $this->putJson('/api/me', [
            'name' => 'New Name'
        ]);

        $response->assertStatus(401);
    }

    public function test_endpoint_put_me_saves_changes_in_database(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $email = fake()->email;

        $this->putJson('/api/me', [
            'name' => 'New Name',
            'email' => $email
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => $email,
        ]);
    }

    public function test_endpoint_put_me_hashes_password(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $this->putJson('/api/me', [
            'password' => 'new-password',
            'password_confirmation' => 'new-password'
        ]);

        $this->assertNotEquals('new-password', $user->fresh()->password);
    }

    public function test_endpoint_put_me_returns_422_without_valid_email(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $response = $this->putJson('/api/me', [
            'name' => $user->name,
            'email' => 'invalid-email',
        ]);

        $response->assertStatus(422);
    }
}
