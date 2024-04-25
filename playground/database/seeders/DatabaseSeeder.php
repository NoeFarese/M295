<?php

namespace Database\Seeders;

use App\Models\Clown;
use App\Models\farms;
use App\Models\plants;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //plants::factory(100)->create();
        //farms::factory(50)->create();
        Clown::factory(10)->create();
    }
}
