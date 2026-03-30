<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TrackerService
{
    public function sendOrderCreated(Order $order): bool
    {
        $order->loadMissing('user', 'items.product');

        $payload = [
            'storix_order_id' => $order->id,
            'storix_user_id' => $order->user_id,
            'status' => $this->normalizeStatus($order->status),
            'total_price' => $order->total_price,
            'placed_at' => $order->placed_at?->toIsoString(),
            'items' => $order->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'sku' => $item->product?->id,
                'product_name' => $item->product?->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ])->all(),
        ];

        return $this->postToTracker('/orders/sync', $payload);
    }

    public function sendOrderStatus(Order $order): bool
    {
        return $this->postToTracker("/orders/{$order->id}/status", [
            'status' => $this->normalizeStatus($order->status),
            'approved_at' => $order->approved_at?->toIsoString(),
            'completed_at' => $order->completed_at?->toIsoString(),
        ]);
    }

    protected function postToTracker(string $path, array $payload): bool
    {
        $baseUrl = rtrim((string) config('services.tracker.url'), '/');

        if ($baseUrl === '') {
            return false;
        }

        if (! str_ends_with($baseUrl, '/api')) {
            $baseUrl .= '/api';
        }

        $url = $baseUrl . $path;

        $response = Http::withToken(config('services.tracker.token'))
            ->timeout(10)
            ->post($url, $payload);

        if ($response->successful()) {
            return true;
        }

        try {
            Log::warning('Tracker API request failed', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (Throwable) {
            // Ignore logging failures so the original sync failure can be retried by the queue.
        }

        return false;
    }

    protected function normalizeStatus(string $status): string
    {
        return match ($status) {
            'rejected' => 'cancelled',
            default => $status,
        };
    }
}
