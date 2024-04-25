<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'amount' => $this->faker->randomFloat(2, 1.00, 10000.00),
            'comment' => $this->faker->text(),
            'created_at' => $this->faker->dateTimeBetween('-20 days', 'now')->format('Y-m-d H:i:s'),
            'category_id' => Category::all()->random()->id
        ];
    }
}
