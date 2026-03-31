<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{

    public function run(): void
    {
        $vendors = Vendor::all();

        $products = [
            [
                'vendor_slug' => 'techgear-electronics',
                'items' => [
                    ['name' => 'Wireless Bluetooth Headphones', 'description' => 'High-quality noise-canceling headphones with 30h battery life', 'price' => 79.99, 'stock' => 50],
                    ['name' => 'USB-C Fast Charger', 'description' => '65W fast charger compatible with most devices', 'price' => 29.99, 'stock' => 100],
                    ['name' => 'Mechanical Gaming Keyboard', 'description' => 'RGB backlit mechanical keyboard with blue switches', 'price' => 89.99, 'stock' => 30],
                    ['name' => 'Wireless Mouse', 'description' => 'Ergonomic wireless mouse with adjustable DPI', 'price' => 39.99, 'stock' => 75],
                    ['name' => '4K Webcam', 'description' => 'Ultra HD webcam with built-in microphone', 'price' => 129.99, 'stock' => 25],
                ],
            ],
            [
                'vendor_slug' => 'fashion-hub',
                'items' => [
                    ['name' => 'Classic Cotton T-Shirt', 'description' => 'Comfortable 100% cotton t-shirt', 'price' => 19.99, 'stock' => 200],
                    ['name' => 'Slim Fit Jeans', 'description' => 'Modern slim fit denim jeans', 'price' => 49.99, 'stock' => 80],
                    ['name' => 'Leather Belt', 'description' => 'Genuine leather belt with metal buckle', 'price' => 24.99, 'stock' => 60],
                    ['name' => 'Running Shoes', 'description' => 'Lightweight breathable running shoes', 'price' => 69.99, 'stock' => 45],
                    ['name' => 'Winter Jacket', 'description' => 'Warm insulated winter jacket', 'price' => 119.99, 'stock' => 35],
                ],
            ],
            [
                'vendor_slug' => 'home-essentials',
                'items' => [
                    ['name' => 'Stainless Steel Cookware Set', 'description' => '10-piece professional cookware set', 'price' => 199.99, 'stock' => 20],
                    ['name' => 'Memory Foam Pillow', 'description' => 'Ergonomic memory foam pillow for better sleep', 'price' => 34.99, 'stock' => 100],
                    ['name' => 'LED Desk Lamp', 'description' => 'Adjustable LED lamp with USB charging port', 'price' => 44.99, 'stock' => 55],
                    ['name' => 'Vacuum Storage Bags', 'description' => 'Set of 6 space-saving storage bags', 'price' => 22.99, 'stock' => 150],
                    ['name' => 'Digital Kitchen Scale', 'description' => 'Precise digital scale for cooking and baking', 'price' => 18.99, 'stock' => 70],
                ],
            ],
            [
                'vendor_slug' => 'sports-world',
                'items' => [
                    ['name' => 'Yoga Mat', 'description' => 'Non-slip exercise yoga mat with carrying strap', 'price' => 27.99, 'stock' => 90],
                    ['name' => 'Adjustable Dumbbells', 'description' => 'Pair of adjustable dumbbells 5-25 lbs', 'price' => 149.99, 'stock' => 30],
                    ['name' => 'Resistance Bands Set', 'description' => '5-piece resistance bands for home workouts', 'price' => 19.99, 'stock' => 120],
                    ['name' => 'Jump Rope', 'description' => 'Speed jump rope for cardio training', 'price' => 12.99, 'stock' => 80],
                    ['name' => 'Fitness Tracker', 'description' => 'Smart fitness band with heart rate monitor', 'price' => 59.99, 'stock' => 65],
                ],
            ],
            [
                'vendor_slug' => 'book-paradise',
                'items' => [
                    ['name' => 'The Art of Programming', 'description' => 'Comprehensive guide to software development', 'price' => 44.99, 'stock' => 40],
                    ['name' => 'Business Strategy 101', 'description' => 'Essential principles for business success', 'price' => 29.99, 'stock' => 55],
                    ['name' => 'Cooking Masterclass', 'description' => 'Recipes and techniques from professional chefs', 'price' => 34.99, 'stock' => 45],
                    ['name' => 'Mindfulness & Meditation', 'description' => 'Guide to inner peace and mental clarity', 'price' => 21.99, 'stock' => 70],
                    ['name' => 'World History Encyclopedia', 'description' => 'Complete illustrated history of civilization', 'price' => 59.99, 'stock' => 25],
                ],
            ],
        ];

        foreach ($products as $vendorProducts) {
            $vendor = $vendors->firstWhere('slug', $vendorProducts['vendor_slug']);

            if (! $vendor) {
                continue;
            }

            foreach ($vendorProducts['items'] as $productData) {
                Product::firstOrCreate(
                    ['name' => $productData['name']],
                    [
                        'vendor_id' => $vendor->id,
                        'description' => $productData['description'],
                        'price' => $productData['price'],
                        'stock' => $productData['stock'],
                    ]
                );
            }
        }

        Product::factory()->count(20)->create();
    }
}
