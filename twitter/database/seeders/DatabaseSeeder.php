<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        /*
        User::factory()
            ->count(20)
            ->has(Tweet::factory()->count(30))
            ->create();
        */

        /*
        User::factory()
            ->count(20)
            ->has(Tweet::factory()->count(30))
            ->create();
        */


        User::factory()
            ->count(20)
            ->create()
            ->each(function ($user) {
                $tweetCount = rand(0, 50);
                $user->tweets()->saveMany(Tweet::factory()->count($tweetCount)->make());
            });

        User::first()->update([
            'email' => 'user@example.com',
            'password' => bcrypt('password')
        ]);
    }
}
