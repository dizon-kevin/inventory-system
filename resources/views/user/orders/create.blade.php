<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - Storix</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --sw: 240px; --th: 60px;
            --dark: #060a11; --light: #f2f5f3; --card: #ffffff;
            --teal: #00d4aa; --teal-dim: rgba(0,212,170,0.11);
            --tp: #0c1a14; --ts: rgba(12,26,20,0.5); --tm: rgba(12,26,20,0.35);
            --border: rgba(12,26,20,0.08); --st: rgba(220,240,232,0.52); --sab: rgba(0,212,170,0.1);
            --danger: #c2410c;
        }
        html, body { min-height: 100%; font-family: 'Sora', sans-serif; background: var(--light); color: var(--tp); }
        .shell { display: flex; min-height: 100vh; }
        .sidebar { width: var(--sw); flex-shrink: 0; background: var(--dark); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh; z-index: 100; overflow: hidden; }
        .sidebar::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(rgba(0,212,170,0.13) 1px, transparent 1px); background-size: 28px 28px; pointer-events: none; z-index: 0; }
        .sb-brand { position: relative; z-index: 2; display: flex; align-items: center; gap: 10px; padding: 1.4rem 1.4rem 1rem; border-bottom: 1px solid rgba(0,212,170,0.07); }
        .sb-logomark { width: 36px; height: 36px; background: linear-gradient(135deg,#00d4aa,#0077ff); border-radius: 9px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 18px rgba(0,212,170,0.4); flex-shrink: 0; }
        .sb-logomark svg { width: 20px; height: 20px; fill: #060a11; }
        .sb-wordmark { font-family: 'Space Mono', monospace; font-size: 1.15rem; font-weight: 700; color: #e2eeea; letter-spacing: .09em; }
        .sb-wordmark em { font-style: normal; color: #00d4aa; }
        .sb-section { position: relative; z-index: 2; font-size: .62rem; letter-spacing: .12em; text-transform: uppercase; color: rgba(0,212,170,0.32); padding: 1.2rem 1.4rem 0.5rem; font-weight: 600; }
        .sb-nav { position: relative; z-index: 2; flex: 1; padding: 0.4rem 0.75rem; overflow-y: auto; }
        .ni { display: flex; align-items: center; gap: 10px; padding: .62rem .75rem; border-radius: 9px; text-decoration: none; color: var(--st); font-size: .84rem; font-weight: 500; transition: background .18s, color .18s; margin-bottom: 2px; position: relative; }
        .ni.active { background: var(--sab); color: #00d4aa; }
        .ni.active::before { content: ''; position: absolute; left: 0; top: 20%; bottom: 20%; width: 3px; background: #00d4aa; border-radius: 0 3px 3px 0; }
        .ni-icon { width: 18px; height: 18px; color: rgba(180,220,205,0.38); flex-shrink: 0; }
        .sb-divider { height: 1px; background: rgba(0,212,170,0.06); margin: .6rem .5rem; }
        .sb-footer { position: relative; z-index: 2; padding: 1rem 1.1rem; border-top: 1px solid rgba(0,212,170,0.07); }
        .sb-version { font-size: .63rem; letter-spacing: .08em; color: rgba(0,212,170,0.22); font-family: 'Space Mono', monospace; text-align: center; }
        .main { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }
        .topbar { height: var(--th); background: var(--card); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 1.8rem; position: sticky; top: 0; z-index: 50; gap: 1rem; }
        .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: .78rem; color: var(--tm); }
        .breadcrumb a { color: var(--tm); text-decoration: none; }
        .user-trigger { display: flex; align-items: center; gap: 8px; padding: 5px 10px 5px 6px; border: 1px solid var(--border); border-radius: 10px; background: transparent; font-family: 'Sora', sans-serif; }
        .user-avatar { width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg,#00d4aa,#0077ff); display: flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 700; font-family: 'Space Mono', monospace; color: #060a11; }
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }
        .page-header { margin-bottom: 1.4rem; }
        .page-eyebrow { font-size: .75rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: #00a878; margin-bottom: 3px; }
        .page-title { font-size: 1.45rem; font-weight: 700; letter-spacing: -.02em; }
        .page-subtitle { font-size: .82rem; color: var(--tm); margin-top: 2px; }
        .flash { padding: .9rem 1rem; border-radius: 12px; margin-bottom: 1rem; font-size: .82rem; font-weight: 500; }
        .flash-info { background: rgba(59,130,246,.08); border: 1px solid rgba(59,130,246,.15); color: #1d4ed8; }
        .flash-error { background: rgba(220,38,38,.06); border: 1px solid rgba(220,38,38,.15); color: #b91c1c; }
        .checkout-grid { display: grid; grid-template-columns: minmax(0, 1.45fr) 360px; gap: 1.25rem; align-items: start; }
        .stack { display: grid; gap: 1rem; }
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 16px; overflow: hidden; }
        .card-head { padding: 1rem 1.2rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; gap: .75rem; }
        .card-title { font-size: .78rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--tm); display: flex; align-items: center; gap: 8px; }
        .card-title::before { content: ''; width: 3px; height: 14px; border-radius: 2px; background: var(--teal); }
        .card-body { padding: 1.2rem; }
        .address-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .9rem; }
        .field { display: grid; gap: .35rem; }
        .field.full { grid-column: 1 / -1; }
        .field label { font-size: .71rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: rgba(12,26,20,.45); }
        .field input, .field select, .field textarea {
            width: 100%; border: 1px solid var(--border); border-radius: 11px; background: #fbfcfb;
            padding: .82rem .95rem; font: inherit; color: var(--tp);
        }
        .field textarea { min-height: 92px; resize: vertical; }
        .field select:disabled { opacity: .6; cursor: wait; }
        .error-text { font-size: .73rem; color: var(--danger); }
        .address-note { font-size: .78rem; color: var(--tm); margin-bottom: .9rem; }
        .payment-picker { display:grid; gap:.7rem; }
        .payment-current { display:flex; justify-content:space-between; align-items:center; gap:.7rem; padding:.85rem .95rem; border:1px solid var(--border); border-radius:12px; background:#fbfcfb; }
        .payment-name { font-weight: 700; }
        .payment-copy { font-size: .76rem; color: var(--tm); line-height: 1.45; }
        .payment-picker-btn { border:1px solid var(--border); background:#fff; color:var(--tp); border-radius:10px; padding:.72rem .9rem; font:inherit; font-size:.8rem; font-weight:700; cursor:pointer; }
        .item-list { display: grid; gap: .85rem; }
        .item-card { display: flex; align-items: center; justify-content: space-between; gap: .8rem; padding: .9rem 0; border-bottom: 1px solid var(--border); }
        .item-card:last-child { padding-bottom: 0; border-bottom: 0; }
        .item-meta { font-size: .76rem; color: var(--tm); margin-top: 3px; }
        .item-price { font-family: 'Space Mono', monospace; font-weight: 700; white-space: nowrap; }
        .summary-card { position: sticky; top: calc(var(--th) + 1.2rem); }
        .summary-body { padding: 1.2rem; display: grid; gap: .8rem; }
        .summary-row { display: flex; align-items: center; justify-content: space-between; gap: .75rem; font-size: .82rem; color: var(--tm); }
        .summary-row strong { color: var(--tp); font-family: 'Space Mono', monospace; }
        .summary-total { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid var(--border); padding-top: .85rem; }
        .summary-total strong { font-size: 1.2rem; font-family: 'Space Mono', monospace; color: var(--tp); }
        .summary-note { padding: .8rem .9rem; border-radius: 11px; background: var(--teal-dim); border: 1px solid rgba(0,212,170,.18); font-size: .76rem; color: #007a57; line-height: 1.5; }
        .btn-primary, .btn-secondary {
            display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; text-decoration: none; cursor: pointer;
            border-radius: 11px; font: inherit; font-weight: 700; transition: transform .18s, box-shadow .18s, background .18s;
        }
        .btn-primary { border: 0; background: #0a1a15; color: #d8f0e8; padding: .95rem 1rem; }
        .btn-primary:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,168,120,.2); }
        .btn-secondary { border: 1px solid var(--border); color: var(--ts); padding: .82rem 1rem; background: transparent; }
        .btn-primary.loading { pointer-events: none; opacity: .8; }
        .hint { font-size: .72rem; color: var(--tm); }
        .notes-and-confirm { display: grid; grid-template-columns: minmax(0,1fr) 180px; gap: .7rem; align-items: end; }
        .confirm-payment-btn {
            border: 1px solid var(--border);
            border-radius: 11px;
            background: #fff;
            color: var(--tp);
            font: inherit;
            font-size: .82rem;
            font-weight: 700;
            height: 44px;
            cursor: pointer;
        }
        .confirm-payment-btn.active {
            background: #0a1a15;
            color: #d8f0e8;
            border-color: #0a1a15;
        }
        .confirm-payment-hint { font-size: .73rem; color: var(--tm); margin-top: .35rem; }
        .method-modal { position: fixed; inset: 0; background: rgba(6,10,17,.55); display: none; align-items: center; justify-content: center; z-index: 1200; padding: 1rem; }
        .method-modal.open { display: flex; }
        .method-modal-card { width: min(560px, 100%); max-height: 80vh; overflow: hidden; background:#fff; border:1px solid var(--border); border-radius: 16px; display:grid; grid-template-rows:auto 1fr auto; }
        .method-modal-head { padding: .95rem 1.1rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; gap:.6rem; }
        .method-modal-title { font-size:.9rem; font-weight:700; }
        .method-modal-close { border:1px solid var(--border); background:#fff; border-radius:9px; padding:.4rem .6rem; font:inherit; font-size:.74rem; font-weight:700; cursor:pointer; }
        .method-list { padding: .8rem 1rem; overflow:auto; display:grid; gap:.55rem; }
        .method-item { border:1px solid var(--border); background:#fbfcfb; border-radius:12px; padding:.75rem .85rem; text-align:left; cursor:pointer; display:grid; gap:.2rem; }
        .method-item.active { border-color: rgba(0,168,120,.55); box-shadow: 0 8px 20px rgba(0,168,120,.1); background:#fff; }
        .method-modal-foot { padding:.85rem 1rem 1rem; border-top:1px solid var(--border); }
        .method-modal-note { font-size:.74rem; color:var(--tm); }
        .pay-modal { position: fixed; inset: 0; background: rgba(6,10,17,.58); display: none; align-items: flex-end; justify-content: center; z-index: 1300; padding: 0; }
        .pay-modal.open { display: flex; }
        .pay-modal-card { width: min(460px, 100%); background:#fff; border:1px solid var(--border); border-radius: 18px 18px 0 0; overflow:hidden; box-shadow: 0 -14px 44px rgba(6,10,17,.3); transform: translateY(14px); animation: paySheetIn .2s ease-out forwards; }
        @keyframes paySheetIn { to { transform: translateY(0); } }
        .pay-modal-head { padding: .95rem 1rem; border-bottom:1px solid var(--border); display:flex; justify-content:space-between; align-items:center; gap:.6rem; background: linear-gradient(180deg, #031a4a 0%, #082868 100%); color:#fff; }
        .pay-brand { font-size:.95rem; font-weight:700; }
        .pay-chip { font-size:.65rem; letter-spacing:.08em; text-transform:uppercase; padding:.24rem .5rem; border-radius:999px; background:rgba(0,168,120,.12); color:#047857; font-weight:700; }
        .pay-close { border:0; background:transparent; color:#fff; font:inherit; font-size:1.05rem; font-weight:700; cursor:pointer; line-height:1; }
        .pay-progress { height: 4px; background: rgba(6,10,17,.08); }
        .pay-progress-bar { height: 100%; width: 62%; background: linear-gradient(90deg, #00d4aa 0%, #0ea5e9 100%); }
        .pay-modal-body { padding: 0; display:grid; gap:0; max-height: 62vh; overflow:auto; }
        .gateway-summary { padding: .85rem 1rem .9rem; border-bottom:1px solid var(--border); display:grid; gap:.28rem; }
        .gateway-summary-top { display:flex; justify-content:space-between; align-items:center; font-size:.77rem; color:var(--tm); }
        .gateway-total { font-family:'Space Mono', monospace; font-size:1.5rem; font-weight:700; color:var(--tp); }
        .gateway-sub { display:flex; justify-content:space-between; align-items:center; font-size:.74rem; color:var(--tm); }
        .gateway-sub button { border:0; background:transparent; color:#1d4ed8; font:inherit; font-size:.74rem; cursor:pointer; }
        .gateway-block { padding:.85rem 1rem; border-bottom:1px solid var(--border); display:grid; gap:.58rem; }
        .gateway-label { font-size:.8rem; color:var(--tm); font-weight:600; }
        .gateway-item { border:1px solid var(--border); border-radius:12px; background:#fff; padding:.7rem .8rem; display:flex; justify-content:space-between; align-items:center; gap:.7rem; }
        .gateway-item.active { border-color: rgba(37,99,235,.45); box-shadow: 0 8px 18px rgba(37,99,235,.12); }
        .gateway-item-main { display:grid; gap:.22rem; }
        .gateway-item-title { font-size:.95rem; font-weight:700; color:var(--tp); }
        .gateway-item-icons { display:flex; align-items:center; gap:.32rem; font-size:.66rem; color:#475569; }
        .gateway-pill { border:1px solid var(--border); border-radius:999px; padding:.16rem .42rem; background:#f8fafc; }
        .gateway-arrow { font-size:1.05rem; color:#334155; }
        .gateway-qr { padding: .2rem 1rem .95rem; display:grid; gap:.45rem; justify-items:center; }
        .gateway-qr-label { font-size:.74rem; color:var(--tm); }
        .gateway-qr-wrap { width: 180px; height: 180px; border:1px solid var(--border); border-radius:12px; background:#fbfcfb; display:flex; align-items:center; justify-content:center; overflow:hidden; }
        .gateway-qr-wrap img { width:100%; height:100%; object-fit:cover; }
        .pay-modal-foot { padding: 0 1rem 1rem; display:grid; grid-template-columns:1fr 1fr; gap:.6rem; }
        .pay-modal-btn { border-radius: 10px; padding: .75rem .8rem; font: inherit; font-size:.8rem; font-weight:700; cursor:pointer; }
        .pay-modal-btn-confirm { border:0; background:#0a1a15; color:#d8f0e8; }
        .pay-modal-btn-cancel { border:1px solid var(--border); background:#fff; color:var(--tp); }
        .pay-powered { font-size:.68rem; color:var(--tm); text-align:center; padding-bottom: .95rem; }
        @media (min-width: 720px) { .pay-modal { align-items: center; padding: 1rem; } .pay-modal-card { border-radius: 18px; } }
        @media (max-width: 1080px) { .checkout-grid { grid-template-columns: 1fr; } .summary-card { position: static; } }
        @media (max-width: 900px) { .main { margin-left: 0; } .sidebar { display: none; } .content { padding: 1.2rem 1rem 2rem; } }
        @media (max-width: 640px) { .address-grid, .notes-and-confirm { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="shell">
    <aside class="sidebar">
        <div class="sb-brand">
            <div class="sb-logomark"><svg viewBox="0 0 24 24"><path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM17 13h4v4h-4zM13 17h4v4h-4z"/></svg></div>
            <span class="sb-wordmark">STO<em>RIX</em></span>
        </div>
        <p class="sb-section">Shop</p>
        <nav class="sb-nav">
            <a href="{{ route('user.dashboard') }}" class="ni"><svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</a>
            <a href="{{ route('user.products.index') }}" class="ni"><svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>Products</a>
            <a href="{{ route('user.orders.index') }}" class="ni active"><svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>My Orders</a>
            <a href="{{ route('user.cart.index') }}" class="ni"><svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4"/></svg>My Cart</a>
            <div class="sb-divider"></div>
        </nav>
        <div class="sb-footer"><p class="sb-version">STORIX v1.0 · USER</p></div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="breadcrumb">
                <a href="{{ route('user.dashboard') }}">Dashboard</a>
                <span>/</span>
                <a href="{{ route('user.cart.index') }}">Cart</a>
                <span>/</span>
                <span>Checkout</span>
            </div>
            <button class="user-trigger" type="button">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
                <span>{{ auth()->user()->name ?? 'User' }}</span>
            </button>
        </header>

        <main class="content">
            <div class="page-header">
                <p class="page-eyebrow">Checkout Flow</p>
                <h1 class="page-title">Pickup, Delivery, and Payment</h1>
                <p class="page-subtitle">Fill out both addresses, place the order, and continue to the official Xendit hosted checkout where cards, wallets, QR, and other enabled payment methods are handled securely.</p>
            </div>

            @if ($errors->any())
                <div class="flash flash-error">
                    <strong>Please review the checkout form.</strong>
                    <div style="margin-top:.35rem;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(! $xenditConfigured)
                <div class="flash flash-info">
                    <strong>Temporary Manual Confirmation Mode</strong>
                    <div style="margin-top:.35rem;">
                        @if(($xenditConfigIssue ?? null) === 'public_key_only')
                            Xendit public key detected. Use <strong>XENDIT_SECRET_KEY</strong> in `.env` for real hosted checkout and webhook flow.
                        @else
                        Xendit API key is not configured. Set <strong>XENDIT_SECRET_KEY</strong> to enable real hosted checkout and real QR/payment channels.
                        @endif
                    </div>
                </div>
            @endif

            <form action="{{ route('user.orders.store') }}" method="POST" id="checkoutForm">
                @csrf
                <input type="hidden" name="payment_method" id="paymentMethodInput" value="XENDIT">

                <div class="checkout-grid">
                    <div class="stack">
                        <section class="card">
                            <div class="card-head">
                                <p class="card-title">Pickup Address</p>
                                <span class="hint">Loaded from the PSGC API</span>
                            </div>
                            <div class="card-body">
                                <p class="address-note">Choose the Philippine region first, then continue down to barangay. Street address and contact number complete the full pickup point.</p>
                                @include('user.orders.partials.address-form', ['prefix' => 'pickup', 'title' => 'Pickup'])
                            </div>
                        </section>

                        <section class="card">
                            <div class="card-head">
                                <p class="card-title">Delivery Address</p>
                                <span class="hint">Stored with the order and synced to Tracker</span>
                            </div>
                            <div class="card-body">
                                <p class="address-note">The delivery address is also shown to the admin and pushed to the Tracker system together with payment details and order status.</p>
                                @include('user.orders.partials.address-form', ['prefix' => 'delivery', 'title' => 'Delivery'])
                            </div>
                        </section>

                        <section class="card">
                            <div class="card-head">
                                <p class="card-title">Xendit Payment Method</p>
                                <span class="hint">Hosted checkout after order creation</span>
                            </div>
                            <div class="card-body">
                                <div class="payment-picker">
                                    <div class="payment-current">
                                        <div>
                                            <div class="payment-name">Xendit Hosted Checkout</div>
                                            <div class="payment-copy">After you place the order, Storix will redirect you to the official Xendit hosted checkout page where all enabled methods are shown directly.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="field full" style="margin-top:1rem;">
                                    <label for="notes">Order Notes</label>
                                    <textarea id="notes" name="notes" placeholder="Optional delivery or handling notes">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </section>

                        <section class="card">
                            <div class="card-head">
                                <p class="card-title">Order Items</p>
                                <span class="hint">{{ $cartItems->count() }} item{{ $cartItems->count() === 1 ? '' : 's' }}</span>
                            </div>
                            <div class="card-body">
                                <div class="item-list">
                                    @foreach($cartItems as $item)
                                        <div class="item-card">
                                            <div>
                                                <div style="font-weight:700;">{{ $item->product->name }}</div>
                                                <div class="item-meta">Qty {{ $item->quantity }} × PHP {{ number_format($item->product->price, 2) }}</div>
                                            </div>
                                            <div class="item-price">PHP {{ number_format($item->quantity * $item->product->price, 2) }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="card summary-card">
                        <div class="card-head">
                            <p class="card-title">Order Summary</p>
                        </div>
                        <div class="summary-body">
                            <div class="summary-row"><span>Subtotal</span><strong>PHP {{ number_format($total, 2) }}</strong></div>
                            <div class="summary-row"><span>Payment Flow</span><strong>Xendit</strong></div>
                            <div class="summary-row"><span>Sync Target</span><strong>Tracker API</strong></div>
                            <div class="summary-total">
                                <span>Total Due</span>
                                <strong>PHP {{ number_format($total, 2) }}</strong>
                            </div>
                            <div class="summary-note">
                                After placing the order, Storix will redirect you to Xendit hosted checkout for the actual payment step. The order page will stay linked to the same Xendit invoice for retries and status refresh.
                            </div>
                            <button type="submit" class="btn-primary" id="placeOrderButton">Place Order and Pay with Xendit</button>
                            <a href="{{ route('user.cart.index') }}" class="btn-secondary">Back to Cart</a>
                        </div>
                    </aside>
                </div>
            </form>
        </main>
    </div>
</div>

<script>
    const checkoutForm = document.getElementById('checkoutForm');
    const placeOrderButton = document.getElementById('placeOrderButton');
    const addressEndpoints = {
        regions: @json(route('user.checkout.address-data.regions')),
        provinces: @json(route('user.checkout.address-data.provinces', ['regionCode' => '__REGION__'])),
        cities: @json(route('user.checkout.address-data.cities', ['regionCode' => '__REGION__'])),
        barangays: @json(route('user.checkout.address-data.barangays', ['cityCode' => '__CITY__'])),
    };

    const buildUrl = (template, replacements = {}) => Object.entries(replacements).reduce(
        (url, [key, value]) => url.replace(key, encodeURIComponent(value)),
        template
    );

    const setOptions = (select, items, placeholder, selectedCode = '', allowEmpty = true) => {
        const normalizedSelectedCode = String(selectedCode || '');
        select.innerHTML = '';

        if (allowEmpty) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = placeholder;
            select.appendChild(option);
        }

        items.forEach((item) => {
            const option = document.createElement('option');
            option.value = item.code;
            option.textContent = item.name;
            option.selected = normalizedSelectedCode !== '' && item.code === normalizedSelectedCode;
            select.appendChild(option);
        });
    };

    const syncHiddenName = (select, hiddenInput) => {
        if (!hiddenInput) {
            return;
        }

        hiddenInput.value = select.value ? (select.options[select.selectedIndex]?.text || '') : '';
    };

    const setLoadingState = (select, placeholder) => {
        select.disabled = true;
        select.innerHTML = `<option value="">${placeholder}</option>`;
    };

    const setupAddressGrid = async (grid) => {
        const regionSelect = grid.querySelector('[data-role="region"]');
        const provinceSelect = grid.querySelector('[data-role="province"]');
        const citySelect = grid.querySelector('[data-role="city"]');
        const barangaySelect = grid.querySelector('[data-role="barangay"]');
        const regionNameInput = grid.querySelector('[data-role="region-name"]');
        const provinceNameInput = grid.querySelector('[data-role="province-name"]');
        const cityNameInput = grid.querySelector('[data-role="city-name"]');
        const barangayNameInput = grid.querySelector('[data-role="barangay-name"]');

        const fetchItems = async (url) => {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error(`Request failed with status ${response.status}`);
            }

            return response.json();
        };

        const resetProvince = () => {
            provinceSelect.disabled = true;
            provinceNameInput.value = '';
            setOptions(provinceSelect, [], 'Select a province');
        };

        const resetCity = () => {
            citySelect.disabled = true;
            cityNameInput.value = '';
            setOptions(citySelect, [], 'Select a city or municipality');
        };

        const resetBarangay = () => {
            barangaySelect.disabled = true;
            barangayNameInput.value = '';
            setOptions(barangaySelect, [], 'Select a barangay');
        };

        const loadBarangays = async (selectedCode = barangaySelect.dataset.selectedCode || '') => {
            resetBarangay();

            if (!citySelect.value) {
                return;
            }

            setLoadingState(barangaySelect, 'Loading barangays...');

            try {
                const items = await fetchItems(buildUrl(addressEndpoints.barangays, { '__CITY__': citySelect.value }));
                setOptions(barangaySelect, items, 'Select a barangay', selectedCode);
                barangaySelect.disabled = false;
                syncHiddenName(barangaySelect, barangayNameInput);
            } catch (error) {
                setOptions(barangaySelect, [], 'Unable to load barangays');
                console.error(error);
            }
        };

        const loadCities = async (selectedCode = citySelect.dataset.selectedCode || '') => {
            resetCity();
            resetBarangay();

            if (!regionSelect.value) {
                return;
            }

            setLoadingState(citySelect, 'Loading cities and municipalities...');

            try {
                let url = buildUrl(addressEndpoints.cities, { '__REGION__': regionSelect.value });

                if (provinceSelect.value) {
                    url += `?province=${encodeURIComponent(provinceSelect.value)}`;
                }

                const items = await fetchItems(url);
                setOptions(citySelect, items, 'Select a city or municipality', selectedCode);
                citySelect.disabled = false;
                syncHiddenName(citySelect, cityNameInput);

                if (selectedCode || citySelect.value) {
                    await loadBarangays();
                }
            } catch (error) {
                setOptions(citySelect, [], 'Unable to load cities and municipalities');
                console.error(error);
            }
        };

        const loadProvinces = async (selectedCode = provinceSelect.dataset.selectedCode || '') => {
            resetProvince();
            resetCity();
            resetBarangay();

            if (!regionSelect.value) {
                return;
            }

            setLoadingState(provinceSelect, 'Loading provinces...');

            try {
                const items = await fetchItems(buildUrl(addressEndpoints.provinces, { '__REGION__': regionSelect.value }));

                if (items.length === 0) {
                    provinceSelect.disabled = false;
                    setOptions(provinceSelect, [], 'No province required');
                    syncHiddenName(provinceSelect, provinceNameInput);
                    await loadCities();
                    return;
                }

                setOptions(provinceSelect, items, 'Select a province', selectedCode);
                provinceSelect.disabled = false;
                syncHiddenName(provinceSelect, provinceNameInput);

                if (selectedCode || provinceSelect.value) {
                    await loadCities();
                }
            } catch (error) {
                setOptions(provinceSelect, [], 'Unable to load provinces');
                console.error(error);
            }
        };

        regionSelect.addEventListener('change', async () => {
            syncHiddenName(regionSelect, regionNameInput);
            provinceSelect.dataset.selectedCode = '';
            citySelect.dataset.selectedCode = '';
            barangaySelect.dataset.selectedCode = '';
            await loadProvinces('');
        });

        provinceSelect.addEventListener('change', async () => {
            syncHiddenName(provinceSelect, provinceNameInput);
            citySelect.dataset.selectedCode = '';
            barangaySelect.dataset.selectedCode = '';
            await loadCities('');
        });

        citySelect.addEventListener('change', async () => {
            syncHiddenName(citySelect, cityNameInput);
            barangaySelect.dataset.selectedCode = '';
            await loadBarangays('');
        });

        barangaySelect.addEventListener('change', () => {
            syncHiddenName(barangaySelect, barangayNameInput);
        });

        setLoadingState(regionSelect, 'Loading regions...');

        try {
            const regions = await fetchItems(addressEndpoints.regions);
            setOptions(regionSelect, regions, 'Select a region', regionSelect.dataset.selectedCode);
            regionSelect.disabled = false;
            syncHiddenName(regionSelect, regionNameInput);

            if (regionSelect.dataset.selectedCode || regionSelect.value) {
                await loadProvinces();
            }
        } catch (error) {
            setOptions(regionSelect, [], 'Unable to load regions');
            console.error(error);
        }
    };

    checkoutForm.addEventListener('submit', function () {
        placeOrderButton.classList.add('loading');
    });

    document.querySelectorAll('[data-address-prefix]').forEach((grid) => {
        setupAddressGrid(grid);
    });
</script>
</body>
</html>
