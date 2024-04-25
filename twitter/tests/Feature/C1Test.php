<?php

namespace Tests\Feature;

use App\Models\Tweet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class C1Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_get_tweets_returns_200_status_code()
    {
        $response = $this->get('/api/tweets');
        $response->assertStatus(200);
    }

    public function test_endpoint_get_tweets_fetching_at_maximum_100_tweets()
    {
        $response = $this->get('/api/tweets');
        $this->assertTrue(count($response['data']) <= 100);
    }

    public function test_endpoint_get_tweets_sorts_newest_tweet_first()
    {
        $response = $this->get('/api/tweets');
        $response['data'][0]['created_at'];

        $this->assertEquals(
            $response['data'][0]['id'],
            Tweet::orderBy('created_at', 'desc')->first()->id
        );
    }
}
