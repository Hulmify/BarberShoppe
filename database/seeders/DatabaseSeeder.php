<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Service;
use App\Models\Shop;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Barber User
        $user = User::create([
            'name' => 'Barber Joe',
            'email' => 'joe@barbershoppe.com',
            'password' => bcrypt('password'),
            'role' => 'barber',
        ]);

        // Create Shop
        $shop = Shop::create([
            'user_id' => $user->id,
            'name' => 'Joe\'s Cuts',
            'slug' => 'joes-cuts',
            'custom_domain' => 'joescuts.local', 
            'description' => 'Best cuts in town.',
            'primary_color' => '#ff5722',
        ]);

        // Create Services
        $services = [
            ['name' => 'Classic Haircut', 'price' => 25.00, 'duration_minutes' => 30, 'description' => 'Traditional scissor cut.'],
            ['name' => 'Beard Trim', 'price' => 15.00, 'duration_minutes' => 15, 'description' => 'Shape and trim.'],
            ['name' => 'Full Service', 'price' => 35.00, 'duration_minutes' => 45, 'description' => 'Haircut + Beard Trim + Hot Towel.'],
        ];

        foreach ($services as $svc) {
            Service::create(array_merge($svc, ['shop_id' => $shop->id]));
        }

        // Create Availability (Mon-Fri, 9am - 5pm)
        for ($i = 1; $i <= 5; $i++) {
            Availability::create([
                'shop_id' => $shop->id,
                'day_of_week' => $i,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
            ]);
        }
    }
}
