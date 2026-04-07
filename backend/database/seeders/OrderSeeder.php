<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        if ($users->count() == 0) {
            $user = User::create([
                'name' => 'Demo User',
                'email' => 'demo' . rand(100, 999) . '@example.com',
                'password' => bcrypt('password')
            ]);
        } else {
            $user = $users->first();
        }

        $products = Product::inRandomOrder()->take(50)->get();
        if ($products->count() == 0) {
            echo "لا توجد منتجات لإنشاء طلبات! يرجى إضافة منتجات أولاً.\n";
            return;
        }

        $statuses = ['pending', 'shipped', 'delivered', 'cancelled'];
        $addresses = ['الرياض, السعودية', 'جدة, السعودية', 'القاهرة, مصر', 'دبي, الإمارات', 'أبوظبي, الإمارات'];

        for ($i = 0; $i < 10; $i++) {
            $subtotal = 0;
            $itemsToAttach = [];
            $randomProducts = $products->random(rand(1, 4));

            foreach ($randomProducts as $product) {
                $qty = rand(1, 3);
                $subtotal += $product->price * $qty;
                $itemsToAttach[] = [
                    'product_id' => $product->id,
                    'name' => $product->title,
                    'price' => $product->price,
                    'quantity' => $qty,
                    'image' => $product->thumbnail ?? 'placeholder.png',
                ];
            }

            $shipping = rand(20, 100);
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_name' => $user->name,
                'phone' => '05' . rand(10000000, 99999999),
                'address' => $addresses[array_rand($addresses)],
                'status' => $statuses[array_rand($statuses)],
                'subtotal' => $subtotal,
                'shipping_fee' => $shipping,
                'total' => $subtotal + $shipping,
            ]);

            foreach ($itemsToAttach as $item) {
                $item['order_id'] = $order->id;
                OrderItem::create($item);
            }
        }
        echo "تم إضافة 10 طلبات تجريبية بنجاح.\n";
    }
}
