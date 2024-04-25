<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class C2Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_get_tweets_return_asserted_data_format()
    {
        $response = $this->get('/api/tweets');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'user' => [
                        'id',
                        'name'
                    ]
                ]
            ]
        ]);
    }
}
