<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@example.com')->first();

        if ($admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'product',
                'data' => ['message' => 'New product "Laptop" added to inventory'],
                'read_at' => null,
            ]);

            Notification::create([
                'user_id' => $admin->id,
                'type' => 'order',
                'data' => ['message' => 'New order received - Order #ORD-001 from John Doe'],
                'read_at' => null,
            ]);

            Notification::create([
                'user_id' => $admin->id,
                'type' => 'stock',
                'data' => ['message' => 'Low stock alert - Product "Keyboard" has only 5 units left'],
                'read_at' => null,
            ]);

            Notification::create([
                'user_id' => $admin->id,
                'type' => 'order',
                'data' => ['message' => 'Order #ORD-002 has been shipped'],
                'read_at' => null,
            ]);

            Notification::create([
                'user_id' => $admin->id,
                'type' => 'product',
                'data' => ['message' => 'Product "Mouse" price has been updated'],
                'read_at' => now()->subHours(2),
            ]);
        }
    }
}
