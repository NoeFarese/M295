<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class D2Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;


    public function test_tweet_factory_creates_20_users()
    {
        $this->assertEquals(20, User::count());
    }

    public function test_tweet_factory_creates_at_minimum_1_user_with_1_tweet()
    {
        $this->assertTrue(User::whereHas('tweets')->count() >= 1);
    }
}
