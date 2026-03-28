<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $product->name }} — Storix</title>

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

        /* ════════════════════
           SIDEBAR
        ════════════════════ */
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
        .ni.active .ni-icon { color: #00d4aa; }
        .ni-icon { width: 18px; height: 18px; color: rgba(180,220,205,0.38); flex-shrink: 0; transition: color .18s; }
        .sb-divider { height: 1px; background: rgba(0,212,170,0.06); margin: .6rem .5rem; }
        .sb-footer { position: relative; z-index: 2; padding: 1rem 1.1rem; border-top: 1px solid rgba(0,212,170,0.07); }
        .sb-version { font-size: .63rem; letter-spacing: .08em; color: rgba(0,212,170,0.22); font-family: 'Space Mono',monospace; text-align: center; }

        /* ════════════════════
           MAIN
        ════════════════════ */
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
        .breadcrumb-current { color: var(--tp); font-weight: 500; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .topbar-right { display: flex; align-items: center; gap: .85rem; }

        /* Cart button */
        .btn-cart {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .5rem 1rem; background: var(--teal-dim);
            color: #00a878; border: 1.5px solid rgba(0,168,120,.25);
            font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            border-radius: 8px; cursor: pointer; text-decoration: none;
            transition: background .2s, border-color .2s, transform .15s;
            letter-spacing: .02em; position: relative;
        }
        .btn-cart:hover { background: rgba(0,168,120,.15); border-color: rgba(0,168,120,.45); transform: translateY(-1px); }

        .cart-count {
            position: absolute; top: -6px; right: -6px;
            width: 17px; height: 17px; border-radius: 50%;
            background: #00a878; color: #fff;
            font-size: .58rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--card);
        }

        .topbar-icon-btn { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ts); transition: background .15s, color .15s; }
        .topbar-icon-btn:hover { background: var(--light); color: var(--tp); }

        .user-menu { position: relative; }
        .user-trigger { display: flex; align-items: center; gap: 8px; padding: 5px 10px 5px 6px; border: 1px solid var(--border); border-radius: 10px; cursor: pointer; background: transparent; font-family: 'Sora',sans-serif; transition: background .15s; }
        .user-trigger:hover { background: var(--light); }
        .user-avatar { width: 30px; height: 30px; border-radius: 8px; background: linear-gradient(135deg,#00d4aa,#0077ff); display: flex; align-items: center; justify-content: center; font-size: .72rem; font-weight: 700; font-family: 'Space Mono',monospace; color: #060a11; flex-shrink: 0; }
        .user-name { font-size: .82rem; font-weight: 600; color: var(--tp); max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-role-badge { font-size: .6rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; background: var(--teal-dim); color: #00a878; border-radius: 5px; padding: 1px 6px; }
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

        /* ════════════════════
           CONTENT
        ════════════════════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }

        /* ── PRODUCT LAYOUT ── */
        .product-grid { display: grid; grid-template-columns: 440px 1fr; gap: 1.6rem; align-items: start; animation: cardIn .5s cubic-bezier(.22,1,.36,1) both; }
        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        /* ── IMAGE COLUMN ── */
        .image-col { display: flex; flex-direction: column; gap: 1rem; }

        .main-image-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 16px; overflow: hidden; position: relative;
        }

        .main-image-wrap {
            width: 100%; aspect-ratio: 1; overflow: hidden;
        }

        .main-image {
            width: 100%; height: 100%; object-fit: cover;
            transition: transform .5s ease;
        }

        .main-image-wrap:hover .main-image { transform: scale(1.06); }

        .image-placeholder {
            width: 100%; aspect-ratio: 1;
            background: linear-gradient(135deg, rgba(0,212,170,.06), rgba(0,119,255,.04));
            display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: .7rem;
            color: rgba(0,212,170,.3);
        }

        .image-placeholder p { font-size: .82rem; color: var(--tm); }

        .image-status-badge {
            position: absolute; top: 12px; left: 12px;
        }

        /* ── INFO COLUMN ── */
        .info-col { display: flex; flex-direction: column; gap: 1.1rem; }

        /* Breadcrumb */
        .page-nav { display: flex; align-items: center; gap: 5px; font-size: .78rem; color: var(--tm); margin-bottom: -.2rem; }
        .page-nav a { color: var(--tm); text-decoration: none; transition: color .15s; }
        .page-nav a:hover { color: #00a878; }
        .page-nav-sep { opacity: .4; }

        /* Product header */
        .product-header { }
        .product-title { font-size: 1.65rem; font-weight: 700; color: var(--tp); letter-spacing: -.025em; line-height: 1.2; margin-bottom: .55rem; }
        .product-meta { display: flex; align-items: center; gap: .6rem; flex-wrap: wrap; }
        .meta-chip { display: inline-flex; align-items: center; gap: 5px; font-size: .74rem; color: var(--ts); background: var(--light); border: 1px solid var(--border); border-radius: 6px; padding: 3px 9px; }
        .meta-chip svg { color: var(--teal); }

        /* Price block */
        .price-block {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 13px; padding: 1.2rem 1.4rem;
        }

        .price-main { font-family: 'Space Mono',monospace; font-size: 2rem; font-weight: 700; color: var(--tp); letter-spacing: -.04em; line-height: 1; }
        .price-sub  { font-size: .76rem; color: var(--tm); margin-top: 4px; }

        /* Stock indicator */
        .stock-bar-wrap { display: flex; align-items: center; gap: 8px; margin-top: .85rem; }
        .stock-bar { flex: 1; height: 5px; background: var(--light); border-radius: 10px; overflow: hidden; max-width: 140px; }
        .stock-bar-fill { height: 100%; border-radius: 10px; }
        .stock-bar-fill.green  { background: #00d4aa; }
        .stock-bar-fill.yellow { background: #f59e0b; }
        .stock-bar-fill.red    { background: #ef4444; }
        .stock-label { font-size: .76rem; font-weight: 600; }
        .stock-label.green  { color: #00a878; }
        .stock-label.yellow { color: #d97706; }
        .stock-label.red    { color: #dc2626; }
        .stock-qty { font-size: .72rem; color: var(--tm); font-family: 'Space Mono',monospace; }

        /* Description */
        .desc-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 13px; padding: 1.1rem 1.4rem;
        }

        .desc-label { font-size: .68rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--tm); margin-bottom: .55rem; display: flex; align-items: center; gap: 7px; }
        .desc-label::before { content: ''; width: 3px; height: 12px; background: var(--teal); border-radius: 2px; }
        .desc-text  { font-size: .85rem; color: var(--ts); line-height: 1.7; }
        .desc-empty { font-size: .82rem; color: var(--tm); font-style: italic; }

        /* Add to cart card */
        .cart-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 13px; padding: 1.2rem 1.4rem;
        }

        .cart-card-title { font-size: .78rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--tm); margin-bottom: 1rem; display: flex; align-items: center; gap: 8px; }
        .cart-card-title::before { content: ''; width: 3px; height: 13px; background: var(--teal); border-radius: 2px; }

        /* Qty input */
        .qty-row { display: flex; align-items: center; gap: .75rem; margin-bottom: 1.1rem; }
        .qty-label { font-size: .72rem; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: var(--tm); }

        .qty-control { display: flex; align-items: center; border: 1.5px solid var(--border); border-radius: 9px; overflow: hidden; background: var(--card); }
        .qty-btn { width: 36px; height: 36px; border: none; background: var(--light); cursor: pointer; font-size: 1rem; color: var(--ts); display: flex; align-items: center; justify-content: center; transition: background .14s, color .14s; user-select: none; }
        .qty-btn:hover { background: rgba(0,168,120,.08); color: #00a878; }
        .qty-input { width: 48px; height: 36px; border: none; text-align: center; font-family: 'Space Mono',monospace; font-size: .9rem; font-weight: 700; color: var(--tp); background: transparent; outline: none; -moz-appearance: textfield; }
        .qty-input::-webkit-outer-spin-button,
        .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; }
        .qty-max { font-size: .72rem; color: var(--tm); font-family: 'Space Mono',monospace; }

        /* Add to cart button */
        .btn-add-cart {
            width: 100%; padding: .9rem 1rem;
            background: linear-gradient(135deg, #00d4aa, #00a3e0);
            border: none; border-radius: 10px;
            color: #060a11; font-family: 'Sora',sans-serif;
            font-size: .9rem; font-weight: 700; letter-spacing: .03em;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: transform .15s, box-shadow .2s, opacity .2s;
            position: relative; overflow: hidden;
        }

        .btn-add-cart::before { content: ''; position: absolute; inset: 0; background: rgba(255,255,255,0); transition: background .2s; }
        .btn-add-cart:hover { transform: translateY(-1px); box-shadow: 0 8px 24px rgba(0,212,170,.3); }
        .btn-add-cart:hover::before { background: rgba(255,255,255,.1); }
        .btn-add-cart:active { transform: translateY(0); }

        /* Out of stock state */
        .out-of-stock-banner {
            display: flex; align-items: center; gap: 10px;
            background: rgba(239,68,68,.06); border: 1.5px solid rgba(239,68,68,.18);
            border-radius: 10px; padding: .9rem 1.1rem;
            font-size: .84rem; font-weight: 600; color: #dc2626;
        }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .badge-green  { background: rgba(34,197,94,.1);  color: #16a34a; } .badge-green::before  { background: #16a34a; }
        .badge-yellow { background: rgba(251,191,36,.1); color: #b45309; } .badge-yellow::before { background: #d97706; }
        .badge-red    { background: rgba(239,68,68,.1);  color: #dc2626; } .badge-red::before    { background: #dc2626; }

        /* Back link */
        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: .82rem; color: #00a878; text-decoration: none; font-weight: 500; transition: color .18s; margin-top: .3rem; }
        .back-link:hover { color: #007558; }

        /* Flash */
        .flash { display: flex; align-items: center; gap: 10px; border-radius: 10px; padding: .75rem 1rem; font-size: .82rem; margin-bottom: 1.4rem; animation: fadeIn .4s ease; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
        .flash-success { background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2); color: #15803d; }
        .flash-error   { background: rgba(239,68,68,0.08);  border: 1px solid rgba(239,68,68,0.2);  color: #dc2626; }

        /* Mobile */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 1050px) { .product-grid { grid-template-columns: 340px 1fr; } }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
            .product-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 560px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .user-name, .user-role-badge { display: none; }
        }
        @include('admin-notifications-styles')
    </style>
</head>
<body>

<div class="sb-overlay" id="sbOverlay" onclick="closeSidebar()"></div>

<div class="shell">

    {{-- ════ SIDEBAR ════ --}}
    <aside class="sidebar" id="sidebar">
        <div class="sb-brand">
            <div class="sb-logomark"><svg viewBox="0 0 24 24"><path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM17 13h4v4h-4zM13 17h4v4h-4z"/></svg></div>
            <span class="sb-wordmark">STO<em>RIX</em></span>
        </div>
        <p class="sb-section">Main</p>
        <nav class="sb-nav">
            <a href="{{ route('user.products.index') }}" class="ni active">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Browse Products
            </a>
            <a href="{{ route('user.cart.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.99-1.85L23 6H6"/></svg>
                My Cart
            </a>
            <a href="{{ route('user.orders.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                Order History
            </a>
            <div class="sb-divider"></div>
            <p class="sb-section" style="padding-top:0">Account</p>
            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                My Profile
            </a>
            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M21 12h-2M5 12H3M16.24 16.24l1.41 1.41M6.34 17.66L4.93 19.07M12 21v-2M12 5V3"/></svg>
                Settings
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
                    <a href="{{ route('user.products.index') }}">Products</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">{{ $product->name }}</span>
                </div>
            </div>
            <div class="topbar-right">

                {{-- Cart button --}}
                <a href="{{ route('user.cart.index') }}" class="btn-cart">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.99-1.85L23 6H6"/></svg>
                    Cart
                    @if(isset($cartCount) && $cartCount > 0)
                        <span class="cart-count">{{ $cartCount }}</span>
                    @endif
                </a>

                @include('admin-notifications-dropdown')

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
                            <a href="{{ route('user.cart.index') }}" class="dd-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.99-1.85L23 6H6"/></svg>
                                My Cart
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

            {{-- Flash --}}
            @if(session('success'))
                <div class="flash flash-success">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash flash-error">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <div class="product-grid">

                {{-- ── IMAGE COLUMN ── --}}
                <div class="image-col">
                    <div class="main-image-card">
                        @if($product->image)
                            <div class="main-image-wrap">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="main-image" />
                            </div>
                        @else
                            <div class="image-placeholder">
                                <svg width="52" height="52" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <p>No image available</p>
                            </div>
                        @endif

                        <div class="image-status-badge">
                            @if($product->stock_status === 'In Stock')
                                <span class="badge badge-green">In Stock</span>
                            @elseif($product->stock_status === 'Low Stock')
                                <span class="badge badge-yellow">Low Stock</span>
                            @else
                                <span class="badge badge-red">Out of Stock</span>
                            @endif
                        </div>
                    </div>

                    {{-- Back link --}}
                    <a href="{{ route('user.products.index') }}" class="back-link">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        Back to Products
                    </a>
                </div>

                {{-- ── INFO COLUMN ── --}}
                <div class="info-col">

                    {{-- Product header --}}
                    <div class="product-header">
                        <h1 class="product-title">{{ $product->name }}</h1>
                        <div class="product-meta">
                            <span class="meta-chip">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                                {{ $product->category?->name ?? 'Uncategorized' }}
                            </span>
                            <span class="meta-chip" style="font-family:'Space Mono',monospace;font-size:.7rem">
                                {{ $product->sku }}
                            </span>
                        </div>
                    </div>

                    {{-- Price block --}}
                    <div class="price-block">
                        <div class="price-main">${{ number_format($product->price, 2) }}</div>
                        <div class="price-sub">Unit price · inclusive of taxes</div>

                        {{-- Stock bar --}}
                        @php
                            $stockPct = $product->quantity > 0 ? min(($product->quantity / 100) * 100, 100) : 0;
                            $stockClass = $product->stock_status === 'In Stock' ? 'green' : ($product->stock_status === 'Low Stock' ? 'yellow' : 'red');
                        @endphp

                        <div class="stock-bar-wrap">
                            <div class="stock-bar">
                                <div class="stock-bar-fill {{ $stockClass }}" style="width:{{ $stockPct }}%"></div>
                            </div>
                            <span class="stock-label {{ $stockClass }}">{{ $product->stock_status }}</span>
                            <span class="stock-qty">{{ $product->quantity }} left</span>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="desc-card">
                        <p class="desc-label">Description</p>
                        @if($product->description)
                            <p class="desc-text">{{ $product->description }}</p>
                        @else
                            <p class="desc-empty">No description provided for this product.</p>
                        @endif
                    </div>

                    {{-- Add to Cart / Out of Stock --}}
                    @if($product->stock_status === 'Out of Stock')
                        <div class="out-of-stock-banner">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            This product is currently out of stock. Check back later.
                        </div>
                    @else
                        <div class="cart-card">
                            <p class="cart-card-title">Add to Cart</p>

                            <form action="{{ route('user.cart.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}" />

                                <div class="qty-row">
                                    <span class="qty-label">Qty</span>
                                    <div class="qty-control">
                                        <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                                        <input
                                            type="number"
                                            id="qtyInput"
                                            name="quantity"
                                            value="1"
                                            min="1"
                                            max="{{ $product->quantity }}"
                                            class="qty-input"
                                        />
                                        <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                                    </div>
                                    <span class="qty-max">/ {{ $product->quantity }} max</span>
                                </div>

                                <button type="submit" class="btn-add-cart" id="addCartBtn">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 001.99-1.85L23 6H6"/></svg>
                                    Add to Cart
                                </button>
                            </form>
                        </div>
                    @endif

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

    // Quantity control
    var qtyInput  = document.getElementById('qtyInput');
    var maxQty    = {{ $product->quantity ?? 999 }};

    function changeQty(delta) {
        if (!qtyInput) return;
        var cur = parseInt(qtyInput.value) || 1;
        var next = Math.max(1, Math.min(maxQty, cur + delta));
        qtyInput.value = next;
    }

    if (qtyInput) {
        qtyInput.addEventListener('change', function() {
            var v = parseInt(this.value) || 1;
            this.value = Math.max(1, Math.min(maxQty, v));
        });
    }

    // Add to cart animation
    var addBtn = document.getElementById('addCartBtn');
    if (addBtn) {
        addBtn.closest('form').addEventListener('submit', function() {
            addBtn.style.opacity = '.7';
            addBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Adding...';
        });
    }
</script>

@include('admin-notifications-script')
</body>
</html>
