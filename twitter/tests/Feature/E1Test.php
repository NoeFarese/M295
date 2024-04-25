<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class E1Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_get_tweets_returns_asserted_data_format()
    {
        $response = $this->getJson('/api/tweets');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'text',
                    'likes',
                    'created_at',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                    ]
                ]
            ]
        ]);
    }

    public function test_endpoint_get_tweets_returns_no_unasserted_data()
    {
        $response = $this->get('/api/tweets');

        $response->assertJsonMissingPath('data.0.user_id');
        $response->assertJsonMissingPath('data.0.updated_at');
        $response->assertJsonMissingPath('data.0.user.email_verified_at');
        $response->assertJsonMissingPath('data.0.user.updated_at');
    }

    public function test_endpoint_get_tweets_returns_created_at_in_iso8601string_format()
    {
        $response = $this->get('/api/tweets');
        $formattedDate = Carbon::parse($response['data'][0]['created_at'])->toIso8601String();
        $response->assertJsonPath('data.0.created_at', $formattedDate);
    }
}
