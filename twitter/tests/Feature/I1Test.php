<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class I1Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_endpoint_get_users_id_returns_asserted_data_format()
    {
        $response = $this->getJson('/api/users/1');
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'created_at',
                'is_verified',
            ]
        ]);
    }

    public function test_endpoint_get_tweets_id_returns_asserted_data_format()
    {
        $response = $this->getJson('/api/tweets');
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'is_verified',
                    ]
                ]
            ]
        ]);
    }

    public function test_endpoint_get_users_id_returns_positive_is_valid_if_likes_more_than_100000()
    {
        DB::statement('PRAGMA foreign_keys = OFF;');

        $user = User::factory()->has(
            Tweet::factory()->count(1)->state([
                'likes' => 100001
            ])
        )->create();

        $response = $this->getJson('/api/users/' . $user->id);
        $response->assertJsonPath('data.is_verified', true);
    }

    public function test_endpoint_get_users_id_returns_negative_is_valid_if_likes_less_than_100000()
    {
        $user = User::factory()->has(
            Tweet::factory()->count(1)->state([
                'likes' => 99999
            ])
        )->create();

        $response = $this->getJson('/api/users/' . $user->id);
        $response->assertJsonPath('data.is_verified', false);
    }
}
