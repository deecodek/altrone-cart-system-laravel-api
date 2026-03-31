<?php

namespace Database\Seeders;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class CartSeeder extends Seeder
{

    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $products = Product::all();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->error('Missing customers or products. Please run other seeders first.');

            return;
        }

        $customers->take(3)->each(function ($user) use ($products) {
            $cart = Cart::firstOrCreate(
                ['user_id' => $user->id],
                []
            );

            $cartProducts = $products->random(rand(2, 4));

            foreach ($cartProducts as $product) {
                CartItem::firstOrCreate(
                    [
                        'cart_id' => $cart->id,
                        'product_id' => $product->id,
                    ],
                    [
                        'quantity' => rand(1, 3),
                    ]
                );
            }

        });
    }
}
