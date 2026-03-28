{{-- resources/views/user/orders/track.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Track Order #{{ $order->id }} — Storix</title>

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
        @keyframes lineGrow { from{width:0} to{width:100%} }
        @keyframes popIn { from{opacity:0;transform:scale(.5)} to{opacity:1;transform:scale(1)} }

        /* PAGE HEADER */
        .page-header { margin-bottom: 1.5rem; display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
        .page-header-left {}
        .page-eyebrow { font-size: .75rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: #00a878; margin-bottom: 3px; }
        .page-title { font-size: 1.3rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; display: flex; align-items: center; gap: .5rem; }
        .order-id-badge { font-family: 'Space Mono',monospace; font-size: .72rem; background: var(--teal-dim); color: #00a878; border: 1px solid rgba(0,212,170,.2); border-radius: 7px; padding: 3px 9px; letter-spacing: .04em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 3px; }

        .btn-detail {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .58rem 1.1rem; border-radius: 9px;
            background: #0a1a15; border: none;
            color: #d8f0e8; font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            text-decoration: none; transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-detail:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,168,120,.2); }

        /* ── STATUS HERO CARD ── */
        .status-hero {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            padding: 1.5rem 1.6rem; margin-bottom: 1.2rem;
            display: flex; align-items: center; gap: 1.2rem; flex-wrap: wrap;
            animation: cardIn .45s cubic-bezier(.22,1,.36,1) both;
            position: relative; overflow: hidden;
        }
        .status-hero::after {
            content: ''; position: absolute; bottom: -40px; right: -40px;
            width: 160px; height: 160px; border-radius: 50%; pointer-events: none;
            opacity: .07;
        }
        .status-hero.pending::after   { background: #f59e0b; }
        .status-hero.approved::after  { background: #0077ff; }
        .status-hero.completed::after { background: #00d4aa; }
        .status-hero.cancelled::after { background: #dc2626; }

        .status-icon-wrap {
            width: 56px; height: 56px; border-radius: 14px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .status-icon-wrap.pending   { background: rgba(245,158,11,.1);  color: #d97706; border: 1.5px solid rgba(245,158,11,.2); }
        .status-icon-wrap.approved  { background: rgba(0,119,255,.1);   color: #2563eb; border: 1.5px solid rgba(0,119,255,.18); }
        .status-icon-wrap.completed { background: rgba(0,212,170,.13);  color: #00a878; border: 1.5px solid rgba(0,212,170,.22); }
        .status-icon-wrap.cancelled { background: rgba(220,38,38,.08);  color: #dc2626; border: 1.5px solid rgba(220,38,38,.15); }

        .status-hero-info { flex: 1; }
        .status-current-label { font-size: .7rem; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--tm); margin-bottom: 4px; }
        .status-current-name  { font-size: 1.2rem; font-weight: 700; color: var(--tp); text-transform: capitalize; }
        .status-current-desc  { font-size: .78rem; color: var(--tm); margin-top: 3px; }

        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 5px 13px; border-radius: 20px;
            font-size: .72rem; font-weight: 700; letter-spacing: .04em;
        }
        .status-badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .badge-pending   { background: rgba(245,158,11,.1);  color: #b45309; } .badge-pending::before   { background: #f59e0b; animation: pulse 1.8s infinite; }
        .badge-approved  { background: rgba(0,119,255,.1);   color: #1d4ed8; } .badge-approved::before  { background: #3b82f6; }
        .badge-completed { background: rgba(0,168,120,.1);   color: #065f46; } .badge-completed::before { background: #00a878; }
        .badge-cancelled { background: rgba(220,38,38,.08);  color: #b91c1c; } .badge-cancelled::before { background: #dc2626; }

        @keyframes pulse {
            0%,100% { opacity: 1; transform: scale(1); }
            50%      { opacity: .5; transform: scale(1.4); }
        }

        /* ── PROGRESS TRACKER ── */
        .tracker-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            padding: 1.5rem 1.8rem 1.8rem; margin-bottom: 1.2rem;
            animation: cardIn .5s .06s cubic-bezier(.22,1,.36,1) both;
        }

        .tracker-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm);
            display: flex; align-items: center; gap: 8px; margin-bottom: 2rem;
        }
        .tracker-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }

        /* Horizontal track */
        .track-steps { display: flex; align-items: flex-start; position: relative; }

        .track-step { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; }

        /* Connector line between steps */
        .track-step:not(:last-child)::after {
            content: ''; position: absolute;
            top: 20px; left: 50%; right: -50%;
            height: 2px; background: var(--border);
            z-index: 0;
        }
        .track-step.done:not(:last-child)::after  { background: #00a878; }
        .track-step.active:not(:last-child)::after { background: var(--border); }

        /* Animated fill for done connector */
        .track-step.done:not(:last-child)::before {
            content: ''; position: absolute;
            top: 20px; left: 50%; right: -50%;
            height: 2px; background: #00a878;
            z-index: 1;
            animation: lineGrow .6s cubic-bezier(.22,1,.36,1) both;
        }

        .step-node {
            width: 42px; height: 42px; border-radius: 50%; z-index: 2; position: relative;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            transition: all .3s;
        }
        .step-node.done {
            background: #00a878; border: 2px solid #00a878;
            box-shadow: 0 0 0 5px rgba(0,168,120,.12);
            animation: popIn .4s cubic-bezier(.22,1,.36,1) both;
        }
        .step-node.active {
            background: var(--card); border: 2px solid #0a1a15;
            box-shadow: 0 0 0 5px rgba(10,26,21,.07);
        }
        .step-node.idle {
            background: var(--light); border: 2px solid var(--border);
        }

        .step-node-inner { display: flex; align-items: center; justify-content: center; }

        .step-body { text-align: center; margin-top: .75rem; padding: 0 .25rem; }
        .step-name { font-size: .78rem; font-weight: 700; color: var(--tp); text-transform: capitalize; }
        .step-name.idle { color: var(--tm); }
        .step-date { font-size: .68rem; font-family: 'Space Mono',monospace; color: var(--tm); margin-top: 3px; }
        .step-date.none { color: transparent; user-select: none; }
        .step-pending-dot { font-size: .68rem; color: var(--tm); margin-top: 3px; }

        /* ── BACK LINK ── */
        .back-row { margin-top: .5rem; animation: cardIn .5s .14s cubic-bezier(.22,1,.36,1) both; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 7px;
            padding: .58rem 1rem; border-radius: 9px; border: 1.5px solid var(--border);
            background: none; color: var(--ts); font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 500;
            text-decoration: none; transition: background .14s, color .14s;
        }
        .btn-back:hover { background: var(--light); color: var(--tp); }

        /* MOBILE */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
        }
        @media (max-width: 640px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .user-name, .user-role-badge { display: none; }
            .tracker-card { padding: 1.2rem 1rem 1.4rem; }
            .step-node { width: 34px; height: 34px; }
            .track-step.done:not(:last-child)::before,
            .track-step:not(:last-child)::after { top: 17px; }
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
                    <span class="breadcrumb-current">Track #{{ $order->id }}</span>
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

            @php
                $statuses = ['pending', 'approved', 'completed'];
                $currentIdx = array_search($order->status, $statuses);
                if ($currentIdx === false) $currentIdx = -1; // cancelled or unknown

                $statusDescriptions = [
                    'pending'   => 'Your order has been received and is awaiting review.',
                    'approved'  => 'Your order has been approved and is being prepared.',
                    'completed' => 'Your order has been fulfilled successfully.',
                    'cancelled' => 'This order has been cancelled.',
                ];

                $statusDesc = $statusDescriptions[$order->status] ?? 'Order status updated.';

                $timestamps = [
                    'pending'   => $order->placed_at   ?? null,
                    'approved'  => $order->approved_at ?? null,
                    'completed' => $order->completed_at ?? null,
                ];
            @endphp

            {{-- Page Header --}}
            <div class="page-header">
                <div class="page-header-left">
                    <p class="page-eyebrow">Order Tracking</p>
                    <h1 class="page-title">
                        Track Order
                        <span class="order-id-badge">#{{ $order->id }}</span>
                    </h1>
                    <p class="page-subtitle">Placed {{ $order->placed_at ? $order->placed_at->format('M d, Y') : $order->created_at->format('M d, Y') }}</p>
                </div>
                <a href="{{ route('user.orders.show', $order) }}" class="btn-detail">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    View Full Details
                </a>
            </div>

            {{-- Status Hero --}}
            <div class="status-hero {{ $order->status }}">
                <div class="status-icon-wrap {{ $order->status }}">
                    @if($order->status === 'pending')
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    @elseif($order->status === 'approved')
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    @elseif($order->status === 'completed')
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    @else
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    @endif
                </div>

                <div class="status-hero-info">
                    <p class="status-current-label">Current Status</p>
                    <p class="status-current-name">{{ ucfirst($order->status) }}</p>
                    <p class="status-current-desc">{{ $statusDesc }}</p>
                </div>

                <span class="status-badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>

            {{-- Progress Tracker --}}
            @if($order->status !== 'cancelled')
            <div class="tracker-card">
                <p class="tracker-title">Order Progress</p>

                <div class="track-steps">
                    @foreach($statuses as $index => $status)
                        @php
                            $isDone   = $index < $currentIdx || ($index === $currentIdx);
                            $isActive = $index === $currentIdx;
                            $isIdle   = $index > $currentIdx;
                            $nodeClass = $isDone ? 'done' : ($isActive ? 'active' : 'idle');
                            $stepClass = $isDone ? 'done' : ($isActive ? 'active' : 'idle');
                            $ts = $timestamps[$status] ?? null;
                        @endphp
                        <div class="track-step {{ $stepClass }}">
                            <div class="step-node {{ $nodeClass }}">
                                <span class="step-node-inner">
                                    @if($isDone)
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @elseif($isActive)
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0a1a15" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            @if($status === 'pending')
                                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                                            @elseif($status === 'approved')
                                                <path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                                            @else
                                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                            @endif
                                        </svg>
                                    @else
                                        <span style="width:8px;height:8px;border-radius:50%;background:rgba(12,26,20,.18);display:block;"></span>
                                    @endif
                                </span>
                            </div>
                            <div class="step-body">
                                <p class="step-name {{ $isIdle ? 'idle' : '' }}">{{ ucfirst($status) }}</p>
                                @if($ts)
                                    <p class="step-date">{{ $ts->format('M d, Y') }}</p>
                                @elseif($isActive)
                                    <p class="step-pending-dot">In progress</p>
                                @else
                                    <p class="step-date none">—</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Back --}}
            <div class="back-row">
                <a href="{{ route('user.orders.index') }}" class="btn-back">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
                    Back to My Orders
                </a>
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
</script>

</body>
</html>
