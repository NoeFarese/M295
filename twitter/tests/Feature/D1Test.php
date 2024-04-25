<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class D1Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_tweet_factory_creates_related_user()
    {
        $tweet = Tweet::factory()->create(
            [
                'user_id' => User::factory()->create()->id
            ]
        );

        $this->assertNotNull($tweet->user);
    }
}
