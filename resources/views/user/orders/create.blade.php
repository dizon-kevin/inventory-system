{{-- resources/views/user/orders/create.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout — Storix</title>

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
        .sidebar {
            width: var(--sw); flex-shrink: 0; background: var(--dark);
            display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; height: 100vh;
            z-index: 100; transition: transform 0.28s cubic-bezier(0.22,1,0.36,1); overflow: hidden;
        }
        .sidebar::before {
            content: ''; position: absolute; inset: 0;
            background-image: radial-gradient(rgba(0,212,170,0.13) 1px, transparent 1px);
            background-size: 28px 28px; pointer-events: none; z-index: 0;
        }
        .sidebar::after {
            content: ''; position: absolute; width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(0,212,170,0.16), transparent 65%);
            top: -60px; left: -80px; border-radius: 50%; pointer-events: none; z-index: 0;
            animation: glowPulse 10s ease-in-out infinite alternate;
        }
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
        .topbar-icon-btn { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ts); transition: background .15s, color .15s; text-decoration: none; position: relative; }
        .topbar-icon-btn:hover { background: var(--light); color: var(--tp); }
        .tb-badge { position: absolute; top: -4px; right: -4px; width: 16px; height: 16px; border-radius: 50%; background: #00a878; color: #fff; font-size: .55rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid var(--light); font-family: 'Space Mono',monospace; }
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
        .page-header { margin-bottom: 1.5rem; }
        .page-greeting { font-size: .75rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: #00a878; margin-bottom: 3px; }
        .page-title { font-size: 1.3rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        /* FLASH */
        .flash { display: flex; align-items: center; gap: 10px; padding: .85rem 1.1rem; border-radius: 11px; margin-bottom: 1.1rem; font-size: .82rem; font-weight: 500; animation: cardIn .4s cubic-bezier(.22,1,.36,1) both; }
        .flash-error { background: rgba(220,38,38,.07); border: 1px solid rgba(220,38,38,.15); color: #b91c1c; }

        /* CHECKOUT STEPS */
        .steps-bar {
            display: flex; align-items: center; gap: 0;
            background: var(--card); border: 1px solid var(--border); border-radius: 12px;
            padding: .9rem 1.5rem; margin-bottom: 1.3rem;
            animation: cardIn .4s cubic-bezier(.22,1,.36,1) both;
        }
        .step { display: flex; align-items: center; gap: 8px; flex: 1; }
        .step-dot {
            width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem; font-weight: 700; font-family: 'Space Mono',monospace;
            transition: all .2s;
        }
        .step-dot.done   { background: #00a878; color: #fff; }
        .step-dot.active { background: #0a1a15; color: #d8f0e8; box-shadow: 0 0 0 3px rgba(0,168,120,.2); }
        .step-dot.idle   { background: var(--light); color: var(--tm); border: 1.5px solid var(--border); }
        .step-label { font-size: .76rem; font-weight: 600; color: var(--ts); }
        .step-label.active { color: var(--tp); }
        .step-label.done   { color: #00a878; }
        .step-connector { flex: 1; height: 1px; background: var(--border); max-width: 48px; }
        .step-connector.done { background: #00a878; }

        /* LAYOUT */
        .checkout-layout { display: grid; grid-template-columns: 1fr 320px; gap: 1.2rem; align-items: start; }

        /* CART REVIEW CARD */
        .review-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            overflow: hidden;
            animation: cardIn .5s .05s cubic-bezier(.22,1,.36,1) both;
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

        /* Item list */
        .item-list { padding: .5rem 0; }

        .item-row {
            display: flex; align-items: center; gap: 12px;
            padding: .85rem 1.3rem;
            border-bottom: 1px solid var(--border);
            transition: background .12s;
        }
        .item-row:last-child { border-bottom: none; }
        .item-row:hover { background: #fafcfb; }

        .item-thumb {
            width: 44px; height: 44px; border-radius: 9px;
            object-fit: cover; border: 1px solid var(--border); flex-shrink: 0;
        }
        .item-thumb-placeholder {
            width: 44px; height: 44px; border-radius: 9px; flex-shrink: 0;
            background: var(--light); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center; color: rgba(12,26,20,.18);
        }

        .item-info { flex: 1; min-width: 0; }
        .item-name  { font-size: .85rem; font-weight: 600; color: var(--tp); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .item-meta  { font-size: .72rem; color: var(--tm); margin-top: 2px; }
        .item-meta span { font-family: 'Space Mono',monospace; }

        .item-total { font-family: 'Space Mono',monospace; font-size: .88rem; font-weight: 700; color: var(--tp); white-space: nowrap; }

        /* ════ PLACE ORDER CARD ════ */
        .order-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            overflow: hidden; position: sticky; top: calc(var(--th) + 1.2rem);
            animation: cardIn .5s .12s cubic-bezier(.22,1,.36,1) both;
        }

        .order-card-header { padding: .9rem 1.3rem; border-bottom: 1px solid var(--border); }
        .order-card-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm);
            display: flex; align-items: center; gap: 8px;
        }
        .order-card-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }

        .order-body { padding: 1.1rem 1.3rem; display: flex; flex-direction: column; gap: .6rem; }

        .order-row { display: flex; align-items: center; justify-content: space-between; }
        .order-label { font-size: .8rem; color: var(--tm); }
        .order-value { font-size: .82rem; font-weight: 600; color: var(--ts); font-family: 'Space Mono',monospace; }

        .order-divider { height: 1px; background: var(--border); margin: .15rem 0; }

        .order-total-row { display: flex; align-items: center; justify-content: space-between; }
        .order-total-label { font-size: .9rem; font-weight: 700; color: var(--tp); }
        .order-total-value { font-size: 1.2rem; font-weight: 700; font-family: 'Space Mono',monospace; color: var(--tp); letter-spacing: -.02em; }

        /* Confirm notice */
        .confirm-notice {
            margin: 0 1.3rem .2rem;
            padding: .75rem .9rem;
            background: var(--teal-dim); border: 1px solid rgba(0,212,170,.2);
            border-radius: 9px; display: flex; align-items: flex-start; gap: 8px;
        }
        .confirm-notice-icon { color: #00a878; flex-shrink: 0; margin-top: 1px; }
        .confirm-notice-text { font-size: .74rem; color: #00875a; line-height: 1.45; }

        .order-footer { padding: 1rem 1.3rem; border-top: 1px solid var(--border); display: flex; flex-direction: column; gap: .6rem; }

        .btn-place {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: .88rem 1rem;
            background: #0a1a15; border: none; border-radius: 10px;
            color: #d8f0e8; font-family: 'Sora',sans-serif; font-size: .9rem; font-weight: 700;
            cursor: pointer; letter-spacing: .02em;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-place:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,168,120,.25); }
        .btn-place:active { transform: translateY(0); box-shadow: none; }

        /* Loading state */
        .btn-place.loading { pointer-events: none; opacity: .7; }
        .btn-place.loading .btn-label { display: none; }
        .btn-place.loading .btn-spinner { display: flex; }
        .btn-spinner { display: none; align-items: center; gap: 8px; }
        .spinner {
            width: 16px; height: 16px; border: 2px solid rgba(216,240,232,.3);
            border-top-color: #d8f0e8; border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .btn-back {
            display: flex; align-items: center; justify-content: center; gap: 7px;
            width: 100%; padding: .68rem 1rem;
            background: none; border: 1.5px solid var(--border); border-radius: 10px;
            color: var(--ts); font-family: 'Sora',sans-serif; font-size: .82rem; font-weight: 500;
            text-decoration: none; transition: background .15s, color .15s;
        }
        .btn-back:hover { background: var(--light); color: var(--tp); }

        /* MOBILE */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 960px) {
            .checkout-layout { grid-template-columns: 1fr; }
            .order-card { position: static; }
        }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
            .steps-bar { padding: .75rem 1rem; gap: 0; }
            .step-label { display: none; }
            .step-connector { max-width: 28px; }
        }
        @media (max-width: 600px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .user-name, .user-role-badge { display: none; }
        }
    </style>
</head>
<body>

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
            <a href="{{ route('user.orders.index') }}" class="ni">
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
                    <a href="{{ route('user.cart.index') }}">Cart</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Checkout</span>
                </div>
            </div>

            <div class="topbar-right">
                <a href="{{ route('user.cart.index') }}" class="topbar-icon-btn" aria-label="Cart">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @php $cartCount = $cartItems->count(); @endphp
                    @if($cartCount > 0)<span class="tb-badge">{{ $cartCount }}</span>@endif
                </a>

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
                <p class="page-greeting">Final Step</p>
                <h1 class="page-title">Checkout</h1>
                <p class="page-subtitle">Review your order and confirm to place it</p>
            </div>

            {{-- Flash --}}
            @if(session('error'))
                <div class="flash flash-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Checkout Steps --}}
            <div class="steps-bar">
                <div class="step">
                    <div class="step-dot done">
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <span class="step-label done">Cart</span>
                </div>
                <div class="step-connector done"></div>
                <div class="step">
                    <div class="step-dot active">2</div>
                    <span class="step-label active">Review</span>
                </div>
                <div class="step-connector"></div>
                <div class="step">
                    <div class="step-dot idle">3</div>
                    <span class="step-label">Confirmed</span>
                </div>
            </div>

            {{-- Main Layout --}}
            <div class="checkout-layout">

                {{-- ── Cart Review ── --}}
                <div class="review-card">
                    <div class="card-header">
                        <p class="card-title">Order Items</p>
                        <span style="font-size:.72rem;color:var(--tm);">{{ $cartItems->count() }} {{ Str::plural('item', $cartItems->count()) }}</span>
                    </div>

                    <div class="item-list">
                        @foreach($cartItems as $item)
                            <div class="item-row">
                                {{-- Thumb --}}
                                @if($item->product->image)
                                    <img
                                        class="item-thumb"
                                        src="{{ asset('storage/' . $item->product->image) }}"
                                        alt="{{ $item->product->name }}"
                                    >
                                @else
                                    <div class="item-thumb-placeholder">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                    </div>
                                @endif

                                {{-- Info --}}
                                <div class="item-info">
                                    <p class="item-name">{{ $item->product->name }}</p>
                                    <p class="item-meta">
                                        <span>{{ $item->quantity }}</span> × $<span>{{ number_format($item->product->price, 2) }}</span>
                                    </p>
                                </div>

                                {{-- Line total --}}
                                <p class="item-total">${{ number_format($item->quantity * $item->product->price, 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ── Place Order ── --}}
                <div class="order-card">
                    <div class="order-card-header">
                        <p class="order-card-title">Order Summary</p>
                    </div>

                    <div class="order-body">
                        <div class="order-row">
                            <span class="order-label">Subtotal ({{ $cartItems->count() }} {{ Str::plural('item', $cartItems->count()) }})</span>
                            <span class="order-value">${{ number_format($total, 2) }}</span>
                        </div>
                        <div class="order-row">
                            <span class="order-label">Shipping</span>
                            <span class="order-value" style="color:#00a878;">Free</span>
                        </div>
                        <div class="order-divider"></div>
                        <div class="order-total-row">
                            <span class="order-total-label">Order Total</span>
                            <span class="order-total-value">${{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    <div class="confirm-notice">
                        <svg class="confirm-notice-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <p class="confirm-notice-text">By placing your order, your cart will be cleared and stock will be reserved for your items.</p>
                    </div>

                    <div class="order-footer">
                        <form action="{{ route('user.orders.store') }}" method="POST" id="checkoutForm">
                            @csrf
                            <button type="submit" class="btn-place" id="placeBtn" onclick="handlePlace(this)">
                                <span class="btn-label" style="display:flex;align-items:center;gap:8px;">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    Place Order
                                </span>
                                <span class="btn-spinner">
                                    <span class="spinner"></span>
                                    Placing order…
                                </span>
                            </button>
                        </form>
                        <a href="{{ route('user.cart.index') }}" class="btn-back">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                            Back to Cart
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

    function handlePlace(btn) {
        btn.classList.add('loading');
    }
</script>

</body>
</html>