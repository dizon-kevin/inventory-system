<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\TrackerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use RuntimeException;

class SyncOrderCreatedToTracker implements ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public function __construct(public int $orderId)
    {
        $this->onQueue('tracker-sync');
    }

    public function backoff(): array
    {
        return [10, 30, 60, 120];
    }

    public function handle(TrackerService $trackerService): void
    {
        $order = Order::query()->with('items.product', 'user')->findOrFail($this->orderId);

        if (! $trackerService->sendOrderCreated($order)) {
            throw new RuntimeException("Unable to sync created order {$this->orderId} to Tracker.");
        }
    }
}
