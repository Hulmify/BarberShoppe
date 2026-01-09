<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => \App\Models\Shop::factory(),
            'name' => fake()->jobTitle(),
            'duration' => fake()->randomElement([30, 60, 90]),
            'price' => fake()->randomFloat(2, 20, 100),
        ];
    }
}
