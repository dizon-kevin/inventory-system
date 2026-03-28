<?php

namespace App\Http\Controllers;

use App\Events\OrderStatusUpdated;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminOrderController extends Controller
{
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

        $order->status = $data['status'];
        if ($data['status'] === 'approved') {
            $order->approved_at = now();
        }
        if ($data['status'] === 'completed') {
            $order->completed_at = now();
        }
        $order->save();

        event(new OrderStatusUpdated($order));

        return back()->with('success', 'Order status updated.');
    }
}
