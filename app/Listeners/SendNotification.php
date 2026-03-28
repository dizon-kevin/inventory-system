<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Events\ProductAdded;
use App\Events\ProductLowStock;
use App\Events\ProductUpdated;
use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle($event): void
    {
        if ($event instanceof ProductLowStock) {
            $users = User::all();
            foreach ($users as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => 'low_stock',
                    'data' => [
                        'message' => "Product {$event->product->name} is low on stock (Quantity: {$event->product->quantity})",
                        'product_id' => $event->product->id,
                    ],
                ]);
                // Broadcast notification to user
                NotificationCreated::dispatch($notification);
            }
            return;
        }

        if ($event instanceof ProductAdded) {
            $users = User::all();
            foreach ($users as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => 'product_added',
                    'data' => [
                        'message' => "New product {$event->product->name} has been added.",
                        'product_id' => $event->product->id,
                    ],
                ]);
                // Broadcast notification to user
                NotificationCreated::dispatch($notification);
            }
            return;
        }

        if ($event instanceof ProductUpdated) {
            $users = User::all();
            foreach ($users as $user) {
                $notification = Notification::create([
                    'user_id' => $user->id,
                    'type' => 'product_updated',
                    'data' => [
                        'message' => "Product {$event->product->name} has been updated.",
                        'product_id' => $event->product->id,
                    ],
                ]);
                // Broadcast notification to user
                NotificationCreated::dispatch($notification);
            }
            return;
        }

        if ($event instanceof OrderPlaced) {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                $notification = Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'order_placed',
                    'data' => [
                        'message' => "Order #{$event->order->id} placed by {$event->order->user->name}.",
                        'order_id' => $event->order->id,
                    ],
                ]);
                // Broadcast notification to admin
                NotificationCreated::dispatch($notification);
            }
            return;
        }

        if ($event instanceof OrderStatusUpdated) {
            $orderUser = $event->order->user;
            $notification = Notification::create([
                'user_id' => $orderUser->id,
                'type' => 'order_status_updated',
                'data' => [
                    'message' => "Your order #{$event->order->id} has been {$event->order->status}.",
                    'order_id' => $event->order->id,
                    'status' => $event->order->status,
                ],
            ]);
            // Broadcast notification to user
            NotificationCreated::dispatch($notification);
        }
    }
}
