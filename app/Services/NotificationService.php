<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;

class NotificationService
{
    public function notifyAdmins(Order $order): void
    {
        $order->loadMissing('user');

        User::where('role', 'admin')
            ->get()
            ->each(function (User $admin) use ($order) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'new_order',
                    'title' => 'New order #' . $order->id,
                    'description' => 'New order #' . $order->id . ' placed by ' . ($order->user->name ?? 'a customer'),
                    'order_id' => $order->id,
                ]);
            });
    }

    public function notifyUser(Order $order, string $status): void
    {
        $order->loadMissing('user');

        $type = match ($status) {
            'approved' => 'order_approved',
            'rejected' => 'order_rejected',
            'completed' => 'order_completed',
            default => 'order_' . $status,
        };

        Notification::create([
            'user_id' => $order->user_id,
            'type' => $type,
            'title' => 'Order #' . $order->id . ' ' . ucfirst($status),
            'description' => 'Your order #' . $order->id . ' has been ' . $status,
            'order_id' => $order->id,
        ]);
    }
}
