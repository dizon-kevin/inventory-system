<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order #{{ $order->id }} - Storix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#f2f5f3; --card:#fff; --text:#0c1a14; --muted:rgba(12,26,20,.48); --border:rgba(12,26,20,.08); --teal:#00d4aa; --dark:#060a11; }
        body { font-family: 'Sora', sans-serif; background: var(--bg); color: var(--text); }
        .wrap { max-width: 1180px; margin: 0 auto; padding: 1.5rem 1rem 2rem; }
        .head { display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1.2rem; }
        .eyebrow { font-size:.76rem; text-transform:uppercase; letter-spacing:.1em; color:#00a878; font-weight:700; }
        .title { font-size:1.5rem; font-weight:700; margin-top:.2rem; }
        .subtitle { font-size:.82rem; color:var(--muted); margin-top:.2rem; }
        .pill { display:inline-flex; align-items:center; gap:6px; padding:.35rem .8rem; border-radius:999px; font-size:.72rem; font-weight:700; }
        .pill.pending { background:rgba(245,158,11,.12); color:#b45309; }
        .pill.approved { background:rgba(59,130,246,.12); color:#1d4ed8; }
        .pill.completed { background:rgba(0,168,120,.12); color:#047857; }
        .pill.rejected { background:rgba(220,38,38,.1); color:#b91c1c; }
        .pill.unpaid { background:rgba(107,114,128,.1); color:#374151; }
        .pill.paid { background:rgba(0,168,120,.12); color:#047857; }
        .pill.failed { background:rgba(220,38,38,.1); color:#b91c1c; }
        .grid { display:grid; grid-template-columns: minmax(0, 1.35fr) 360px; gap:1rem; align-items:start; }
        .card { background:var(--card); border:1px solid var(--border); border-radius:16px; overflow:hidden; }
        .card-head { padding:1rem 1.2rem; border-bottom:1px solid var(--border); }
        .card-title { font-size:.78rem; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); font-weight:700; }
        .card-body { padding:1.2rem; }
        .detail-grid { display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:.9rem; }
        .detail { display:grid; gap:.25rem; }
        .detail label { font-size:.69rem; text-transform:uppercase; letter-spacing:.08em; color:rgba(12,26,20,.38); font-weight:700; }
        .detail div { font-size:.9rem; }
        .mono { font-family:'Space Mono', monospace; }
        .address-box { padding:1rem; border:1px solid var(--border); border-radius:14px; background:#fbfcfb; }
        .address-box + .address-box { margin-top:.85rem; }
        .address-box h3 { font-size:.84rem; font-weight:700; margin-bottom:.45rem; }
        .address-box p { font-size:.84rem; color:var(--muted); line-height:1.6; }
        .item-row { display:flex; justify-content:space-between; gap:.8rem; padding:.9rem 0; border-bottom:1px solid var(--border); }
        .item-row:last-child { border-bottom:0; padding-bottom:0; }
        .item-name { font-weight:700; }
        .item-meta { font-size:.78rem; color:var(--muted); margin-top:.25rem; }
        .sidebar-card { position:sticky; top:1rem; }
        .actions { display:grid; gap:.7rem; }
        .btn { display:flex; align-items:center; justify-content:center; text-decoration:none; border-radius:12px; padding:.85rem 1rem; font-weight:700; font-size:.86rem; }
        .btn-primary { background:#0a1a15; color:#d8f0e8; border:0; }
        .btn-secondary { border:1px solid var(--border); color:var(--text); background:#fff; }
        .btn-danger { width:100%; background:rgba(220,38,38,.08); color:#b91c1c; border:1px solid rgba(220,38,38,.12); cursor:pointer; }
        .flash { margin-bottom:1rem; padding:.9rem 1rem; border-radius:12px; font-size:.82rem; font-weight:600; }
        .flash-success { background:rgba(0,168,120,.1); color:#047857; border:1px solid rgba(0,168,120,.15); }
        .flash-info { background:rgba(59,130,246,.08); color:#1d4ed8; border:1px solid rgba(59,130,246,.14); }
        .flash-error { background:rgba(220,38,38,.08); color:#b91c1c; border:1px solid rgba(220,38,38,.12); }
        @media (max-width: 980px) { .grid { grid-template-columns: 1fr; } .sidebar-card { position:static; } }
        @media (max-width: 640px) { .detail-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    @php
        $paymentStatusLabel = match ($order->payment_status) {
            'pending' => 'Pending Payment',
            'paid' => 'Paid Payment',
            'failed' => 'Failed Payment',
            default => ucfirst($order->payment_status) . ' Payment',
        };

        $shouldAutoTrackPayment = filled($order->xendit_invoice_url) && ! in_array($order->payment_status, ['paid', 'failed', 'expired'], true);
    @endphp
    <div class="wrap">
        <div class="head">
            <div>
                <div class="eyebrow">Order Details</div>
                <div class="title">Order #{{ $order->id }}</div>
                <div class="subtitle">Placed {{ $order->placed_at?->format('M d, Y h:i A') ?? $order->created_at->format('M d, Y h:i A') }}</div>
            </div>
            <div style="display:flex;gap:.6rem;align-items:flex-start;flex-wrap:wrap;">
                <span class="pill {{ $order->status }}" data-order-status-pill>{{ ucfirst($order->status) }}</span>
                <span class="pill {{ $order->payment_status }}" data-payment-status-pill>{{ $paymentStatusLabel }}</span>
            </div>
        </div>

        @if(session('success')) <div class="flash flash-success">{{ session('success') }}</div> @endif
        @if(session('info')) <div class="flash flash-info">{{ session('info') }}</div> @endif
        @if(session('error')) <div class="flash flash-error">{{ session('error') }}</div> @endif

        <div class="grid">
            <div style="display:grid;gap:1rem;">
                @if($order->xendit_invoice_url)
                    <div
                        class="flash {{ $order->payment_status === 'paid' ? 'flash-success' : 'flash-info' }}"
                        data-payment-banner
                    >
                        {{ $order->payment_status === 'paid'
                            ? 'Xendit confirmed your payment and Storix approved the order automatically.'
                            : 'This order stays connected to Xendit hosted checkout. Complete the payment on Xendit and Storix will approve it automatically once Xendit sends the confirmation.' }}
                    </div>
                @endif

                <section class="card">
                    <div class="card-head"><div class="card-title">Payment Summary</div></div>
                    <div class="card-body">
                        <div class="detail-grid">
                            <div class="detail"><label>Payment Method</label><div>{{ config('checkout.payment_methods.' . $order->payment_method, $order->payment_method ?: 'Not set') }}</div></div>
                            <div class="detail"><label>Payment Amount</label><div class="mono">PHP {{ number_format((float) $order->payment_amount, 2) }}</div></div>
                            <div class="detail"><label>Xendit Invoice ID</label><div class="mono">{{ $order->xendit_invoice_id ?: 'Not available' }}</div></div>
                            <div class="detail"><label>Xendit Channel</label><div>{{ $order->xendit_payment_method ?: 'Waiting for confirmation' }}</div></div>
                            <div class="detail"><label>Paid At</label><div data-payment-paid-at>{{ $order->payment_paid_at?->format('M d, Y h:i A') ?? 'Waiting for payment confirmation' }}</div></div>
                            <div class="detail"><label>Payment Link</label><div>@if($order->xendit_invoice_url)<a href="{{ $order->xendit_invoice_url }}" target="_blank" rel="noopener">Open Xendit hosted checkout</a>@else Not available @endif</div></div>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-head"><div class="card-title">Pickup and Delivery</div></div>
                    <div class="card-body">
                        <div class="address-box">
                            <h3>Pickup Address</h3>
                            <p>{{ collect([$order->pickup_address['street_address'] ?? null, $order->pickup_address['barangay_name'] ?? null, $order->pickup_address['city_name'] ?? null, $order->pickup_address['province_name'] ?? null, $order->pickup_address['region_name'] ?? null])->filter()->implode(', ') ?: 'Not available' }}</p>
                            <p style="margin-top:.35rem;">Contact: {{ $order->pickup_address['contact_number'] ?? 'Not available' }}</p>
                        </div>
                        <div class="address-box">
                            <h3>Delivery Address</h3>
                            <p>{{ collect([$order->delivery_address['street_address'] ?? null, $order->delivery_address['barangay_name'] ?? null, $order->delivery_address['city_name'] ?? null, $order->delivery_address['province_name'] ?? null, $order->delivery_address['region_name'] ?? null])->filter()->implode(', ') ?: 'Not available' }}</p>
                            <p style="margin-top:.35rem;">Contact: {{ $order->delivery_address['contact_number'] ?? 'Not available' }}</p>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-head"><div class="card-title">Order Items</div></div>
                    <div class="card-body">
                        @foreach($order->items as $item)
                            <div class="item-row">
                                <div>
                                    <div class="item-name">{{ $item->product->name }}</div>
                                    <div class="item-meta">Qty {{ $item->quantity }} × PHP {{ number_format($item->unit_price, 2) }}</div>
                                </div>
                                <div class="mono">PHP {{ number_format($item->total_price, 2) }}</div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <aside class="card sidebar-card">
                <div class="card-head"><div class="card-title">Order Meta</div></div>
                <div class="card-body">
                    <div class="detail" style="margin-bottom:.8rem;"><label>Status</label><div data-order-status-text>{{ ucfirst($order->status) }}</div></div>
                    <div class="detail" style="margin-bottom:.8rem;"><label>Total</label><div class="mono">PHP {{ number_format($order->total_price, 2) }}</div></div>
                    <div class="detail" style="margin-bottom:.8rem;"><label>Notes</label><div>{{ $order->notes ?: 'No notes added.' }}</div></div>
                    <div class="actions" style="margin-top:1rem;">
                        <a href="{{ route('user.orders.track', $order) }}" class="btn btn-primary">Track Order</a>
                        @if($order->xendit_invoice_url && $order->payment_status !== 'paid')
                            <a href="{{ $order->xendit_invoice_url }}" target="_blank" rel="noopener" class="btn btn-primary">Pay with Xendit</a>
                        @endif
                        @if($order->status === 'pending')
                            <form method="POST" action="{{ route('user.orders.cancel', $order) }}">
                                @csrf
                                <button type="submit" class="btn btn-danger">Cancel Order</button>
                            </form>
                        @endif
                        <a href="{{ route('user.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    @if($shouldAutoTrackPayment)
        <script>
            (() => {
                const endpoint = @json(route('user.orders.status-snapshot', $order));
                const initialStatus = @json($order->status);
                const initialPaymentStatus = @json($order->payment_status);
                const statusPill = document.querySelector('[data-order-status-pill]');
                const paymentPill = document.querySelector('[data-payment-status-pill]');
                const statusText = document.querySelector('[data-order-status-text]');
                const paidAtText = document.querySelector('[data-payment-paid-at]');
                const paymentBanner = document.querySelector('[data-payment-banner]');

                let currentStatus = initialStatus;
                let currentPaymentStatus = initialPaymentStatus;
                let isPolling = false;

                const paymentStatusLabel = (status) => {
                    switch (status) {
                        case 'pending':
                            return 'Pending Payment';
                        case 'paid':
                            return 'Paid Payment';
                        case 'failed':
                            return 'Failed Payment';
                        case 'expired':
                            return 'Expired Payment';
                        default:
                            return `${status.charAt(0).toUpperCase()}${status.slice(1)} Payment`;
                    }
                };

                const titleCase = (value) => `${value.charAt(0).toUpperCase()}${value.slice(1)}`;

                const formatDate = (value) => {
                    if (! value) {
                        return 'Waiting for payment confirmation';
                    }

                    return new Intl.DateTimeFormat('en-US', {
                        month: 'short',
                        day: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true,
                    }).format(new Date(value));
                };

                const updatePill = (element, nextClass, nextText, previousClass) => {
                    if (! element) {
                        return;
                    }

                    if (previousClass) {
                        element.classList.remove(previousClass);
                    }

                    element.classList.add(nextClass);
                    element.textContent = nextText;
                };

                const updateUi = (payload) => {
                    updatePill(statusPill, payload.status, titleCase(payload.status), currentStatus);
                    updatePill(paymentPill, payload.payment_status, paymentStatusLabel(payload.payment_status), currentPaymentStatus);

                    if (statusText) {
                        statusText.textContent = titleCase(payload.status);
                    }

                    if (paidAtText) {
                        paidAtText.textContent = formatDate(payload.payment_paid_at);
                    }

                        if (paymentBanner) {
                        paymentBanner.classList.remove('flash-success', 'flash-info');
                        if (payload.payment_status === 'paid') {
                            paymentBanner.classList.add('flash-success');
                            paymentBanner.textContent = 'Xendit confirmed your payment and Storix approved the order automatically.';
                        } else {
                            paymentBanner.classList.add('flash-info');
                            paymentBanner.textContent = 'This order stays connected to Xendit hosted checkout. Complete the payment on Xendit and Storix will approve it automatically once Xendit sends the confirmation.';
                        }
                    }

                    currentStatus = payload.status;
                    currentPaymentStatus = payload.payment_status;
                };

                const shouldStopPolling = (payload) => ['paid', 'failed', 'expired'].includes(payload.payment_status);

                const poll = async () => {
                    if (isPolling) {
                        return;
                    }

                    isPolling = true;

                    try {
                        const response = await fetch(endpoint, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            cache: 'no-store',
                        });

                        if (! response.ok) {
                            return;
                        }

                        const payload = await response.json();

                        if (payload.status !== currentStatus || payload.payment_status !== currentPaymentStatus || payload.payment_paid_at) {
                            updateUi(payload);
                        }

                        if (shouldStopPolling(payload)) {
                            clearInterval(intervalId);
                            setTimeout(() => window.location.reload(), 1200);
                        }
                    } catch (error) {
                        console.debug('Auto payment status polling failed.', error);
                    } finally {
                        isPolling = false;
                    }
                };

                const intervalId = window.setInterval(poll, 5000);

                document.addEventListener('visibilitychange', () => {
                    if (document.visibilityState === 'visible') {
                        poll();
                    }
                });
            })();
        </script>
    @endif
</body>
</html>
