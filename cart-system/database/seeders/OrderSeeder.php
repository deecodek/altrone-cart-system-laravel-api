<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{

    public function run(): void
    {
        $users = User::where('role', 'customer')->get();
        $vendors = Vendor::all();
        $products = Product::all();

        $users->each(function ($user) use ($vendors, $products) {
            $orderCount = rand(2, 3);

            for ($i = 0; $i < $orderCount; $i++) {
                $vendor = $vendors->random();
                $vendorProducts = $products->where('vendor_id', $vendor->id);

                if ($vendorProducts->isEmpty()) {
                    continue;
                }

                $total = 0;
                $status = [OrderStatus::PENDING->value, OrderStatus::PAID->value, OrderStatus::PAID->value][array_rand([OrderStatus::PENDING->value, OrderStatus::PAID->value, OrderStatus::PAID->value])];

                $order = Order::create([
                    'user_id' => $user->id,
                    'vendor_id' => $vendor->id,
                    'status' => $status,
                    'total' => 0,
                ]);

                $itemsCount = min(rand(1, 3), $vendorProducts->count());
                $selectedProducts = $vendorProducts->random($itemsCount);

                foreach ($selectedProducts as $product) {
                    $quantity = rand(1, 2);
                    $itemTotal = $product->price * $quantity;
                    $total += $itemTotal;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'quantity' => $quantity,
                        'unit_price' => $product->price,
                        'total_price' => $itemTotal,
                    ]);
                }

                $order->update(['total' => $total]);

                if ($status === OrderStatus::PAID->value) {
                    Payment::create([
                        'order_id' => $order->id,
                        'amount' => $total,
                        'status' => 'paid',
                    ]);
                }
            }
        });

    }
}
