<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class E4Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_get_user_id_tweets_returns_200()
    {
        $response = $this->get('/api/users/1/tweets');

        $response->assertStatus(200);
    }

    public function test_endpoint_get_user_id_tweets_returns_at_max_10_tweets()
    {
        $user = User::factory()->has(
            Tweet::factory()->count(15)
        )->create();

        $response = $this->get('/api/users/' . $user->id . '/tweets');
        $response->assertJsonCount(10, 'data');
    }

    public function test_endpoint_get_user_id_tweets_returns_tweets_of_selected_user()
    {
        $user = User::factory()->has(
            Tweet::factory()->count(50)
        )->create();

        $response = $this->get('/api/users/' . $user->id . '/tweets');
        $assertionArray = collect($response['data'])->filter(function ($tweet) use ($user) {
            return $tweet['user']['id'] !== $user->id;
        });

        $this->assertEmpty($assertionArray);
    }

}
