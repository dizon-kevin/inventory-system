<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Services\TrackerService;

class OrderController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected TrackerService $trackerService
    ) {
    }

    public function index()
    {
        $orders = Order::where('user_id', auth()->id())->with('items.product')->latest()->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    public function create()
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.products.index')->with('error', 'Cart is empty. Please add products first.');
        }

        $total = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

        return view('user.orders.create', compact('cartItems', 'total'));
    }

    public function store(Request $request)
    {
        $cartItems = Cart::with('product')->where('user_id', auth()->id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.products.index')->with('error', 'Cart is empty.');
        }

        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->quantity) {
                return back()->with('error', "Not enough stock for {$item->product->name}. Please update your cart.");
            }
        }

        $total = $cartItems->sum(fn ($item) => $item->quantity * $item->product->price);

        $order = Order::create([
            'user_id' => auth()->id(),
            'total_price' => $total,
            'status' => 'pending',
            'placed_at' => now(),
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->product->price,
                'total_price' => $item->quantity * $item->product->price,
            ]);

            $item->product->decrement('quantity', $item->quantity);
        }

        Cart::where('user_id', auth()->id())->delete();

        $this->notificationService->notifyAdmins($order);
        $this->trackerService->sendOrderCreated($order);

        return redirect()->route('user.orders.index')->with('success', 'Order placed successfully.');
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $order->load('items.product', 'user');

        return view('user.orders.show', compact('order'));
    }

    public function cancel(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (! in_array($order->status, ['pending'])) {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->status = 'rejected';
        $order->save();

        foreach ($order->items as $item) {
            $item->product->increment('quantity', $item->quantity);
        }

        $this->notificationService->notifyUser($order, 'rejected');
        $this->trackerService->sendOrderStatus($order);

        return back()->with('success', 'Order cancelled successfully.');
    }

    public function track(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        return view('user.orders.track', compact('order'));
    }
}
