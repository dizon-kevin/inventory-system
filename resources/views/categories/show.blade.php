<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $category->name }} — Storix</title>

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

        /* ════ CONTENT ════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }
        .page-wrap { width: 100%; max-width: 900px; margin: 0 auto; }

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        /* ── PAGE HEADER ── */
        .page-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.6rem; flex-wrap: wrap; }
        .page-title { font-size: 1.25rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        .btn-edit {
            display: inline-flex; align-items: center; gap: 7px;
            padding: .65rem 1.2rem; background: #0a1a15; border: none; border-radius: 9px;
            color: #d8f0e8; font-family: 'Sora',sans-serif; font-size: .84rem; font-weight: 600;
            cursor: pointer; text-decoration: none; letter-spacing: .03em; white-space: nowrap;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-edit:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,168,120,.2); }
        .btn-edit:active { transform: translateY(0); }

        /* ── CATEGORY INFO CARD ── */
        .info-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.4rem 1.5rem;
            display: flex; align-items: center; gap: 1.2rem;
            margin-bottom: 1.2rem;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }

        .cat-icon {
            width: 52px; height: 52px; flex-shrink: 0; border-radius: 13px;
            background: linear-gradient(135deg, rgba(0,212,170,.14), rgba(0,119,255,.08));
            border: 1.5px solid rgba(0,212,170,.18);
            display: flex; align-items: center; justify-content: center;
            color: #00a878;
        }

        .info-body { flex: 1; min-width: 0; }
        .info-name  { font-size: 1rem; font-weight: 700; color: var(--tp); }
        .info-meta  { display: flex; align-items: center; gap: 1rem; margin-top: 6px; flex-wrap: wrap; }

        .meta-chip {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: .72rem; color: var(--tm); font-weight: 500;
        }
        .meta-chip svg { color: var(--teal); opacity: .7; }

        .product-count-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px; border-radius: 20px;
            background: var(--teal-dim); color: #007a57;
            font-size: .7rem; font-weight: 700; letter-spacing: .04em;
        }

        /* ── PRODUCTS TABLE CARD ── */
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            animation: cardIn .5s .08s cubic-bezier(.22,1,.36,1) both;
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }

        .card-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm);
            display: flex; align-items: center; gap: 8px;
        }
        .card-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }

        .card-count {
            font-size: .72rem; font-weight: 700; font-family: 'Space Mono',monospace;
            color: var(--tm); background: var(--light); border: 1px solid var(--border);
            border-radius: 6px; padding: 2px 8px;
        }

        /* Table */
        .products-table { width: 100%; border-collapse: collapse; }

        .products-table th {
            font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            color: var(--tm); text-align: left; padding: .75rem 1.5rem;
            background: #fafbfa; border-bottom: 1px solid var(--border);
        }
        .products-table th:nth-child(3),
        .products-table th:nth-child(4) { text-align: right; }

        .products-table td {
            padding: .95rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-size: .855rem; color: var(--tp);
            vertical-align: middle;
        }
        .products-table tr:last-child td { border-bottom: none; }
        .products-table tr:hover td { background: #fafcfb; }

        .product-name { font-weight: 600; color: var(--tp); }
        .sku-chip {
            font-family: 'Space Mono',monospace; font-size: .72rem;
            color: var(--tm); background: var(--light);
            border: 1px solid var(--border); border-radius: 5px;
            padding: 2px 7px; display: inline-block;
        }

        .qty-cell { text-align: right; font-family: 'Space Mono',monospace; font-size: .82rem; font-weight: 700; color: var(--tp); }
        .price-cell { text-align: right; font-family: 'Space Mono',monospace; font-size: .82rem; color: var(--tp); }

        /* Stock badges */
        .stock-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 9px; border-radius: 20px;
            font-size: .68rem; font-weight: 700; letter-spacing: .03em;
        }
        .stock-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .stock-in       { background: rgba(0,168,120,.1);  color: #007a57; } .stock-in::before       { background: #00a878; }
        .stock-low      { background: rgba(234,179,8,.1);  color: #92700a; } .stock-low::before      { background: #ca8a04; }
        .stock-out      { background: rgba(220,38,38,.08); color: #b91c1c; } .stock-out::before      { background: #dc2626; }

        /* Empty state */
        .empty-state {
            padding: 3rem 1.5rem;
            text-align: center;
        }
        .empty-icon {
            width: 48px; height: 48px; border-radius: 14px;
            background: var(--light); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; color: var(--tm);
        }
        .empty-title { font-size: .9rem; font-weight: 600; color: var(--tp); }
        .empty-sub   { font-size: .78rem; color: var(--tm); margin-top: 4px; }

        /* Back link */
        .back-row { margin-top: 1.2rem; }
        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .65rem 1.1rem; border: 1.5px solid var(--border); border-radius: 9px;
            background: none; color: var(--ts); font-family: 'Sora',sans-serif; font-size: .84rem; font-weight: 500;
            cursor: pointer; text-decoration: none;
            transition: background .15s, color .15s, border-color .15s;
        }
        .btn-back:hover { background: var(--light); color: var(--tp); border-color: rgba(12,26,20,.15); }

        /* Mobile */
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
            .products-table th:nth-child(2),
            .products-table td:nth-child(2) { display: none; }
        }
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
            <a href="{{ route('admin.dashboard') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Products
            </a>
            <a href="{{ route('admin.orders.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                Orders
            </a>
            <div class="sb-divider"></div>
            <p class="sb-section" style="padding-top:0">Management</p>
            <a href="{{ route('admin.users.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Users
            </a>
            <a href="#" class="ni active">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Categories
            </a>
            <div class="sb-divider"></div>
            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M21 12h-2M5 12H3M16.24 16.24l1.41 1.41M6.34 17.66L4.93 19.07M12 21v-2M12 5V3"/></svg>
                Settings
            </a>
        </nav>
        <div class="sb-footer"><p class="sb-version">STORIX v1.0 · ADMIN</p></div>
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
                    <a href="#">Categories</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">{{ $category->name }}</span>
                </div>
            </div>

            <div class="topbar-right">
                <button class="topbar-icon-btn" aria-label="Notifications">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                </button>
                <div class="user-menu" id="userMenu">
                    <button class="user-trigger" onclick="toggleDropdown()">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
                        <span class="user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="user-role-badge">Admin</span>
                        <svg class="chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <div class="dd-header">
                            <p class="dd-name">{{ auth()->user()->name ?? 'Admin' }}</p>
                            <p class="dd-email">{{ auth()->user()->email ?? '' }}</p>
                        </div>
                        <div class="dd-body">
                            <a href="#" class="dd-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                My Profile
                            </a>
                            <a href="#" class="dd-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M21 12h-2M5 12H3M16.24 16.24l1.41 1.41M6.34 17.66L4.93 19.07M12 21v-2M12 5V3"/></svg>
                                Settings
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
            <div class="page-wrap">

                {{-- Page Header --}}
                <div class="page-header">
                    <div>
                        <h1 class="page-title">Category Details</h1>
                        <p class="page-subtitle">View category information and associated products</p>
                    </div>
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn-edit">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        Edit Category
                    </a>
                </div>

                {{-- Category Info Card --}}
                <div class="info-card">
                    <div class="cat-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </div>
                    <div class="info-body">
                        <p class="info-name">{{ $category->name }}</p>
                        <div class="info-meta">
                            <span class="meta-chip">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                Created {{ $category->created_at->format('M d, Y') }}
                            </span>
                            <span class="meta-chip">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                ID #{{ $category->id }}
                            </span>
                            <span class="product-count-badge">
                                {{ $category->products->count() }} {{ Str::plural('product', $category->products->count()) }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Products Table Card --}}
                <div class="table-card">
                    <div class="card-header">
                        <p class="card-title">Products in this Category</p>
                        <span class="card-count">{{ $category->products->count() }}</span>
                    </div>

                    @if($category->products->count() > 0)
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->products as $product)
                                <tr>
                                    <td>
                                        <span class="product-name">{{ $product->name }}</span>
                                    </td>
                                    <td>
                                        <span class="sku-chip">{{ $product->sku }}</span>
                                    </td>
                                    <td class="qty-cell">{{ $product->quantity }}</td>
                                    <td class="price-cell">${{ number_format($product->price, 2) }}</td>
                                    <td>
                                        @php
                                            $stockClass = match($product->stock_status) {
                                                'In Stock'  => 'stock-in',
                                                'Low Stock' => 'stock-low',
                                                default     => 'stock-out',
                                            };
                                        @endphp
                                        <span class="stock-badge {{ $stockClass }}">{{ $product->stock_status }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <p class="empty-title">No products yet</p>
                            <p class="empty-sub">This category doesn't have any products assigned to it.</p>
                        </div>
                    @endif
                </div>

                {{-- Back --}}
                <div class="back-row">
                    <a href="#" class="btn-back">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                        Back to Categories
                    </a>
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
</script>

</body>
</html>