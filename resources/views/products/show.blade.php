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
        .breadcrumb-current { color: var(--tp); font-weight: 500; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .topbar-right { display: flex; align-items: center; gap: .85rem; }
        .btn-edit { display: inline-flex; align-items: center; gap: 6px; padding: .5rem 1rem; background: var(--dark); color: #d8f0e8; font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; transition: background .2s, transform .15s, box-shadow .2s; letter-spacing: .02em; }
        .btn-edit:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,168,120,0.18); }
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

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.6rem; flex-wrap: wrap; gap: .75rem; }
        .page-title    { font-size: 1.25rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        /* ── DETAIL GRID ── */
        .detail-grid { display: grid; grid-template-columns: 340px 1fr; gap: 1.4rem; align-items: start; animation: cardIn .5s cubic-bezier(.22,1,.36,1) both; }
        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        /* ── IMAGE PANEL ── */
        .img-panel {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden;
        }

        .product-image-wrap {
            width: 100%; aspect-ratio: 1;
            overflow: hidden;
            position: relative;
        }

        .product-image {
            width: 100%; height: 100%;
            object-fit: cover;
            transition: transform .4s ease;
        }

        .product-image-wrap:hover .product-image {
            transform: scale(1.04);
        }

        .product-image-placeholder {
            width: 100%; aspect-ratio: 1;
            background: linear-gradient(135deg, rgba(0,212,170,.06), rgba(0,119,255,.04));
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: .65rem; color: var(--tm);
        }

        .product-image-placeholder svg { color: rgba(0,212,170,.3); }
        .product-image-placeholder p { font-size: .8rem; color: var(--tm); }

        .img-panel-footer {
            padding: .9rem 1.1rem;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            gap: .5rem;
        }

        .img-panel-id { font-family: 'Space Mono',monospace; font-size: .72rem; color: var(--tm); }
        .img-panel-status { }

        /* ── INFO PANEL ── */
        .info-panel {
            display: flex; flex-direction: column; gap: 1.2rem;
        }

        /* Product title card */
        .title-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; padding: 1.4rem 1.5rem;
        }

        .product-name { font-size: 1.5rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; line-height: 1.2; margin-bottom: .6rem; }
        .product-category { font-size: .8rem; color: var(--tm); display: flex; align-items: center; gap: 5px; }
        .product-category svg { color: var(--teal); }

        .title-meta { display: flex; align-items: center; gap: .6rem; margin-top: .8rem; flex-wrap: wrap; }

        /* Status badge */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 700; letter-spacing: .04em; }
        .badge::before { content: ''; width: 6px; height: 6px; border-radius: 50%; }
        .badge-green  { background: rgba(34,197,94,.1);  color: #16a34a; } .badge-green::before  { background: #16a34a; }
        .badge-yellow { background: rgba(251,191,36,.1); color: #b45309; } .badge-yellow::before { background: #d97706; }
        .badge-red    { background: rgba(239,68,68,.1);  color: #dc2626; } .badge-red::before    { background: #dc2626; }

        /* Stats row */
        .stats-row {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;
        }

        .stat-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 12px; padding: 1rem 1.1rem;
            display: flex; flex-direction: column; gap: .35rem;
        }

        .stat-icon { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: .15rem; }
        .stat-icon.green  { background: rgba(34,197,94,.1);  color: #16a34a; }
        .stat-icon.teal   { background: rgba(0,212,170,.1);  color: #00a878; }
        .stat-icon.blue   { background: rgba(77,166,255,.1); color: #2563eb; }

        .stat-value { font-family: 'Space Mono',monospace; font-size: 1.3rem; font-weight: 700; color: var(--tp); line-height: 1; letter-spacing: -.02em; }
        .stat-label { font-size: .7rem; color: var(--tm); font-weight: 500; }

        /* Details card */
        .details-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden;
        }

        .details-header { padding: .9rem 1.4rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 8px; }
        .details-header-title { font-size: .78rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--tm); }
        .details-header-title::before { content: ''; display: inline-block; width: 3px; height: 13px; background: var(--teal); border-radius: 2px; margin-right: 8px; vertical-align: middle; }

        .details-list { padding: .6rem 0; }

        .detail-row {
            display: flex; align-items: flex-start; gap: 1rem;
            padding: .72rem 1.4rem;
            border-bottom: 1px solid rgba(12,26,20,0.05);
            transition: background .12s;
        }

        .detail-row:last-child { border-bottom: none; }
        .detail-row:hover { background: #fafcfa; }

        .detail-key { font-size: .72rem; font-weight: 600; letter-spacing: .07em; text-transform: uppercase; color: var(--tm); min-width: 110px; flex-shrink: 0; padding-top: 1px; }
        .detail-val { font-size: .85rem; color: var(--tp); flex: 1; word-break: break-word; }
        .detail-val.mono { font-family: 'Space Mono',monospace; font-size: .78rem; }
        .detail-val.muted { color: var(--ts); }

        /* Description card */
        .desc-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; padding: 1.2rem 1.4rem;
        }

        .desc-label { font-size: .72rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--tm); margin-bottom: .65rem; display: flex; align-items: center; gap: 8px; }
        .desc-label::before { content: ''; display: inline-block; width: 3px; height: 13px; background: var(--teal); border-radius: 2px; }
        .desc-text { font-size: .86rem; color: var(--ts); line-height: 1.7; }
        .desc-empty { font-size: .82rem; color: var(--tm); font-style: italic; }

        /* Back link */
        .back-link { display: inline-flex; align-items: center; gap: 6px; font-size: .82rem; color: #00a878; text-decoration: none; font-weight: 500; transition: color .18s; margin-top: .5rem; }
        .back-link:hover { color: #007558; }

        /* Mobile */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 1100px) { .detail-grid { grid-template-columns: 280px 1fr; } }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
            .detail-grid { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(3,1fr); }
        }
        @media (max-width: 560px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .stats-row { grid-template-columns: 1fr 1fr; }
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
            <div class="sb-logomark"><svg viewBox="0 0 24 24"><path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM17 13h4v4h-4zM13 17h4v4h-4z"/></svg></div>
            <span class="sb-wordmark">STO<em>RIX</em></span>
        </div>
        <p class="sb-section">Main</p>
        <nav class="sb-nav">
            <a href="{{ route('admin.dashboard') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}" class="ni active">
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
            <a href="{{ route('admin.categories.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Categories
            </a>
            <a href="{{ route('admin.reports') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Reports
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
                    <a href="{{ route('admin.products.index') }}">Products</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">{{ $product->name }}</span>
                </div>
            </div>
            <div class="topbar-right">
                <a href="{{ route('admin.products.edit', $product) }}" class="btn-edit">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    Edit Product
                </a>
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

            <div class="page-header">
                <div>
                    <h1 class="page-title">Product Details</h1>
                    <p class="page-subtitle">Full information and stock details for this product</p>
                </div>
            </div>

            <div class="detail-grid">

                {{-- ── LEFT: IMAGE PANEL ── --}}
                <div>
                    <div class="img-panel">
                        @if($product->image)
                            <div class="product-image-wrap">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-image" />
                            </div>
                        @else
                            <div class="product-image-placeholder">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <p>No image available</p>
                            </div>
                        @endif

                        <div class="img-panel-footer">
                            <span class="img-panel-id">#{{ $product->id }}</span>
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
                    <a href="{{ route('admin.products.index') }}" class="back-link" style="margin-top:1rem;display:inline-flex">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        Back to Products
                    </a>
                </div>

                {{-- ── RIGHT: INFO PANEL ── --}}
                <div class="info-panel">

                    {{-- Title card --}}
                    <div class="title-card">
                        <h2 class="product-name">{{ $product->name }}</h2>
                        <p class="product-category">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                            {{ $product->category->name ?? 'Uncategorized' }}
                        </p>
                        <div class="title-meta">
                            <span style="font-family:'Space Mono',monospace;font-size:.74rem;background:var(--light);color:var(--tm);padding:3px 8px;border-radius:6px;border:1px solid var(--border)">{{ $product->sku }}</span>
                            @if($product->stock_status === 'In Stock')
                                <span class="badge badge-green">In Stock</span>
                            @elseif($product->stock_status === 'Low Stock')
                                <span class="badge badge-yellow">Low Stock</span>
                            @else
                                <span class="badge badge-red">Out of Stock</span>
                            @endif
                        </div>
                    </div>

                    {{-- Stats row --}}
                    <div class="stats-row">
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                            </div>
                            <div class="stat-value">${{ number_format($product->price, 2) }}</div>
                            <div class="stat-label">Unit Price</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon teal">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            </div>
                            <div class="stat-value">{{ $product->quantity }}</div>
                            <div class="stat-label">In Stock</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon blue">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                            </div>
                            <div class="stat-value">${{ number_format($product->quantity * $product->price, 0) }}</div>
                            <div class="stat-label">Stock Value</div>
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($product->description)
                        <div class="desc-card">
                            <p class="desc-label">Description</p>
                            <p class="desc-text">{{ $product->description }}</p>
                        </div>
                    @endif

                    {{-- Details list --}}
                    <div class="details-card">
                        <div class="details-header">
                            <p class="details-header-title">Product Details</p>
                        </div>
                        <div class="details-list">
                            <div class="detail-row">
                                <span class="detail-key">Product ID</span>
                                <span class="detail-val mono">#{{ $product->id }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-key">SKU</span>
                                <span class="detail-val mono">{{ $product->sku }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-key">Category</span>
                                <span class="detail-val">{{ $product->category->name ?? 'N/A' }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-key">Unit Price</span>
                                <span class="detail-val mono">${{ number_format($product->price, 2) }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-key">Quantity</span>
                                <span class="detail-val mono">{{ $product->quantity }} units</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-key">Stock Value</span>
                                <span class="detail-val mono">${{ number_format($product->quantity * $product->price, 2) }}</span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-key">Status</span>
                                <span class="detail-val">
                                    @if($product->stock_status === 'In Stock')
                                        <span class="badge badge-green">In Stock</span>
                                    @elseif($product->stock_status === 'Low Stock')
                                        <span class="badge badge-yellow">Low Stock</span>
                                    @else
                                        <span class="badge badge-red">Out of Stock</span>
                                    @endif
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-key">Created</span>
                                <span class="detail-val muted">{{ $product->created_at->format('M d, Y') }} <span style="color:var(--tm)">{{ $product->created_at->format('H:i') }}</span></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-key">Last Updated</span>
                                <span class="detail-val muted">{{ $product->updated_at->format('M d, Y') }} <span style="color:var(--tm)">{{ $product->updated_at->format('H:i') }}</span></span>
                            </div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div style="display:flex;gap:.75rem;flex-wrap:wrap">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-edit" style="flex:1;justify-content:center">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Edit Product
                        </a>
                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete {{ addslashes($product->name) }}? This cannot be undone.')" style="flex:1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="width:100%;display:inline-flex;align-items:center;justify-content:center;gap:6px;padding:.68rem 1rem;background:rgba(220,38,38,.06);border:1.5px solid rgba(220,38,38,.2);border-radius:8px;color:#dc2626;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:600;cursor:pointer;transition:background .15s,border-color .15s" onmouseover="this.style.background='rgba(220,38,38,.1)'" onmouseout="this.style.background='rgba(220,38,38,.06)'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                Delete
                            </button>
                        </form>
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
</script>

</body>
</html>