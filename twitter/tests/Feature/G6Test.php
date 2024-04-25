<?php

namespace Tests\Feature;

use App\Models\Tweet;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class G6Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_if_related_tweets_get_deleted(): void
    {
        $user = User::factory()->has(Tweet::factory()->count(3))->create();
        Sanctum::actingAs($user);

        $this->deleteJson('/api/me');
        $this->assertDatabaseMissing('tweets', [
            'user_id' => $user->id
        ]);
    }
}
