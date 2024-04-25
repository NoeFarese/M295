<?php

namespace Database\Factories;

use App\Models\plants;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class FarmFactory extends Factory
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
            'slug' => $this->faker->slug(),
            'description' => $this->faker->text(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'zip' => $this->faker->postcode(),
            'plant_id' => plants::factory(),
        ];
    }
}
