<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reports — Storix</title>

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
        .topbar-title { font-size: 1.05rem; font-weight: 700; color: var(--tp); letter-spacing: -.01em; }
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

        /* ════════════════════
           CONTENT
        ════════════════════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.6rem; flex-wrap: wrap; gap: .75rem; }
        .page-title    { font-size: 1.25rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        .btn-export {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .55rem 1.1rem; background: var(--dark); color: #d8f0e8;
            font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            border: none; border-radius: 9px; cursor: pointer;
            text-decoration: none;
            transition: background .2s, transform .15s, box-shadow .2s;
            letter-spacing: .02em;
        }
        .btn-export:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,168,120,.18); }

        /* ── METRIC GRID ── */
        .metrics-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 1.5rem; }

        .metric-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; padding: 1.2rem 1.3rem;
            display: flex; flex-direction: column; gap: .8rem;
            transition: box-shadow .18s, transform .18s;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }

        .metric-card:nth-child(1){animation-delay:.04s}
        .metric-card:nth-child(2){animation-delay:.08s}
        .metric-card:nth-child(3){animation-delay:.12s}
        .metric-card:nth-child(4){animation-delay:.16s}

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        .metric-card:hover { box-shadow: 0 5px 18px rgba(0,0,0,.06); transform: translateY(-2px); }

        .metric-top { display: flex; align-items: center; justify-content: space-between; }
        .metric-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .metric-icon.green  { background: rgba(34,197,94,.1);  color: #16a34a; }
        .metric-icon.yellow { background: rgba(251,191,36,.1); color: #d97706; }
        .metric-icon.blue   { background: rgba(77,166,255,.1); color: #2563eb; }
        .metric-icon.teal   { background: rgba(0,212,170,.1);  color: #00a878; }

        .metric-trend { font-size: .68rem; font-weight: 600; padding: 2px 7px; border-radius: 20px; }
        .trend-up   { background: rgba(34,197,94,.1);  color: #16a34a; }
        .trend-warn { background: rgba(251,191,36,.1); color: #d97706; }
        .trend-down { background: rgba(239,68,68,.1);  color: #dc2626; }

        .metric-value { font-family: 'Space Mono',monospace; font-size: 1.65rem; font-weight: 700; color: var(--tp); line-height: 1; letter-spacing: -.03em; }
        .metric-label { font-size: .74rem; color: var(--tm); margin-top: 2px; font-weight: 500; }

        /* ── TWO-COL GRID ── */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 1.4rem; margin-bottom: 1.4rem; }

        /* ── SECTION CARD ── */
        .section-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden;
            animation: cardIn .5s .2s cubic-bezier(.22,1,.36,1) both;
        }

        .section-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.4rem; border-bottom: 1px solid var(--border); }
        .section-title  { font-size: .9rem; font-weight: 700; color: var(--tp); display: flex; align-items: center; gap: 8px; }
        .title-dot      { width: 7px; height: 7px; border-radius: 50%; background: var(--teal); box-shadow: 0 0 8px rgba(0,212,170,0.4); }
        .title-dot.warn { background: #d97706; box-shadow: 0 0 8px rgba(251,191,36,0.4); }
        .section-badge  { font-size: .68rem; font-weight: 700; font-family: 'Space Mono',monospace; background: rgba(251,191,36,.1); color: #b45309; border-radius: 20px; padding: 2px 8px; }

        /* ── INVENTORY BREAKDOWN BAR ── */
        .inv-breakdown { padding: 1.3rem 1.4rem; }
        .inv-row { display: flex; align-items: center; gap: .75rem; margin-bottom: .85rem; }
        .inv-row:last-child { margin-bottom: 0; }
        .inv-label { font-size: .78rem; font-weight: 500; color: var(--ts); width: 120px; flex-shrink: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .inv-bar-track { flex: 1; height: 7px; background: var(--light); border-radius: 10px; overflow: hidden; }
        .inv-bar-fill  { height: 100%; border-radius: 10px; transition: width .6s cubic-bezier(.22,1,.36,1); }
        .inv-value { font-family: 'Space Mono',monospace; font-size: .74rem; color: var(--ts); min-width: 70px; text-align: right; }

        /* ── STOCK STATUS DONUT LEGEND ── */
        .donut-section { padding: 1.3rem 1.4rem; display: flex; align-items: center; gap: 1.5rem; }
        .donut-canvas-wrap { position: relative; width: 120px; height: 120px; flex-shrink: 0; }
        #donutChart { width: 120px; height: 120px; }
        .donut-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; }
        .donut-center-val { font-family: 'Space Mono',monospace; font-size: 1.1rem; font-weight: 700; color: var(--tp); line-height: 1; }
        .donut-center-lbl { font-size: .62rem; color: var(--tm); margin-top: 2px; }
        .donut-legend { display: flex; flex-direction: column; gap: .7rem; flex: 1; }
        .legend-item { display: flex; align-items: center; gap: 8px; }
        .legend-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
        .legend-label { font-size: .78rem; color: var(--ts); flex: 1; }
        .legend-count { font-family: 'Space Mono',monospace; font-size: .74rem; color: var(--tp); font-weight: 700; }
        .legend-pct   { font-size: .7rem; color: var(--tm); }

        /* ── LOW STOCK TABLE ── */
        .full-card { animation: cardIn .5s .25s cubic-bezier(.22,1,.36,1) both; }

        .storix-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
        .storix-table thead th { padding: .65rem 1.2rem; text-align: left; font-size: .63rem; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--tm); background: #fafbfa; border-bottom: 1px solid var(--border); white-space: nowrap; }
        .storix-table tbody td { padding: .9rem 1.2rem; border-bottom: 1px solid rgba(12,26,20,0.05); color: var(--ts); vertical-align: middle; }
        .storix-table tbody tr:last-child td { border-bottom: none; }
        .storix-table tbody tr { transition: background .12s; }
        .storix-table tbody tr:hover { background: #fafcfa; }

        .td-primary { font-weight: 600; color: var(--tp); }
        .td-mono    { font-family: 'Space Mono',monospace; font-size: .78rem; }
        .td-qty-low { font-family: 'Space Mono',monospace; font-size: .78rem; font-weight: 700; color: #d97706; }

        /* Urgency bar */
        .qty-bar-wrap { display: flex; align-items: center; gap: 8px; }
        .qty-bar { flex: 1; height: 5px; background: var(--light); border-radius: 10px; overflow: hidden; max-width: 80px; }
        .qty-bar-fill { height: 100%; border-radius: 10px; }
        .qty-bar-fill.critical { background: #dc2626; }
        .qty-bar-fill.low      { background: #d97706; }

        /* Section footer */
        .section-footer { display: flex; align-items: center; justify-content: space-between; padding: .85rem 1.4rem; border-top: 1px solid var(--border); background: #fafbfa; }
        .footer-info { font-size: .76rem; color: var(--tm); }

        /* Empty state */
        .empty-state { padding: 2.5rem 1.4rem; text-align: center; color: var(--tm); }
        .empty-state p { font-size: .82rem; margin-top: .5rem; }

        /* Mobile */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 1100px) { .metrics-grid { grid-template-columns: repeat(2,1fr); } }
        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
            .two-col { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .metrics-grid { grid-template-columns: 1fr 1fr; }
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
            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Categories
            </a>
            <a href="{{ route('admin.reports') }}" class="ni active">
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
                <p class="topbar-title">Reports</p>
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
                    <h1 class="page-title">Inventory Reports</h1>
                    <p class="page-subtitle">{{ now()->format('F j, Y') }} — Stock overview and alerts</p>
                </div>
                @if($lowStockProducts->count() > 0)
                    <a href="{{ route('admin.reports') }}?export=pdf" class="btn-export">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export PDF
                    </a>
                @endif
            </div>

            {{-- ── METRIC CARDS ── --}}
            @php
                $inStock   = \App\Models\Product::where('quantity', '>=', 10)->count();
                $lowStock  = \App\Models\Product::where('quantity', '>', 0)->where('quantity', '<', 10)->count();
                $outStock  = \App\Models\Product::where('quantity', '<=', 0)->count();
                $totalProds = \App\Models\Product::count();
            @endphp

            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-icon green">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <span class="metric-trend trend-up">+8%</span>
                    </div>
                    <div>
                        <div class="metric-value">${{ number_format($totalValue, 0) }}</div>
                        <div class="metric-label">Total Inventory Value</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-icon teal">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        </div>
                        <span class="metric-trend trend-up">Active</span>
                    </div>
                    <div>
                        <div class="metric-value">{{ $totalProds }}</div>
                        <div class="metric-label">Total Products</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-icon yellow">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <span class="metric-trend trend-warn">Alert</span>
                    </div>
                    <div>
                        <div class="metric-value">{{ $lowStockProducts->count() }}</div>
                        <div class="metric-label">Low Stock Items</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-icon blue">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </div>
                        <span class="metric-trend trend-down">Critical</span>
                    </div>
                    <div>
                        <div class="metric-value">{{ $outStock }}</div>
                        <div class="metric-label">Out of Stock</div>
                    </div>
                </div>
            </div>

            {{-- ── TWO-COL: BREAKDOWN + DONUT ── --}}
            <div class="two-col">

                {{-- Inventory value breakdown by category --}}
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title"><span class="title-dot"></span>Value by Category</h3>
                    </div>
                    <div class="inv-breakdown">
                        @php
                            $byCategory = \App\Models\Product::with('category')
                                ->selectRaw('category_id, SUM(quantity * price) as total_val')
                                ->groupBy('category_id')
                                ->orderByDesc('total_val')
                                ->get();
                            $maxVal = $byCategory->max('total_val') ?: 1;
                            $colors = ['#00d4aa','#4da6ff','#a78bfa','#fbbf24','#34d399','#f87171'];
                        @endphp
                        @forelse($byCategory as $i => $row)
                            <div class="inv-row">
                                <span class="inv-label">{{ $row->category->name ?? 'Uncategorized' }}</span>
                                <div class="inv-bar-track">
                                    <div class="inv-bar-fill" style="width:{{ round(($row->total_val / $maxVal) * 100) }}%; background:{{ $colors[$i % count($colors)] }}"></div>
                                </div>
                                <span class="inv-value">${{ number_format($row->total_val, 0) }}</span>
                            </div>
                        @empty
                            <p style="font-size:.8rem;color:var(--tm);text-align:center;padding:1.5rem 0">No category data available.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Stock status donut --}}
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title"><span class="title-dot"></span>Stock Status</h3>
                    </div>
                    <div class="donut-section">
                        <div class="donut-canvas-wrap">
                            <canvas id="donutChart"></canvas>
                            <div class="donut-center">
                                <div class="donut-center-val">{{ $totalProds }}</div>
                                <div class="donut-center-lbl">Total</div>
                            </div>
                        </div>
                        <div class="donut-legend">
                            @php $total = max($totalProds, 1); @endphp
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#00d4aa"></div>
                                <span class="legend-label">In Stock</span>
                                <span class="legend-count">{{ $inStock }}</span>
                                <span class="legend-pct">{{ round(($inStock/$total)*100) }}%</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#fbbf24"></div>
                                <span class="legend-label">Low Stock</span>
                                <span class="legend-count">{{ $lowStock }}</span>
                                <span class="legend-pct">{{ round(($lowStock/$total)*100) }}%</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#f87171"></div>
                                <span class="legend-label">Out of Stock</span>
                                <span class="legend-count">{{ $outStock }}</span>
                                <span class="legend-pct">{{ round(($outStock/$total)*100) }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── LOW STOCK TABLE ── --}}
            <div class="section-card full-card">
                <div class="section-header">
                    <h3 class="section-title">
                        <span class="title-dot warn"></span>
                        Low Stock Items
                    </h3>
                    @if($lowStockProducts->count() > 0)
                        <span class="section-badge">{{ $lowStockProducts->count() }} items need attention</span>
                    @endif
                </div>

                @if($lowStockProducts->count() > 0)
                    <div style="overflow-x:auto">
                        <table class="storix-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Stock Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($lowStockProducts as $product)
                                    @php
                                        $pct = min(($product->quantity / 10) * 100, 100);
                                        $cls = $product->quantity <= 2 ? 'critical' : 'low';
                                    @endphp
                                    <tr>
                                        <td class="td-primary">{{ $product->name }}</td>
                                        <td class="td-mono">{{ $product->sku }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td>
                                            <div class="qty-bar-wrap">
                                                <span class="td-qty-low">{{ $product->quantity }}</span>
                                                <div class="qty-bar">
                                                    <div class="qty-bar-fill {{ $cls }}" style="width:{{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="td-mono">${{ number_format($product->price, 2) }}</td>
                                        <td class="td-mono">${{ number_format($product->quantity * $product->price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="section-footer">
                        <span class="footer-info">
                            Showing {{ $lowStockProducts->count() }} items with quantity ≤ 10
                        </span>
                        <a href="{{ route('admin.reports') }}?export=pdf" class="btn-export" style="padding:.48rem .9rem;font-size:.76rem">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Export PDF
                        </a>
                    </div>
                @else
                    <div class="empty-state">
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:rgba(0,212,170,.3);margin:0 auto"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <p>All products are sufficiently stocked.</p>
                    </div>
                @endif
            </div>

        </main>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
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

    // Donut chart
    var ctx = document.getElementById('donutChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $inStock }}, {{ $lowStock }}, {{ $outStock }}],
                    backgroundColor: ['#00d4aa', '#fbbf24', '#f87171'],
                    borderWidth: 0,
                    hoverOffset: 4,
                }]
            },
            options: {
                cutout: '72%',
                plugins: { legend: { display: false }, tooltip: { enabled: true } },
                animation: { animateRotate: true, duration: 800 },
            }
        });
    }
</script>

</body>
</html>