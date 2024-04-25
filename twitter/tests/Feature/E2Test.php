<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class E2Test extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_there_is_no_n1_problem()
    {
        DB::enableQueryLog();

        $this->getJson('/api/tweets');

        $this->assertLessThanOrEqual(3, count(DB::getQueryLog()), 'There is a N+1 problem present, too many queries are executed.');
    }
}
