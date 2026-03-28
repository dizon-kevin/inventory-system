<?php

namespace App\Listeners;

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
                    'title' => 'Stock alert',
                    'description' => "Product {$event->product->name} is low on stock (Quantity: {$event->product->quantity})",
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
                    'title' => 'Product added',
                    'description' => "New product {$event->product->name} has been added.",
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
                    'title' => 'Product updated',
                    'description' => "Product {$event->product->name} has been updated.",
                ]);
                // Broadcast notification to user
                NotificationCreated::dispatch($notification);
            }
            return;
        }
    }
}
