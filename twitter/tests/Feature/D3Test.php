<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class D3Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_get_user_id_returns_200()
    {
        $response = $this->get('/api/users/1');
        $response->assertStatus(200);
    }

    public function test_endpoint_get_user_id_return_data()
    {
        $response = $this->get('/api/users/1');
        $this->assertArrayHasKey('data', $response);
    }

    public function test_endpoint_get_user_id_returns_asserted_data_format()
    {
        $response = $this->get('/api/users/1');
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email'
            ]
        ]);
    }
}
