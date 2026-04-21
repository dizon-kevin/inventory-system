<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Order #{{ $order->id }} - Storix</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root { --bg:#f2f5f3; --card:#fff; --text:#0c1a14; --muted:rgba(12,26,20,.48); --border:rgba(12,26,20,.08); --teal:#00d4aa; }
        body { font-family:'Sora',sans-serif; background:var(--bg); color:var(--text); }
        .wrap { max-width:1220px; margin:0 auto; padding:1.5rem 1rem 2rem; }
        .head { display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap; margin-bottom:1rem; }
        .eyebrow { font-size:.76rem; font-weight:700; color:#00a878; text-transform:uppercase; letter-spacing:.1em; }
        .title { font-size:1.55rem; font-weight:700; margin-top:.2rem; }
        .subtitle { color:var(--muted); font-size:.83rem; margin-top:.2rem; }
        .grid { display:grid; grid-template-columns:minmax(0,1.4fr) 370px; gap:1rem; align-items:start; }
        .card { background:var(--card); border:1px solid var(--border); border-radius:16px; overflow:hidden; }
        .card-head { padding:1rem 1.2rem; border-bottom:1px solid var(--border); }
        .card-title { font-size:.78rem; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); font-weight:700; }
        .card-body { padding:1.2rem; }
        .detail-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:.9rem; }
        .detail { display:grid; gap:.25rem; }
        .detail label { font-size:.69rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:rgba(12,26,20,.38); }
        .detail div { font-size:.9rem; }
        .mono { font-family:'Space Mono',monospace; }
        .pill { display:inline-flex; align-items:center; gap:6px; padding:.35rem .8rem; border-radius:999px; font-size:.72rem; font-weight:700; }
        .pill.pending { background:rgba(245,158,11,.12); color:#b45309; }
        .pill.approved { background:rgba(59,130,246,.12); color:#1d4ed8; }
        .pill.completed { background:rgba(0,168,120,.12); color:#047857; }
        .pill.rejected { background:rgba(220,38,38,.1); color:#b91c1c; }
        .pill.unpaid { background:rgba(107,114,128,.1); color:#374151; }
        .pill.paid { background:rgba(0,168,120,.12); color:#047857; }
        .pill.failed { background:rgba(220,38,38,.1); color:#b91c1c; }
        .address-box { padding:1rem; border:1px solid var(--border); border-radius:14px; background:#fbfcfb; }
        .address-box + .address-box { margin-top:.85rem; }
        .address-box h3 { font-size:.84rem; font-weight:700; margin-bottom:.45rem; }
        .address-box p { font-size:.84rem; color:var(--muted); line-height:1.6; }
        .item-row { display:flex; justify-content:space-between; gap:.8rem; padding:.9rem 0; border-bottom:1px solid var(--border); }
        .item-row:last-child { border-bottom:0; padding-bottom:0; }
        .item-name { font-weight:700; }
        .item-meta { font-size:.78rem; color:var(--muted); margin-top:.25rem; }
        .sidebar-card { position:sticky; top:1rem; }
        .field { display:grid; gap:.35rem; margin-bottom:.9rem; }
        .field label { font-size:.69rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:rgba(12,26,20,.38); }
        .field select { width:100%; border:1px solid var(--border); border-radius:12px; padding:.82rem .9rem; font:inherit; background:#fbfcfb; }
        .btn { display:flex; align-items:center; justify-content:center; width:100%; border-radius:12px; padding:.88rem 1rem; text-decoration:none; font-weight:700; font-size:.86rem; cursor:pointer; }
        .btn-primary { border:0; background:#0a1a15; color:#d8f0e8; }
        .btn-secondary { border:1px solid var(--border); background:#fff; color:var(--text); }
        .flash { margin-bottom:1rem; padding:.9rem 1rem; border-radius:12px; font-size:.82rem; font-weight:600; }
        .flash-success { background:rgba(0,168,120,.1); color:#047857; border:1px solid rgba(0,168,120,.15); }
        .flash-info { background:rgba(59,130,246,.08); color:#1d4ed8; border:1px solid rgba(59,130,246,.14); }
        .flash-error { background:rgba(220,38,38,.08); color:#b91c1c; border:1px solid rgba(220,38,38,.12); }
        @media (max-width:980px) { .grid { grid-template-columns:1fr; } .sidebar-card { position:static; } }
        @media (max-width:640px) { .detail-grid { grid-template-columns:1fr; } }
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
    @endphp
    <div class="wrap">
        <div class="head">
            <div>
                <div class="eyebrow">Admin Order Detail</div>
                <div class="title">Order #{{ $order->id }}</div>
                <div class="subtitle">Customer {{ $order->user->name }} · {{ $order->user->email }}</div>
            </div>
            <div style="display:flex;gap:.6rem;align-items:flex-start;flex-wrap:wrap;">
                <span class="pill {{ $order->status }}">{{ ucfirst($order->status) }}</span>
                <span class="pill {{ $order->payment_status }}">{{ $paymentStatusLabel }}</span>
            </div>
        </div>

        @if(session('success')) <div class="flash flash-success">{{ session('success') }}</div> @endif
        @if(session('error')) <div class="flash flash-error">{{ session('error') }}</div> @endif

        <div class="grid">
            <div style="display:grid;gap:1rem;">
                <section class="card">
                    <div class="card-head"><div class="card-title">Payment and Order Meta</div></div>
                    <div class="card-body">
                        <div class="detail-grid">
                            <div class="detail"><label>Placed At</label><div>{{ $order->placed_at?->format('M d, Y h:i A') ?? 'Not available' }}</div></div>
                            <div class="detail"><label>Total Payment</label><div class="mono">PHP {{ number_format((float) $order->payment_amount, 2) }}</div></div>
                            <div class="detail"><label>Payment Method</label><div>{{ config('checkout.payment_methods.' . $order->payment_method, $order->payment_method ?: 'Not set') }}</div></div>
                            <div class="detail"><label>Payment Status</label><div>{{ str_replace(' Payment', '', $paymentStatusLabel) }}</div></div>
                            <div class="detail"><label>Xendit Invoice ID</label><div class="mono">{{ $order->xendit_invoice_id ?: 'Not available' }}</div></div>
                            <div class="detail"><label>Xendit Channel</label><div>{{ $order->xendit_payment_method ?: 'Waiting for confirmation' }}</div></div>
                            <div class="detail"><label>Paid At</label><div>{{ $order->payment_paid_at?->format('M d, Y h:i A') ?? 'Waiting for payment confirmation' }}</div></div>
                            <div class="detail"><label>Invoice Link</label><div>@if($order->xendit_invoice_url)<a href="{{ $order->xendit_invoice_url }}" target="_blank" rel="noopener">Open Xendit page</a>@else Not available @endif</div></div>
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
                <div class="card-head"><div class="card-title">Update Status</div></div>
                <div class="card-body">
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="field">
                            <label for="status">Order Status</label>
                            <select id="status" name="status">
                                @foreach(['pending', 'approved', 'rejected', 'completed'] as $status)
                                    <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </form>

                    <div style="display:grid;gap:.7rem;margin-top:1rem;">
                        <div class="flash {{ $order->payment_status === 'paid' ? 'flash-success' : 'flash-info' }}" style="margin-bottom:0;">
                            {{ $order->payment_status === 'paid'
                                ? 'Payment was confirmed through Xendit by the customer and already synced.'
                                : 'Payment confirmation is handled in customer Xendit checkout. Admin can still update order status after payment is confirmed.' }}
                        </div>
                        <form action="{{ route('admin.orders.resync-tracker', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-secondary">Resync to Tracker</button>
                        </form>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Back to Orders</a>
                        @if($order->xendit_invoice_url)
                            <a href="{{ $order->xendit_invoice_url }}" target="_blank" rel="noopener" class="btn btn-secondary">Open Payment Page</a>
                        @endif
                    </div>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
