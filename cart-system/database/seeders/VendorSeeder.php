<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{

    public function run(): void
    {
        $vendors = [
            [
                'name' => 'TechGear Electronics',
                'email' => 'contact@techgear.com',
                'slug' => 'techgear-electronics',
            ],
            [
                'name' => 'Fashion Hub',
                'email' => 'info@fashionhub.com',
                'slug' => 'fashion-hub',
            ],
            [
                'name' => 'Home Essentials',
                'email' => 'support@homeessentials.com',
                'slug' => 'home-essentials',
            ],
            [
                'name' => 'Sports World',
                'email' => 'hello@sportsworld.com',
                'slug' => 'sports-world',
            ],
            [
                'name' => 'Book Paradise',
                'email' => 'contact@bookparadise.com',
                'slug' => 'book-paradise',
            ],
        ];

        foreach ($vendors as $vendorData) {
            Vendor::firstOrCreate(
                ['email' => $vendorData['email']],
                $vendorData
            );
        }

        Vendor::factory()->count(5)->create();
    }
}
