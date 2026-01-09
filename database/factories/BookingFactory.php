<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Shop;
use App\Models\Stylist;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-1 month', '+1 month');
        $endTime = (clone $startTime)->modify('+1 hour');

        return [
            'shop_id' => Shop::factory(), // Should be overridden
            'customer_id' => Customer::factory(),
            'stylist_id' => Stylist::factory(), // Should be overridden or we'll assumet exist
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => fake()->randomFloat(2, 20, 100),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'notes' => fake()->sentence(),
        ];
    }
}
