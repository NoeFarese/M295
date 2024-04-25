<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Transaction;
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

        Category::factory(10)->create();

        Transaction::factory()
            ->count(250)
            ->state(['type' => 'expense'])
            ->create();

        Transaction::factory()
            ->count(250)
            ->state(['type' => 'income'])
            ->create();

        User::factory(60)->create();

        User::first()->update([
            'email' => 'user1@example.com',
            'password' => bcrypt('secret')
        ]);
    }
}
