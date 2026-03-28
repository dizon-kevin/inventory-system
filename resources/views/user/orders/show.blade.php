{{-- resources/views/user/orders/show.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order #{{ $order->id }} — Storix</title>

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
            --border: rgba(12,26,20,0.08);
            --st: rgba(220,240,232,0.52); --sab: rgba(0,212,170,0.1);
        }

        html, body { height: 100%; font-family: 'Sora', sans-serif; background: var(--light); color: var(--tp); }
        .shell { display: flex; min-height: 100vh; }

        /* ════ SIDEBAR ════ */
        .sidebar { width: var(--sw); flex-shrink: 0; background: var(--dark); display: flex; flex-direction: column; position: fixed; top: 0; left: 0; height: 100vh; z-index: 100; transition: transform 0.28s cubic-bezier(0.22,1,0.36,1); overflow: hidden; }
        .sidebar::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(rgba(0,212,170,0.13) 1px, transparent 1px); background-size: 28px 28px; pointer-events: none; z-index: 0; }
        .sidebar::after { content: ''; position: absolute; width: 280px; height: 280px; background: radial-gradient(circle, rgba(0,212,170,0.16), transparent 65%); top: -60px; left: -80px; border-radius: 50%; pointer-events: none; z-index: 0; animation: glowPulse 10s ease-in-out infinite alternate; }
        @keyframes glowPulse { 0%{opacity:.6;transform:scale(1)} 100%{opacity:1;transform:scale(1.2)} }
        .sb-brand { position: relative; z-index: 2; display: flex; align-items: center; gap: 10px; padding: 1.4rem 1.4rem 1rem; border-bottom: 1px solid rgba(0,212,170,0.07); }
        .sb-logomark { width: 36px; height: 36px; background: linear-gradient(135deg,#00d4aa,#0077ff); border-radius: 9px; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 18px rgba(0,212,170,0.4); flex-shrink: 0; }
        .sb-logomark svg { width: 20px; height: 20px; fill: #060a11; }
        .sb-wordmark { font-family: 'Space Mono',monospace; font-size: 1.15rem; font-weight: 700; color: #e2eeea; letter-spacing: .09em; }
        .sb-wordmark em { font-style: normal; color: #00d4aa; }
        .sb-section { position: relative; z-index: 2; font-size: .62rem; letter-spacing: .12em; text-transform: uppercase; color: rgba(0,212,170,0.32); padding: 1.2rem 1.4rem 0.5rem; font-weight: 600; }
        .sb-nav { position: relative; z-index: 2; flex: 1; padding: 0.4rem 0.75rem; overflow-y: auto; }
        .sb-nav::-webkit-scrollbar { width: 0; }
        .ni { display: flex; align-items: center; gap: 10px; padding: .62rem .75rem; border-radius: 9px; text-decoration: none; color: var(--st); font-size: .84rem; font-weight: 500; transition: background .18s, color .18s; margin-bottom: 2px; position: relative; }
        .ni:hover { background: rgba(0,212,170,0.07); color: rgba(220,240,232,0.85); }
        .ni.active { background: var(--sab); color: #00d4aa; }
        .ni.active::before { content: ''; position: absolute; left: 0; top: 20%; bottom: 20%; width: 3px; background: #00d4aa; border-radius: 0 3px 3px 0; }
        .ni-icon { width: 18px; height: 18px; color: rgba(180,220,205,0.38); flex-shrink: 0; transition: color .18s; }
        .ni.active .ni-icon { color: #00d4aa; }
        .sb-divider { height: 1px; background: rgba(0,212,170,0.06); margin: .6rem .5rem; }
        .sb-footer { position: relative; z-index: 2; padding: 1rem 1.1rem; border-top: 1px solid rgba(0,212,170,0.07); }
        .sb-version { font-size: .63rem; letter-spacing: .08em; color: rgba(0,212,170,0.22); font-family: 'Space Mono',monospace; text-align: center; }

        /* ════ MAIN ════ */
        .main { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* TOPBAR */
        .topbar { height: var(--th); background: var(--card); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 1.8rem; position: sticky; top: 0; z-index: 50; gap: 1rem; }
        .topbar-left { display: flex; align-items: center; gap: .75rem; }
        .sb-toggle { display: none; background: none; border: none; cursor: pointer; color: var(--ts); padding: 4px; border-radius: 6px; transition: background .15s; }
        .sb-toggle:hover { background: var(--light); }
        .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: .78rem; color: var(--tm); }
        .breadcrumb a { color: var(--tm); text-decoration: none; transition: color .15s; }
        .breadcrumb a:hover { color: #00a878; }
        .breadcrumb-sep { opacity: .45; }
        .breadcrumb-current { color: var(--tp); font-weight: 500; }
        .topbar-right { display: flex; align-items: center; gap: .85rem; }
        .topbar-icon-btn { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ts); transition: background .15s, color .15s; text-decoration: none; }
        .topbar-icon-btn:hover { background: var(--light); color: var(--tp); }
        .user-menu { position: relative; }
        .user-trigger { display: flex; align-items: center; gap: 8px; padding: 5px 10px 5px 6px; border: 1px solid var(--border); border-radius: 10px; cursor: pointer; background: transparent; font-family: 'Sora',sans-serif; transition: background .15s; }
        .user-trigger:hover { background: var(--light); }
        .user-avatar { width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg,#00d4aa,#0077ff); display: flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 700; font-family: 'Space Mono',monospace; color: #060a11; flex-shrink: 0; }
        .user-name { font-size: .82rem; font-weight: 600; color: var(--tp); max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-role-badge { font-size: .6rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; background: rgba(77,166,255,.1); color: #2563eb; border-radius: 5px; padding: 1px 6px; }
        .chevron { color: var(--tm); transition: transform .2s; flex-shrink: 0; }
        .user-menu.open .chevron { transform: rotate(180deg); }
        .user-dropdown { position: absolute; top: calc(100% + 8px); right: 0; width: 220px; background: var(--card); border: 1px solid var(--border); border-radius: 12px; box-shadow: 0 12px 36px rgba(0,0,0,0.12); overflow: hidden; opacity: 0; transform: translateY(-8px) scale(0.97); pointer-events: none; transition: opacity .18s, transform .18s; z-index: 200; }
        .user-menu.open .user-dropdown { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }
        .dd-header { padding: .9rem 1rem .75rem; border-bottom: 1px solid var(--border); }
        .dd-name  { font-size: .85rem; font-weight: 600; color: var(--tp); }
        .dd-email { font-size: .74rem; color: var(--tm); margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-body  { padding: .45rem .5rem; }
        .dd-item  { display: flex; align-items: center; gap: 9px; padding: .58rem .7rem; border-radius: 7px; font-size: .82rem; color: var(--ts); text-decoration: none; cursor: pointer; transition: background .14s, color .14s; border: none; background: none; width: 100%; font-family: 'Sora',sans-serif; text-align: left; }
        .dd-item:hover { background: var(--light); color: var(--tp); }
        .dd-item.danger { color: #c0392b; }
        .dd-item.danger:hover { background: rgba(192,57,43,0.07); }
        .dd-divider { height: 1px; background: var(--border); margin: .35rem .5rem; }

        /* ════ CONTENT ════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        /* PAGE HEADER */
        .page-header { margin-bottom: 1.5rem; display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
        .page-eyebrow { font-size: .75rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: #00a878; margin-bottom: 3px; }
        .page-title { font-size: 1.3rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; display: flex; align-items: center; gap: .5rem; flex-wrap: wrap; }
        .order-id-badge { font-family: 'Space Mono',monospace; font-size: .72rem; background: var(--teal-dim); color: #00a878; border: 1px solid rgba(0,212,170,.2); border-radius: 7px; padding: 3px 9px; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 3px; }

        /* FLASH */
        .flash { display: flex; align-items: center; gap: 10px; padding: .85rem 1.1rem; border-radius: 11px; margin-bottom: 1.1rem; font-size: .82rem; font-weight: 500; animation: cardIn .4s cubic-bezier(.22,1,.36,1) both; }
        .flash-success { background: rgba(0,168,120,.1); border: 1px solid rgba(0,168,120,.2); color: #00875a; }
        .flash-error   { background: rgba(220,38,38,.07); border: 1px solid rgba(220,38,38,.15); color: #b91c1c; }

        /* ── LAYOUT ── */
        .order-layout { display: grid; grid-template-columns: 1fr 300px; gap: 1.2rem; align-items: start; }

        /* ── ORDER ITEMS CARD ── */
        .items-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            overflow: hidden;
            animation: cardIn .5s .04s cubic-bezier(.22,1,.36,1) both;
        }

        .card-header {
            padding: .9rem 1.3rem; border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm);
            display: flex; align-items: center; gap: 8px;
        }
        .card-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }
        .card-count { font-size: .68rem; color: var(--tm); }

        /* Item rows */
        .item-row {
            display: flex; align-items: center; gap: 12px;
            padding: 1rem 1.3rem; border-bottom: 1px solid var(--border);
            transition: background .12s;
        }
        .item-row:last-child { border-bottom: none; }
        .item-row:hover { background: #fafcfb; }

        .item-thumb {
            width: 48px; height: 48px; border-radius: 10px;
            object-fit: cover; border: 1px solid var(--border); flex-shrink: 0;
        }
        .item-thumb-placeholder {
            width: 48px; height: 48px; border-radius: 10px; flex-shrink: 0;
            background: var(--light); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center; color: rgba(12,26,20,.18);
        }

        .item-info { flex: 1; min-width: 0; }
        .item-name { font-size: .87rem; font-weight: 600; color: var(--tp); }
        .item-meta { font-size: .72rem; color: var(--tm); margin-top: 2px; }
        .item-meta span { font-family: 'Space Mono',monospace; }

        .item-right { text-align: right; flex-shrink: 0; }
        .item-subtotal { font-family: 'Space Mono',monospace; font-size: .88rem; font-weight: 700; color: var(--tp); }
        .item-unit     { font-size: .68rem; color: var(--tm); margin-top: 2px; font-family: 'Space Mono',monospace; }

        /* ── ORDER META CARD ── */
        .meta-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            overflow: hidden; position: sticky; top: calc(var(--th) + 1.2rem);
            animation: cardIn .5s .1s cubic-bezier(.22,1,.36,1) both;
        }

        .meta-header { padding: .9rem 1.3rem; border-bottom: 1px solid var(--border); }
        .meta-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm);
            display: flex; align-items: center; gap: 8px;
        }
        .meta-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }

        .meta-body { padding: 1.1rem 1.3rem; display: flex; flex-direction: column; gap: .75rem; }

        .meta-field { display: flex; flex-direction: column; gap: 3px; }
        .meta-label { font-size: .67rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: rgba(12,26,20,.3); }
        .meta-value { font-size: .82rem; font-weight: 600; color: var(--tp); }
        .meta-value.mono { font-family: 'Space Mono',monospace; font-size: .78rem; }

        /* Status badge */
        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: .68rem; font-weight: 700; letter-spacing: .04em;
        }
        .status-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
        .badge-pending   { background: rgba(245,158,11,.1);  color: #b45309; } .badge-pending::before   { background: #f59e0b; animation: pulse 1.8s infinite; }
        .badge-approved  { background: rgba(0,119,255,.1);   color: #1d4ed8; } .badge-approved::before  { background: #3b82f6; }
        .badge-completed { background: rgba(0,168,120,.1);   color: #065f46; } .badge-completed::before { background: #00a878; }
        .badge-cancelled { background: rgba(220,38,38,.08);  color: #b91c1c; } .badge-cancelled::before { background: #dc2626; }
        @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }

        .meta-divider { height: 1px; background: var(--border); }

        /* Total row */
        .total-row { display: flex; align-items: center; justify-content: space-between; }
        .total-label { font-size: .85rem; font-weight: 700; color: var(--tp); }
        .total-value { font-family: 'Space Mono',monospace; font-size: 1.15rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }

        /* Footer actions */
        .meta-footer { padding: 1rem 1.3rem; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: .6rem; }

        .btn-track {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            width: 100%; padding: .72rem 1rem;
            background: #0a1a15; border: none; border-radius: 10px;
            color: #d8f0e8; font-family: 'Sora',sans-serif; font-size: .82rem; font-weight: 600;
            text-decoration: none; transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-track:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,168,120,.2); }

        .btn-cancel {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            width: 100%; padding: .68rem 1rem;
            background: rgba(220,38,38,.06); border: 1.5px solid rgba(220,38,38,.15); border-radius: 10px;
            color: #dc2626; font-family: 'Sora',sans-serif; font-size: .82rem; font-weight: 600;
            cursor: pointer; transition: background .15s, transform .12s;
        }
        .btn-cancel:hover { background: rgba(220,38,38,.12); transform: translateY(-1px); }
        .btn-cancel:active { transform: translateY(0); }

        .btn-back {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            width: 100%; padding: .65rem 1rem;
            background: none; border: 1.5px solid var(--border); border-radius: 10px;
            color: var(--ts); font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 500;
            text-decoration: none; transition: background .14s, color .14s;
        }
        .btn-back:hover { background: var(--light); color: var(--tp); }

        /* CANCEL CONFIRM MODAL */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(6,10,17,.55);
            display: flex; align-items: center; justify-content: center;
            z-index: 300; opacity: 0; pointer-events: none;
            transition: opacity .2s;
        }
        .modal-overlay.open { opacity: 1; pointer-events: all; }

        .modal {
            background: var(--card); border: 1px solid var(--border); border-radius: 16px;
            padding: 1.6rem 1.8rem; width: 100%; max-width: 380px; margin: 1rem;
            transform: translateY(12px) scale(.97);
            transition: transform .22s cubic-bezier(.22,1,.36,1);
            box-shadow: 0 24px 60px rgba(0,0,0,.18);
        }
        .modal-overlay.open .modal { transform: translateY(0) scale(1); }

        .modal-icon { width: 46px; height: 46px; border-radius: 12px; background: rgba(220,38,38,.08); border: 1.5px solid rgba(220,38,38,.18); display: flex; align-items: center; justify-content: center; color: #dc2626; margin-bottom: 1rem; }
        .modal-title { font-size: .98rem; font-weight: 700; color: var(--tp); margin-bottom: .35rem; }
        .modal-desc  { font-size: .8rem; color: var(--tm); line-height: 1.5; margin-bottom: 1.2rem; }

        .modal-actions { display: flex; gap: .6rem; }
        .modal-confirm {
            flex: 1; padding: .7rem; background: #dc2626; border: none; border-radius: 9px;
            color: #fff; font-family: 'Sora',sans-serif; font-size: .82rem; font-weight: 700;
            cursor: pointer; transition: background .15s, transform .12s;
        }
        .modal-confirm:hover { background: #b91c1c; transform: translateY(-1px); }
        .modal-dismiss {
            flex: 1; padding: .7rem; background: none; border: 1.5px solid var(--border); border-radius: 9px;
            color: var(--ts); font-family: 'Sora',sans-serif; font-size: .82rem; font-weight: 500;
            cursor: pointer; transition: background .14s;
        }
        .modal-dismiss:hover { background: var(--light); }

        /* MOBILE */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 960px) {
            .order-layout { grid-template-columns: 1fr; }
            .meta-card { position: static; }
        }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
        }
        @media (max-width: 600px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .user-name, .user-role-badge { display: none; }
            .item-row { gap: 9px; }
        }
    </style>
</head>
<body>

{{-- Cancel Confirm Modal --}}
<div class="modal-overlay" id="cancelModal">
    <div class="modal">
        <div class="modal-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
        </div>
        <p class="modal-title">Cancel this order?</p>
        <p class="modal-desc">This will permanently cancel Order <strong>#{{ $order->id }}</strong>. Any reserved stock will be released. This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="modal-dismiss" onclick="closeModal()">Keep Order</button>
            <form action="{{ route('user.orders.cancel', $order) }}" method="POST" style="flex:1;">
                @csrf
                <button type="submit" class="modal-confirm" style="width:100%;">Yes, Cancel</button>
            </form>
        </div>
    </div>
</div>

<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<div class="shell">

    {{-- ════ SIDEBAR ════ --}}
    <aside class="sidebar" id="sidebar">
        <div class="sb-brand">
            <div class="sb-logomark">
                <svg viewBox="0 0 24 24"><path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM17 13h4v4h-4zM13 17h4v4h-4z"/></svg>
            </div>
            <span class="sb-wordmark">STO<em>RIX</em></span>
        </div>
        <p class="sb-section">Shop</p>
        <nav class="sb-nav">
            <a href="{{ route('user.dashboard') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('user.products.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Products
            </a>
            <a href="{{ route('user.orders.index') }}" class="ni active">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                My Orders
            </a>
            <a href="{{ route('user.cart.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                My Cart
            </a>
            <div class="sb-divider"></div>
            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                My Profile
            </a>
        </nav>
        <div class="sb-footer"><p class="sb-version">STORIX v1.0 · USER</p></div>
    </aside>

    {{-- ════ MAIN ════ --}}
    <div class="main">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="sb-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="breadcrumb">
                    <a href="{{ route('user.dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">/</span>
                    <a href="{{ route('user.orders.index') }}">My Orders</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Order #{{ $order->id }}</span>
                </div>
            </div>
            <div class="topbar-right">
                <div class="user-menu" id="userMenu">
                    <button class="user-trigger" onclick="toggleDropdown()">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}</div>
                        <span class="user-name">{{ auth()->user()->name ?? 'User' }}</span>
                        <span class="user-role-badge">User</span>
                        <svg class="chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dd-header">
                            <p class="dd-name">{{ auth()->user()->name ?? 'User' }}</p>
                            <p class="dd-email">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                        <div class="dd-body">
                            <a href="#" class="dd-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                My Profile
                            </a>
                            <a href="{{ route('user.orders.index') }}" class="dd-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                                My Orders
                            </a>
                            <div class="dd-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dd-item danger">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                                    Sign out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <main class="content">

            {{-- Page Header --}}
            <div class="page-header">
                <div>
                    <p class="page-eyebrow">Order Details</p>
                    <h1 class="page-title">
                        Order
                        <span class="order-id-badge">#{{ $order->id }}</span>
                    </h1>
                    <p class="page-subtitle">
                        Placed {{ $order->placed_at ? $order->placed_at->format('M d, Y · H:i') : $order->created_at->format('M d, Y · H:i') }}
                    </p>
                </div>
            </div>

            {{-- Flash --}}
            @if(session('success'))
                <div class="flash flash-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash flash-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Main Layout --}}
            <div class="order-layout">

                {{-- ── Items ── --}}
                <div class="items-card">
                    <div class="card-header">
                        <p class="card-title">Order Items</p>
                        <span class="card-count">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
                    </div>

                    @foreach($order->items as $item)
                        <div class="item-row">
                            {{-- Thumb --}}
                            @if($item->product->image ?? false)
                                <img class="item-thumb" src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                            @else
                                <div class="item-thumb-placeholder">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="item-info">
                                <p class="item-name">{{ $item->product->name }}</p>
                                <p class="item-meta">
                                    <span>{{ $item->quantity }}</span> × $<span>{{ number_format($item->unit_price, 2) }}</span>
                                </p>
                            </div>

                            {{-- Subtotal --}}
                            <div class="item-right">
                                <p class="item-subtotal">${{ number_format($item->total_price, 2) }}</p>
                                <p class="item-unit">subtotal</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- ── Meta / Summary ── --}}
                <div class="meta-card">
                    <div class="meta-header">
                        <p class="meta-title">Order Info</p>
                    </div>

                    <div class="meta-body">
                        <div class="meta-field">
                            <span class="meta-label">Order ID</span>
                            <span class="meta-value mono">#{{ $order->id }}</span>
                        </div>

                        <div class="meta-field">
                            <span class="meta-label">Status</span>
                            <span class="status-badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                        </div>

                        <div class="meta-field">
                            <span class="meta-label">Placed</span>
                            <span class="meta-value">{{ $order->placed_at ? $order->placed_at->format('M d, Y') : $order->created_at->format('M d, Y') }}</span>
                        </div>

                        @if($order->approved_at)
                        <div class="meta-field">
                            <span class="meta-label">Approved</span>
                            <span class="meta-value">{{ $order->approved_at->format('M d, Y') }}</span>
                        </div>
                        @endif

                        @if($order->completed_at)
                        <div class="meta-field">
                            <span class="meta-label">Completed</span>
                            <span class="meta-value">{{ $order->completed_at->format('M d, Y') }}</span>
                        </div>
                        @endif

                        <div class="meta-divider"></div>

                        <div class="meta-field">
                            <span class="meta-label">Items</span>
                            <span class="meta-value">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
                        </div>

                        <div class="meta-divider"></div>

                        <div class="total-row">
                            <span class="total-label">Order Total</span>
                            <span class="total-value">${{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>

                    <div class="meta-footer">
                        <a href="{{ route('user.orders.track', $order) }}" class="btn-track">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Track Order
                        </a>

                        @if($order->status === 'pending')
                            <button type="button" class="btn-cancel" onclick="openModal()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/></svg>
                                Cancel Order
                            </button>
                        @endif

                        <a href="{{ route('user.orders.index') }}" class="btn-back">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                            Back to History
                        </a>
                    </div>
                </div>

            </div>
        </main>
    </div>
</div>

<script>
    function toggleDropdown() { document.getElementById('userMenu').classList.toggle('open'); }
    document.addEventListener('click', function(e) {
        var m = document.getElementById('userMenu');
        if (m && !m.contains(e.target)) m.classList.remove('open');
    });
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sbOverlay').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sbOverlay').classList.remove('open');
    }
    function openModal() {
        document.getElementById('cancelModal').classList.add('open');
    }
    function closeModal() {
        document.getElementById('cancelModal').classList.remove('open');
    }
    document.getElementById('cancelModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });
</script>

</body>
</html> 