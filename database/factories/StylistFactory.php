<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stylist>
 */
class StylistFactory extends Factory
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
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
        ];
    }
}
