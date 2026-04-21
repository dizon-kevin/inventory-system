<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
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
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_amount' => $order->payment_amount,
            'xendit_invoice_id' => $order->xendit_invoice_id,
            'xendit_invoice_url' => $order->xendit_invoice_url,
            'xendit_payment_method' => $order->xendit_payment_method,
            'xendit_reference_id' => $order->xendit_reference_id,
            'prgc_ref' => $this->buildPrgcRef($order),
            'pickup_address' => $order->pickup_address,
            'delivery_address' => $order->delivery_address,
            'placed_at' => $order->placed_at?->toIsoString(),
            'payment_paid_at' => $order->payment_paid_at?->toIsoString(),
            'payment_expires_at' => $order->payment_expires_at?->toIsoString(),
            'approved_at' => $order->approved_at?->toIsoString(),
            'completed_at' => $order->completed_at?->toIsoString(),
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
        $synced = $this->postToTracker("/orders/{$order->id}/status", [
            'status' => $this->normalizeStatus($order->status),
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_amount' => $order->payment_amount,
            'approved_at' => $order->approved_at?->toIsoString(),
            'completed_at' => $order->completed_at?->toIsoString(),
            'payment_paid_at' => $order->payment_paid_at?->toIsoString(),
            'payment_expires_at' => $order->payment_expires_at?->toIsoString(),
            'xendit_invoice_id' => $order->xendit_invoice_id,
            'xendit_invoice_url' => $order->xendit_invoice_url,
            'xendit_payment_method' => $order->xendit_payment_method,
            'xendit_reference_id' => $order->xendit_reference_id,
            'prgc_ref' => $this->buildPrgcRef($order),
            'pickup_address' => $order->pickup_address,
            'delivery_address' => $order->delivery_address,
        ]);

        if ($synced) {
            return true;
        }

        // Self-heal if Tracker missed the original create event and only received a later status update.
        return $this->sendOrderCreated($order);
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

        try {
            $response = Http::withToken(config('services.tracker.token'))
                ->timeout(10)
                ->post($url, $payload);
        } catch (ConnectionException $exception) {
            try {
                Log::warning('Tracker API connection failed', [
                    'url' => $url,
                    'message' => $exception->getMessage(),
                ]);
            } catch (Throwable) {
                // Ignore logging failures so checkout can continue even when Tracker is down.
            }

            return false;
        }

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

    protected function buildPrgcRef(Order $order): string
    {
        $pickup = $order->pickup_address ?? [];
        $delivery = $order->delivery_address ?? [];

        $pickupRef = collect([
            $pickup['region_code'] ?? null,
            $pickup['province_code'] ?? null,
            $pickup['city_code'] ?? null,
            $pickup['barangay_code'] ?? null,
        ])->filter()->implode('-');

        $deliveryRef = collect([
            $delivery['region_code'] ?? null,
            $delivery['province_code'] ?? null,
            $delivery['city_code'] ?? null,
            $delivery['barangay_code'] ?? null,
        ])->filter()->implode('-');

        return trim(implode('|', array_filter([
            $pickupRef !== '' ? "PU:{$pickupRef}" : null,
            $deliveryRef !== '' ? "DL:{$deliveryRef}" : null,
        ])));
    }
}
