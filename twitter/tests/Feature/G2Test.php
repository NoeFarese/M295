<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class G2Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_post_tweets_returns_422_with_too_short_text(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/tweets', [
            'text' => '1',
        ]);

        $response->assertStatus(422);
    }

    public function test_endpoint_post_tweets_returns_422_with_too_long_text(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/tweets', [
            'text' => '1' . str_repeat('0', 160),
        ]);

        $response->assertStatus(422);
    }
}
