<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Services\TrackerService;

class AdminOrderController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected TrackerService $trackerService
    ) {
    }

    public function index()
    {
        $orders = Order::with('user')->latest()->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected,completed',
        ]);

        if ($order->status === $data['status']) {
            return back()->with('info', 'Order status did not change.');
        }

        if (
            $this->usesHostedXenditCheckout($order)
            && in_array($data['status'], ['approved', 'completed'], true)
            && $order->payment_status !== 'paid'
        ) {
            return back()->with('error', 'This order is waiting for Xendit payment confirmation. It will be approved automatically after a successful payment.');
        }

        $order->status = $data['status'];
        if ($data['status'] === 'approved') {
            $order->approved_at = now();
        }
        if ($data['status'] === 'completed') {
            $order->completed_at = now();
        }
        $order->save();

        $this->notificationService->notifyUser($order, $data['status']);
        $this->trackerService->sendOrderStatus($order);

        return back()->with('success', 'Order status updated.');
    }

    public function resyncTracker(Order $order)
    {
        $order->loadMissing('items.product', 'user');

        $synced = $this->trackerService->sendOrderCreated($order);

        return back()->with(
            $synced ? 'success' : 'error',
            $synced
                ? 'Order data was resynced to Tracker successfully.'
                : 'Tracker sync failed. Please make sure the Tracker app is running on http://127.0.0.1:8001.'
        );
    }

    protected function usesHostedXenditCheckout(Order $order): bool
    {
        return filled($order->xendit_invoice_url);
    }
}
