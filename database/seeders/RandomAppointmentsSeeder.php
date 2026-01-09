<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Service;
use App\Models\Stylist;
use Illuminate\Support\Arr;

class RandomAppointmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get the first shop or create one
        $shop = Shop::first();
        if (!$shop) {
            $this->command->info('No shop found. Creating one...');
            $shop = Shop::factory()->create();
        }

        $this->command->info("Seeding appointments for Shop: {$shop->name} (ID: {$shop->id})");

        // 2. Ensure Services exist
        if ($shop->services()->count() < 3) {
            $this->command->info('Creating services...');
            Service::factory()->count(5)->create(['shop_id' => $shop->id]);
        }
        $services = $shop->services;

        // 3. Ensure Stylists exist
        if ($shop->stylists()->count() < 2) {
            $this->command->info('Creating stylists...');
            Stylist::factory()->count(3)->create(['shop_id' => $shop->id]);
        }
        $stylists = $shop->stylists;

        // 4. Create Customers
        $this->command->info('Creating customers...');
        // Create 10 new customers
        $customers = Customer::factory()->count(10)->create();

        // 5. Create Bookings
        $this->command->info('Creating bookings...');
        
        foreach ($customers as $customer) {
            $numberOfBookings = rand(1, 5);

            for ($i = 0; $i < $numberOfBookings; $i++) {
                $stylist = $stylists->random();
                
                // Create the booking shell first
                $booking = Booking::factory()->create([
                    'shop_id' => $shop->id,
                    'customer_id' => $customer->id,
                    'stylist_id' => $stylist->id,
                    'total_price' => 0, // Will update later
                ]);

                // Add items
                $selectedServices = $services->random(rand(1, 2));
                $totalPrice = 0;

                foreach ($selectedServices as $service) {
                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'service_id' => $service->id,
                        'price' => $service->price,
                    ]);
                    $totalPrice += $service->price;
                }

                // Update booking total price
                $booking->update(['total_price' => $totalPrice]);
            }
        }

        $this->command->info('Done!');
    }
}
