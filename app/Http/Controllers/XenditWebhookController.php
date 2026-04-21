<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\NotificationService;
use App\Services\TrackerService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class XenditWebhookController extends Controller
{
    public function __construct(
        protected TrackerService $trackerService,
        protected NotificationService $notificationService
    )
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
            'EXPIRED' => 'expired',
            'FAILED' => 'failed',
            default => 'pending',
        };

        $previousOrderStatus = $order->status;

        $order->fill([
            'status' => $paymentStatus === 'paid' && ! in_array($order->status, ['approved', 'completed'], true)
                ? 'approved'
                : $order->status,
            'payment_status' => $paymentStatus,
            'payment_amount' => $request->input('paid_amount', $request->input('amount', $order->payment_amount)),
            'xendit_payment_method' => $request->input('payment_method', $order->xendit_payment_method),
            'xendit_invoice_id' => $invoiceId !== '' ? $invoiceId : $order->xendit_invoice_id,
            'xendit_reference_id' => $externalId !== '' ? $externalId : $order->xendit_reference_id,
            'approved_at' => $paymentStatus === 'paid' && $order->approved_at === null
                ? now()
                : $order->approved_at,
            'payment_paid_at' => in_array($paymentStatus, ['paid'], true)
                ? now()
                : $order->payment_paid_at,
            'payment_expires_at' => $request->filled('expiry_date')
                ? $request->date('expiry_date')
                : $order->payment_expires_at,
        ]);
        $order->save();

        $freshOrder = $order->fresh(['items.product', 'user']);

        if ($previousOrderStatus !== $freshOrder->status && in_array($freshOrder->status, ['approved', 'completed'], true)) {
            $this->notificationService->notifyUser($freshOrder, $freshOrder->status);
        }

        $this->trackerService->sendOrderStatus($freshOrder);

        return response('', Response::HTTP_NO_CONTENT);
    }
}
