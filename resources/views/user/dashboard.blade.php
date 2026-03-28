{{-- resources/views/user/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard — Storix</title>

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
            top: -60px; left: -80px; border-radius: 50%;
            pointer-events: none; z-index: 0;
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

        /* ════════════════════
           CONTENT
        ════════════════════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }

        /* PAGE HEADER */
        .page-header { margin-bottom: 1.6rem; display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
        .page-header-left {}
        .page-greeting { font-size: .75rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: #00a878; margin-bottom: 3px; }
        .page-title { font-size: 1.3rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }
        .page-header-actions { display: flex; align-items: center; gap: .6rem; }

        .btn-action {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .58rem 1.1rem; border-radius: 9px;
            font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            text-decoration: none; cursor: pointer; border: none;
            transition: background .15s, transform .13s, box-shadow .15s;
        }
        .btn-action:active { transform: translateY(0) !important; }

        .btn-ghost {
            background: var(--card); color: var(--ts);
            border: 1.5px solid var(--border);
        }
        .btn-ghost:hover { background: var(--light); color: var(--tp); border-color: rgba(12,26,20,.15); }

        .btn-primary {
            background: #0a1a15; color: #d8f0e8;
            border: 1.5px solid transparent;
        }
        .btn-primary:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,168,120,.2); }

        .btn-cart {
            background: var(--teal-dim); color: #00a878;
            border: 1.5px solid rgba(0,212,170,.22);
            position: relative;
        }
        .btn-cart:hover { background: rgba(0,212,170,.18); transform: translateY(-1px); }

        .cart-badge {
            position: absolute; top: -5px; right: -5px;
            width: 17px; height: 17px; border-radius: 50%;
            background: #00a878; color: #fff;
            font-size: .57rem; font-weight: 700; font-family: 'Space Mono',monospace;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--light);
        }

        /* STAT CARDS */
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.4rem; }

        .stat-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            padding: 1.25rem 1.3rem; position: relative; overflow: hidden;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }

        .stat-card:nth-child(1) { animation-delay: 0s; }
        .stat-card:nth-child(2) { animation-delay: .06s; }
        .stat-card:nth-child(3) { animation-delay: .12s; }

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        .stat-card::after {
            content: ''; position: absolute; bottom: -20px; right: -20px;
            width: 80px; height: 80px; border-radius: 50%; opacity: .07;
        }

        .stat-card.blue::after  { background: #0077ff; }
        .stat-card.green::after { background: #00d4aa; }
        .stat-card.purple::after{ background: #7c3aed; }

        .stat-icon {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center; margin-bottom: .9rem;
        }

        .stat-icon.blue   { background: rgba(0,119,255,.1);  color: #0077ff; }
        .stat-icon.green  { background: rgba(0,212,170,.12); color: #00a878; }
        .stat-icon.purple { background: rgba(124,58,237,.1); color: #7c3aed; }

        .stat-label { font-size: .7rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: var(--tm); margin-bottom: 4px; }
        .stat-value { font-size: 1.6rem; font-weight: 700; font-family: 'Space Mono',monospace; color: var(--tp); letter-spacing: -.03em; line-height: 1; }
        .stat-sub   { font-size: .72rem; color: var(--tm); margin-top: 5px; }

        /* PRODUCTS TABLE CARD */
        .table-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            overflow: hidden;
            animation: cardIn .5s .18s cubic-bezier(.22,1,.36,1) both;
        }

        .table-header {
            padding: 1rem 1.4rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;
        }

        .table-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm);
            display: flex; align-items: center; gap: 8px;
        }
        .table-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }

        .table-actions { display: flex; align-items: center; gap: .5rem; }

        /* Search */
        .search-wrap { position: relative; }
        .search-input {
            padding: .48rem .9rem .48rem 2.1rem; border: 1.5px solid var(--border);
            border-radius: 8px; background: var(--light); color: var(--tp);
            font-family: 'Sora',sans-serif; font-size: .78rem; outline: none; width: 200px;
            transition: border-color .2s, background .2s, width .25s;
        }
        .search-input:focus { border-color: rgba(0,168,120,.4); background: #fff; width: 240px; }
        .search-icon { position: absolute; left: 8px; top: 50%; transform: translateY(-50%); color: var(--tm); pointer-events: none; }

        /* Table */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }

        thead th {
            padding: .75rem 1.2rem; text-align: left;
            font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            color: var(--tm); background: #fafbfa; border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: #fafcfb; }

        tbody td { padding: .9rem 1.2rem; font-size: .82rem; color: var(--ts); vertical-align: middle; }

        .td-name { font-weight: 600; color: var(--tp); }
        .td-sku  { font-family: 'Space Mono',monospace; font-size: .72rem; }
        .td-price{ font-weight: 600; color: var(--tp); font-family: 'Space Mono',monospace; font-size: .8rem; }

        /* Stock badges */
        .stock-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 9px; border-radius: 20px;
            font-size: .67rem; font-weight: 700; letter-spacing: .04em; white-space: nowrap;
        }
        .stock-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
        .stock-in     { background: rgba(0,168,120,.1);  color: #00875a; } .stock-in::before     { background: #00a878; }
        .stock-low    { background: rgba(234,179,8,.1);  color: #a16207; } .stock-low::before    { background: #eab308; }
        .stock-out    { background: rgba(220,38,38,.08); color: #b91c1c; } .stock-out::before    { background: #dc2626; }

        /* Actions */
        .td-actions { display: flex; align-items: center; gap: .5rem; }

        .action-btn {
            display: inline-flex; align-items: center; gap: 5px;
            padding: .38rem .75rem; border-radius: 7px; font-size: .74rem; font-weight: 500;
            text-decoration: none; cursor: pointer; border: none; font-family: 'Sora',sans-serif;
            transition: background .14s, color .14s, transform .12s;
        }
        .action-btn:active { transform: scale(.97); }

        .action-view {
            background: rgba(0,119,255,.07); color: #2563eb; border: 1px solid rgba(0,119,255,.12);
        }
        .action-view:hover { background: rgba(0,119,255,.13); }

        .action-cart {
            background: rgba(0,212,170,.09); color: #00a878; border: 1px solid rgba(0,212,170,.18);
        }
        .action-cart:hover { background: rgba(0,212,170,.16); }

        /* Empty state */
        .empty-state { padding: 3rem 1.5rem; text-align: center; }
        .empty-icon  { width: 48px; height: 48px; margin: 0 auto 1rem; color: rgba(12,26,20,.15); }
        .empty-title { font-size: .9rem; font-weight: 600; color: var(--ts); }
        .empty-sub   { font-size: .78rem; color: var(--tm); margin-top: 4px; }

        /* Pagination */
        .table-footer {
            padding: .9rem 1.2rem; border-top: 1px solid var(--border);
            background: #fafbfa; display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: .5rem;
        }

        .pagination-info { font-size: .74rem; color: var(--tm); }

        /* Mobile */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 960px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
        }

        @media (max-width: 640px) {
            .stats-grid { grid-template-columns: 1fr; }
            .content { padding: 1.2rem 1rem 2rem; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .user-name, .user-role-badge { display: none; }
            .search-input { width: 160px; }
            .search-input:focus { width: 180px; }
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
            <a href="{{ route('user.dashboard') }}" class="ni active">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
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
                    <span class="breadcrumb-current">Dashboard</span>
                </div>
            </div>

            <div class="topbar-right">
                <a href="{{ route('user.cart.index') }}" class="topbar-icon-btn" style="position:relative;" aria-label="Cart">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->count(); @endphp
                    @if($cartCount > 0)
                        <span style="position:absolute;top:-4px;right:-4px;width:16px;height:16px;border-radius:50%;background:#00a878;color:#fff;font-size:.55rem;font-weight:700;display:flex;align-items:center;justify-content:center;border:2px solid var(--light);font-family:'Space Mono',monospace;">{{ $cartCount }}</span>
                    @endif
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
                <div class="page-header-left">
                    <p class="page-greeting">Welcome back</p>
                    <h1 class="page-title">{{ auth()->user()->name }}</h1>
                    <p class="page-subtitle">Browse available products and manage your orders</p>
                </div>
                <div class="page-header-actions">
                    <a href="{{ route('user.cart.index') }}" class="btn-action btn-cart" style="position:relative;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Cart
                        @if($cartCount > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('user.orders.index') }}" class="btn-action btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                        Order History
                    </a>
                </div>
            </div>

            {{-- Stat Cards --}}
            <div class="stats-grid">
                <div class="stat-card blue">
                    <div class="stat-icon blue">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    </div>
                    <p class="stat-label">Total Products</p>
                    <p class="stat-value">{{ $products->total() }}</p>
                    <p class="stat-sub">Available in catalog</p>
                </div>

                <div class="stat-card green">
                    <div class="stat-icon green">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                    </div>
                    <p class="stat-label">My Orders</p>
                    <p class="stat-value">
                        @php $myOrders = \App\Models\Order::where('user_id', auth()->id())->count(); @endphp
                        {{ $myOrders }}
                    </p>
                    <p class="stat-sub">All time orders</p>
                </div>

                <div class="stat-card purple">
                    <div class="stat-icon purple">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <p class="stat-label">Cart Items</p>
                    <p class="stat-value">{{ $cartCount }}</p>
                    <p class="stat-sub">Ready to checkout</p>
                </div>
            </div>

            {{-- Products Table Card --}}
            <div class="table-card">
                <div class="table-header">
                    <p class="table-title">Available Products</p>
                    <div class="table-actions">
                        <div class="search-wrap">
                            <svg class="search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            <input class="search-input" type="text" placeholder="Search products…" id="productSearch" oninput="filterTable(this.value)">
                        </div>
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="table-wrap">
                        <table id="productsTable">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr class="product-row">
                                        <td class="td-name">{{ $product->name }}</td>
                                        <td class="td-sku">{{ $product->sku }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td>{{ $product->quantity }}</td>
                                        <td class="td-price">${{ number_format($product->price, 2) }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($product->stock_status) {
                                                    'In Stock'  => 'stock-in',
                                                    'Low Stock' => 'stock-low',
                                                    default     => 'stock-out',
                                                };
                                            @endphp
                                            <span class="stock-badge {{ $statusClass }}">{{ $product->stock_status }}</span>
                                        </td>
                                        <td>
                                            <div class="td-actions">
                                                <a href="{{ route('user.products.show', $product) }}" class="action-btn action-view">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                    View
                                                </a>
                                                @if($product->stock_status !== 'Out of Stock')
                                                    <form method="POST" action="{{ route('user.cart.store') }}" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                        <button type="submit" class="action-btn action-cart">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4"/><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/></svg>
                                                            Add
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="table-footer">
                        <span class="pagination-info">
                            Showing {{ $products->firstItem() }}–{{ $products->lastItem() }} of {{ $products->total() }} products
                        </span>
                        {{ $products->links() }}
                    </div>

                @else
                    <div class="empty-state">
                        <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <p class="empty-title">No products available</p>
                        <p class="empty-sub">Check back later for new arrivals.</p>
                    </div>
                @endif
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

    function filterTable(query) {
        var rows = document.querySelectorAll('#productsTable .product-row');
        var q = query.toLowerCase();
        rows.forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    }
</script>

</body>
</html>