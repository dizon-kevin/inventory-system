<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\TrackerService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XenditWebhookController extends Controller
{
    public function __construct(protected TrackerService $trackerService)
    {
    }

    public function __invoke(Request $request): Response
    {
        $expectedToken = (string) config('services.xendit.webhook_token');

        if ($expectedToken !== '' && $request->header('x-callback-token') !== $expectedToken) {
            abort(Response::HTTP_FORBIDDEN, 'Invalid Xendit callback token.');
        }

        $invoiceId = (string) $request->input('id');
        $externalId = (string) $request->input('external_id');

        $order = Order::query()
            ->when($invoiceId !== '', fn ($query) => $query->where('xendit_invoice_id', $invoiceId))
            ->when($invoiceId === '' && $externalId !== '', fn ($query) => $query->where('xendit_reference_id', $externalId))
            ->first();

        if (! $order) {
            return response('', Response::HTTP_NO_CONTENT);
        }

        $status = strtoupper((string) $request->input('status', 'PENDING'));

        $paymentStatus = match ($status) {
            'PAID', 'SETTLED' => 'paid',
            'EXPIRED', 'FAILED' => 'failed',
            default => 'pending',
        };

        $order->fill([
            'payment_status' => $paymentStatus,
            'payment_amount' => $request->input('paid_amount', $request->input('amount', $order->payment_amount)),
            'xendit_payment_method' => $request->input('payment_method', $order->xendit_payment_method),
            'xendit_invoice_id' => $invoiceId !== '' ? $invoiceId : $order->xendit_invoice_id,
            'payment_paid_at' => in_array($paymentStatus, ['paid'], true)
                ? now()
                : $order->payment_paid_at,
            'payment_expires_at' => $request->filled('expiry_date')
                ? $request->date('expiry_date')
                : $order->payment_expires_at,
        ]);
        $order->save();

        $this->trackerService->sendOrderStatus($order->fresh(['items.product', 'user']));

        return response('', Response::HTTP_NO_CONTENT);
    }
}
