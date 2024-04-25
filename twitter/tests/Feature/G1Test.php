<?php

namespace Tests\Feature;

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class G1Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_post_tweets_returns_creates_tweet_in_database(): void
    {
        $user = Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/tweets', [
            'text' => 'This is a tweet',
        ]);

        $this->assertDatabaseHas('tweets', [
            'text' => 'This is a tweet',
            'user_id' => $user->id,
        ]);
    }
}
