<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;
use App\Services\TrackerService;
use App\Services\XenditService;

class OrderController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService,
        protected TrackerService $trackerService,
        protected XenditService $xenditService
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

        return view('user.orders.create', [
            'cartItems' => $cartItems,
            'total' => $total,
            'paymentMethods' => config('checkout.payment_methods'),
            'xenditConfigured' => $this->xenditService->isConfigured(),
            'xenditConfigIssue' => $this->xenditService->configurationIssue(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_method' => ['required', 'string', 'in:' . implode(',', array_keys(config('checkout.payment_methods')))],
            'pickup_address.region_code' => ['required', 'string', 'max:20'],
            'pickup_address.region_name' => ['required', 'string', 'max:255'],
            'pickup_address.province_code' => ['nullable', 'string', 'max:20'],
            'pickup_address.province_name' => ['nullable', 'string', 'max:255'],
            'pickup_address.city_code' => ['required', 'string', 'max:20'],
            'pickup_address.city_name' => ['required', 'string', 'max:255'],
            'pickup_address.barangay_code' => ['required', 'string', 'max:20'],
            'pickup_address.barangay_name' => ['required', 'string', 'max:255'],
            'pickup_address.street_address' => ['required', 'string', 'max:255'],
            'pickup_address.contact_number' => ['required', 'string', 'max:30'],
            'delivery_address.region_code' => ['required', 'string', 'max:20'],
            'delivery_address.region_name' => ['required', 'string', 'max:255'],
            'delivery_address.province_code' => ['nullable', 'string', 'max:20'],
            'delivery_address.province_name' => ['nullable', 'string', 'max:255'],
            'delivery_address.city_code' => ['required', 'string', 'max:20'],
            'delivery_address.city_name' => ['required', 'string', 'max:255'],
            'delivery_address.barangay_code' => ['required', 'string', 'max:20'],
            'delivery_address.barangay_name' => ['required', 'string', 'max:255'],
            'delivery_address.street_address' => ['required', 'string', 'max:255'],
            'delivery_address.contact_number' => ['required', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

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

        $selectedMethod = $validated['payment_method'];

        $xenditConfigured = $this->xenditService->isConfigured();
        $xenditConfigIssue = $this->xenditService->configurationIssue();

        $order = DB::transaction(function () use ($validated, $cartItems, $total, $selectedMethod, $xenditConfigured, $xenditConfigIssue) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $total,
                'status' => 'pending',
                'payment_method' => $selectedMethod,
                'payment_status' => 'pending',
                'payment_amount' => $total,
                'notes' => $validated['notes'] ?? null,
                'pickup_address' => $validated['pickup_address'],
                'delivery_address' => $validated['delivery_address'],
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

            $order->xendit_reference_id = "storix-order-{$order->id}";
            $order->xendit_invoice_id = $this->buildFallbackInvoiceId($order);

            if ($xenditConfigured) {
                $invoiceResponse = $this->xenditService->createInvoice(
                    $this->buildInvoicePayload($order->fresh('user'), $selectedMethod, $total)
                );

                if ($invoiceResponse->successful()) {
                    $invoice = $invoiceResponse->json();

                    $order->fill([
                        'xendit_invoice_id' => $invoice['id'] ?? $order->xendit_invoice_id,
                        'xendit_invoice_url' => $invoice['invoice_url'] ?? null,
                        'xendit_payment_method' => $invoice['payment_method'] ?? $selectedMethod,
                        'xendit_reference_id' => $invoice['external_id'] ?? "storix-order-{$order->id}",
                        'payment_expires_at' => isset($invoice['expiry_date']) ? $invoice['expiry_date'] : null,
                    ]);
                } else {
                    $order->notes = trim(implode(' ', array_filter([
                        $order->notes,
                        'Xendit payment session is not available yet. Order was saved without hosted payment.',
                    ])));
                }
            } else {
                $order->xendit_payment_method = $selectedMethod;
                $issueNote = $xenditConfigIssue === 'public_key_only'
                    ? 'Xendit public key detected. Please configure XENDIT_SECRET_KEY to create real invoices.'
                    : 'Xendit API key is not configured yet. Order was saved in pending payment mode.';

                $order->notes = trim(implode(' ', array_filter([
                    $order->notes,
                    $issueNote,
                ])));
            }

            $order->save();

            Cart::where('user_id', auth()->id())->delete();

            return $order->fresh(['user', 'items.product']);
        });

        $this->notificationService->notifyAdmins($order);
        $this->trackerService->sendOrderCreated($order);

        if ($order->xendit_invoice_url) {
            return redirect()->away($order->xendit_invoice_url);
        }

        return redirect()->route('user.orders.show', $order)->with(
            'success',
            $xenditConfigured
                ? 'Order placed successfully.'
                : ($xenditConfigIssue === 'public_key_only'
                    ? 'Order placed successfully. Xendit public key was detected; use XENDIT_SECRET_KEY for hosted checkout.'
                    : 'Order placed successfully. Xendit is not configured yet, so the order is saved with pending payment.')
        );
    }

    public function show(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $order->load('items.product', 'user');

        return view('user.orders.show', compact('order'));
    }

    public function paymentReturn(Request $request, Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        if (! filled($order->xendit_invoice_id) || ! filled($order->xendit_invoice_url)) {
            return redirect()->route('user.orders.show', $order)->with('error', 'This order does not have an active Xendit payment session.');
        }

        if (! $this->xenditService->isConfigured()) {
            return redirect()->route('user.orders.show', $order)->with('error', 'Xendit is not configured yet. Please contact admin.');
        }

        $syncResult = $this->syncOrderPaymentStatus($order);

        if ($syncResult['result'] === 'paid') {
            return redirect()->route('user.orders.show', $order)->with('success', 'Payment confirmed. Your order is now approved.');
        }

        if ($request->string('status')->lower() === 'success') {
            return redirect()->route('user.orders.show', $order)->with('info', 'Payment was submitted in Xendit. We are still waiting for final confirmation.');
        }

        return redirect()->route('user.orders.show', $order)->with('info', 'Payment is not completed yet in Xendit.');
    }

    public function statusSnapshot(Order $order)
    {
        abort_unless($order->user_id === auth()->id() || auth()->user()->isAdmin(), 403);

        $order->loadMissing('items.product', 'user');

        return response()->json([
            'id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_paid_at' => $order->payment_paid_at?->toIso8601String(),
            'approved_at' => $order->approved_at?->toIso8601String(),
            'xendit_invoice_url' => $order->xendit_invoice_url,
        ]);
    }

    public function cancel(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (! in_array($order->status, ['pending'])) {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->status = 'rejected';
        $order->payment_status = $order->payment_status === 'paid' ? 'paid' : 'failed';
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

    public function confirmPayment(Order $order)
    {
        abort_unless($order->user_id === auth()->id(), 403);

        if (! filled($order->xendit_invoice_id) || ! filled($order->xendit_invoice_url)) {
            return back()->with('error', 'This order does not have an active Xendit payment session.');
        }

        if ($order->payment_status === 'paid') {
            return back()->with('success', 'Payment is already confirmed.');
        }

        if (! $this->xenditService->isConfigured()) {
            return back()->with('error', 'Xendit is not configured yet. Please contact admin.');
        }

        $syncResult = $this->syncOrderPaymentStatus($order);

        if ($syncResult['result'] === 'error') {
            return back()->with('error', 'Unable to verify payment with Xendit right now. Please try again.');
        }

        if ($syncResult['result'] === 'paid') {
            return back()->with('success', 'Payment confirmed. Your order is now approved.');
        }

        return back()->with('info', 'Payment is not completed yet in Xendit. Please finish payment, then confirm again.');
    }

    protected function buildInvoicePayload(Order $order, string $paymentMethod, float $total): array
    {
        $availablePaymentMethods = config("checkout.payment_method_map.{$paymentMethod}", []);

        return array_filter([
            'external_id' => "storix-order-{$order->id}",
            'amount' => $total,
            'description' => "Storix Order #{$order->id}",
            'currency' => 'PHP',
            'payer_email' => $order->user?->email,
            'should_send_email' => true,
            'invoice_duration' => 86400,
            'customer' => [
                'given_names' => $order->user?->name ?? 'Storix Customer',
                'email' => $order->user?->email,
                'mobile_number' => $order->delivery_address['contact_number'] ?? null,
            ],
            'customer_notification_preference' => [
                'invoice_created' => ['email'],
                'invoice_paid' => ['email'],
                'invoice_expired' => ['email'],
            ],
            'success_redirect_url' => route('user.orders.payment-return', ['order' => $order, 'status' => 'success']),
            'failure_redirect_url' => route('user.orders.payment-return', ['order' => $order, 'status' => 'failed']),
            'available_payment_methods' => $availablePaymentMethods,
            'metadata' => [
                'storix_order_id' => $order->id,
                'payment_method_selection' => $paymentMethod,
                'pickup_city' => $order->pickup_address['city_name'] ?? null,
                'delivery_city' => $order->delivery_address['city_name'] ?? null,
            ],
        ], fn ($value) => $value !== null);
    }

    protected function buildFallbackInvoiceId(Order $order): string
    {
        return sprintf('STORIX-XENDIT-%06d', $order->id);
    }

    protected function syncOrderPaymentStatus(Order $order): array
    {
        $invoiceResponse = $this->xenditService->getInvoice($order->xendit_invoice_id);

        if (! $invoiceResponse->successful()) {
            return ['result' => 'error'];
        }

        $invoice = $invoiceResponse->json();
        $status = strtoupper((string) ($invoice['status'] ?? 'PENDING'));

        $paymentStatus = match ($status) {
            'PAID', 'SETTLED' => 'paid',
            'EXPIRED' => 'expired',
            'FAILED' => 'failed',
            default => 'pending',
        };

        $wasApproved = in_array($order->status, ['approved', 'completed'], true);

        $order->fill([
            'status' => $paymentStatus === 'paid' && ! $wasApproved ? 'approved' : $order->status,
            'payment_status' => $paymentStatus,
            'payment_amount' => $invoice['paid_amount'] ?? $invoice['amount'] ?? $order->payment_amount,
            'xendit_payment_method' => $invoice['payment_method'] ?? $order->xendit_payment_method,
            'xendit_reference_id' => $invoice['external_id'] ?? $order->xendit_reference_id,
            'approved_at' => $paymentStatus === 'paid' && $order->approved_at === null ? now() : $order->approved_at,
            'payment_paid_at' => $paymentStatus === 'paid' ? now() : $order->payment_paid_at,
            'payment_expires_at' => isset($invoice['expiry_date']) ? $invoice['expiry_date'] : $order->payment_expires_at,
        ]);
        $order->save();

        $freshOrder = $order->fresh(['items.product', 'user']);
        $this->trackerService->sendOrderStatus($freshOrder);

        if ($paymentStatus === 'paid' && ! $wasApproved) {
            $this->notificationService->notifyUser($freshOrder, 'approved');
        }

        return [
            'result' => $paymentStatus,
            'order' => $freshOrder,
        ];
    }
}
