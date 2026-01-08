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

    ['name' => 'Kids Haircut', 'price' => 18.00, 'duration_minutes' => 25, 'description' => 'Haircut for children under 12.'],
    ['name' => 'Senior Haircut', 'price' => 20.00, 'duration_minutes' => 30, 'description' => 'Discounted haircut for seniors.'],
    ['name' => 'Buzz Cut', 'price' => 15.00, 'duration_minutes' => 15, 'description' => 'One-length machine cut.'],
    ['name' => 'Fade Haircut', 'price' => 30.00, 'duration_minutes' => 35, 'description' => 'Low, mid, or high fade.'],
    ['name' => 'Skin Fade', 'price' => 32.00, 'duration_minutes' => 40, 'description' => 'Fade down to the skin.'],
    ['name' => 'Crew Cut', 'price' => 22.00, 'duration_minutes' => 25, 'description' => 'Short and clean style.'],
    ['name' => 'Undercut', 'price' => 28.00, 'duration_minutes' => 35, 'description' => 'Disconnected undercut style.'],
    ['name' => 'Pompadour Styling', 'price' => 20.00, 'duration_minutes' => 20, 'description' => 'Styled pompadour finish.'],
    ['name' => 'Hair Styling Only', 'price' => 15.00, 'duration_minutes' => 15, 'description' => 'Wash and style without cutting.'],
    ['name' => 'Hair Wash', 'price' => 10.00, 'duration_minutes' => 10, 'description' => 'Shampoo and rinse.'],
    ['name' => 'Deep Conditioning', 'price' => 18.00, 'duration_minutes' => 20, 'description' => 'Moisturizing hair treatment.'],
    ['name' => 'Scalp Massage', 'price' => 15.00, 'duration_minutes' => 15, 'description' => 'Relaxing scalp massage.'],
    ['name' => 'Beard Shaping', 'price' => 18.00, 'duration_minutes' => 20, 'description' => 'Detailed beard shaping.'],
    ['name' => 'Beard Line-Up', 'price' => 12.00, 'duration_minutes' => 10, 'description' => 'Clean beard edges.'],
    ['name' => 'Beard Coloring', 'price' => 25.00, 'duration_minutes' => 30, 'description' => 'Natural beard color enhancement.'],
    ['name' => 'Hot Towel Shave', 'price' => 30.00, 'duration_minutes' => 30, 'description' => 'Traditional straight razor shave.'],
    ['name' => 'Clean Shave', 'price' => 20.00, 'duration_minutes' => 20, 'description' => 'Smooth razor shave.'],
    ['name' => 'Mustache Trim', 'price' => 8.00, 'duration_minutes' => 10, 'description' => 'Quick mustache grooming.'],
    ['name' => 'Facial Cleanup', 'price' => 20.00, 'duration_minutes' => 25, 'description' => 'Basic facial treatment.'],
    ['name' => 'Charcoal Facial', 'price' => 30.00, 'duration_minutes' => 35, 'description' => 'Deep cleansing charcoal facial.'],
    ['name' => 'Gold Facial', 'price' => 40.00, 'duration_minutes' => 45, 'description' => 'Premium gold facial treatment.'],
    ['name' => 'Face Scrub', 'price' => 12.00, 'duration_minutes' => 15, 'description' => 'Exfoliating face scrub.'],
    ['name' => 'Face Massage', 'price' => 15.00, 'duration_minutes' => 20, 'description' => 'Relaxing face massage.'],
    ['name' => 'Eyebrow Trim', 'price' => 7.00, 'duration_minutes' => 5, 'description' => 'Neat eyebrow trimming.'],
    ['name' => 'Hair Coloring', 'price' => 45.00, 'duration_minutes' => 60, 'description' => 'Full hair color service.'],
    ['name' => 'Root Touch-Up', 'price' => 30.00, 'duration_minutes' => 45, 'description' => 'Root color correction.'],
    ['name' => 'Highlights', 'price' => 55.00, 'duration_minutes' => 75, 'description' => 'Partial hair highlights.'],
    ['name' => 'Grey Coverage', 'price' => 35.00, 'duration_minutes' => 50, 'description' => 'Grey hair coverage.'],
    ['name' => 'Hair Straightening', 'price' => 60.00, 'duration_minutes' => 90, 'description' => 'Temporary hair straightening.'],
    ['name' => 'Hair Smoothening', 'price' => 70.00, 'duration_minutes' => 100, 'description' => 'Smooth and frizz-free hair.'],
    ['name' => 'Keratin Treatment', 'price' => 90.00, 'duration_minutes' => 120, 'description' => 'Keratin hair repair treatment.'],
    ['name' => 'Head Massage', 'price' => 20.00, 'duration_minutes' => 25, 'description' => 'Oil head massage.'],
    ['name' => 'Anti-Dandruff Treatment', 'price' => 25.00, 'duration_minutes' => 30, 'description' => 'Dandruff control therapy.'],
    ['name' => 'Hair Spa', 'price' => 35.00, 'duration_minutes' => 40, 'description' => 'Nourishing hair spa session.'],
    ['name' => 'Express Grooming', 'price' => 20.00, 'duration_minutes' => 20, 'description' => 'Quick grooming service.'],
    ['name' => 'Premium Grooming', 'price' => 50.00, 'duration_minutes' => 60, 'description' => 'Complete premium grooming.'],
    ['name' => 'Wedding Groom Package', 'price' => 120.00, 'duration_minutes' => 150, 'description' => 'Full grooming for special occasions.'],
    ['name' => 'Party Styling', 'price' => 25.00, 'duration_minutes' => 30, 'description' => 'Hair styling for events.'],
    ['name' => 'Express Shave', 'price' => 12.00, 'duration_minutes' => 10, 'description' => 'Quick clean shave.'],
    ['name' => 'Luxury Shave', 'price' => 35.00, 'duration_minutes' => 40, 'description' => 'Premium shaving experience.'],
    ['name' => 'Aftershave Treatment', 'price' => 10.00, 'duration_minutes' => 10, 'description' => 'Soothing aftershave care.'],
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
