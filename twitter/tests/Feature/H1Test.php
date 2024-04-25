<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class H1Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_post_tweets_id_like_returns_tweet_with_1_like_more(): void
    {
        Model::unguard();

        $user = Sanctum::actingAs(User::factory()->create());
        $tweet = $user->tweets()->create(Tweet::factory()->make(['likes' => 0])->toArray());

        $this->postJson('/api/tweets/' . $tweet->id . '/like');


        $this->assertEquals(1, $tweet->fresh()->likes);
    }
}
