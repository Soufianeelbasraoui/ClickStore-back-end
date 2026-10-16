<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Initial Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Principal',
                'password' => Hash::make('admin'),
                'role' => 'admin',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&auto=format&fit=crop&q=80',
            ]
        );

        $user1 = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Karim Alaoui',
                'password' => Hash::make('user'),
                'role' => 'user',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&auto=format&fit=crop&q=80',
            ]
        );

        $user2 = User::updateOrCreate(
            ['email' => 'sara@example.com'],
            [
                'name' => 'Sara Bennani',
                'password' => Hash::make('user'),
                'role' => 'user',
                'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&auto=format&fit=crop&q=80',
            ]
        );

        $user3 = User::updateOrCreate(
            ['email' => 'youssef@example.com'],
            [
                'name' => 'Youssef Idrissi',
                'password' => Hash::make('user'),
                'role' => 'user',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&auto=format&fit=crop&q=80',
            ]
        );

        // 2. All Products from Front-end
        $jsonPath = database_path('seed_products.json');
        if (file_exists($jsonPath)) {
            $products = json_decode(file_get_contents($jsonPath), true);
            foreach ($products as $p) {
                Product::updateOrCreate(['id' => $p['id']], $p);
            }
        }

        // 3. Initial Orders
        $order1 = Order::updateOrCreate(
            ['id' => 1001],
            [
                'user_id' => $user1->id,
                'total' => 450.00,
                'status' => 'delivered',
                'payment_method' => 'card',
                'shipping_name' => $user1->name,
                'shipping_email' => $user1->email,
                'shipping_phone' => '+212 600-000001',
                'shipping_address' => 'Casablanca, Maroc',
            ]
        );
        OrderItem::firstOrCreate([
            'order_id' => $order1->id,
            'title' => 'Fauteuil Moderne Salon & Bureau Confort Premium',
            'price' => 450.00,
            'quantity' => 1,
            'img' => '/src/assets/images/pmob.png',
        ]);

        $order2 = Order::updateOrCreate(
            ['id' => 1002],
            [
                'user_id' => $user2->id,
                'total' => 720.00,
                'status' => 'processing',
                'payment_method' => 'paypal',
                'shipping_name' => $user2->name,
                'shipping_email' => $user2->email,
                'shipping_phone' => '+212 600-000002',
                'shipping_address' => 'Rabat, Maroc',
            ]
        );
        OrderItem::firstOrCreate([
            'order_id' => $order2->id,
            'title' => 'Table Basse Design Géométrique en Bois Massif',
            'price' => 320.00,
            'quantity' => 1,
            'img' => '/src/assets/images/pmob2.png',
        ]);

        $order3 = Order::updateOrCreate(
            ['id' => 1003],
            [
                'user_id' => $user3->id,
                'total' => 310.00,
                'status' => 'shipped',
                'payment_method' => 'cash',
                'shipping_name' => $user3->name,
                'shipping_email' => $user3->email,
                'shipping_phone' => '+212 600-000003',
                'shipping_address' => 'Marrakech, Maroc',
            ]
        );

        $order4 = Order::updateOrCreate(
            ['id' => 1004],
            [
                'user_id' => $user1->id,
                'total' => 190.00,
                'status' => 'pending',
                'payment_method' => 'card',
                'shipping_name' => $user1->name,
                'shipping_email' => $user1->email,
                'shipping_phone' => '+212 600-000001',
                'shipping_address' => 'Casablanca, Maroc',
            ]
        );
    }
}
