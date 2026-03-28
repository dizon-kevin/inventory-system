<?php

namespace App\Providers;

use App\Events\ProductLowStock;
use App\Events\ProductAdded;
use App\Events\ProductUpdated;
use App\Listeners\SendNotification;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProductLowStock::class => [
            SendNotification::class,
        ],
        ProductAdded::class => [
            SendNotification::class,
        ],
        ProductUpdated::class => [
            SendNotification::class,
        ],
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
