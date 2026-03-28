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

        .order-wrap { width: 100%; max-width: 820px; margin: 0 auto; }

        .page-header { margin-bottom: 1.6rem; display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; flex-wrap: wrap; }
        .page-header-left {}
        .page-title { font-size: 1.25rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; display: flex; align-items: center; gap: .6rem; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        /* ── FLASH MESSAGES ── */
        .flash { display: flex; align-items: center; gap: 10px; padding: .8rem 1.1rem; border-radius: 10px; font-size: .83rem; font-weight: 500; margin-bottom: 1.2rem; animation: cardIn .4s cubic-bezier(.22,1,.36,1) both; }
        .flash-success { background: rgba(0,168,120,.08); border: 1px solid rgba(0,168,120,.2); color: #007a57; }
        .flash-error   { background: rgba(220,38,38,.06);  border: 1px solid rgba(220,38,38,.18);  color: #b91c1c; }

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        /* ── ORDER SUMMARY CARD ── */
        .summary-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.4rem 1.5rem;
            margin-bottom: 1.2rem;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 1.2rem;
            align-items: start;
        }

        .summary-field {}
        .summary-label { font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--tm); margin-bottom: 4px; }
        .summary-value { font-size: .88rem; font-weight: 500; color: var(--tp); }
        .summary-sub   { font-size: .75rem; color: var(--tm); margin-top: 1px; }

        /* Status badge */
        .status-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px;
            font-size: .7rem; font-weight: 700; letter-spacing: .04em;
        }
        .status-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }

        .status-pending   { background: rgba(234,179,8,.1);   color: #92700a; } .status-pending::before   { background: #ca8a04; }
        .status-approved  { background: rgba(0,168,120,.1);   color: #007a57; } .status-approved::before  { background: #00a878; }
        .status-rejected  { background: rgba(220,38,38,.08);  color: #b91c1c; } .status-rejected::before  { background: #dc2626; }
        .status-completed { background: rgba(37,99,235,.1);   color: #1d4ed8; } .status-completed::before { background: #2563eb; }

        /* Total highlight */
        .total-block {
            background: linear-gradient(135deg, rgba(0,212,170,.06), rgba(0,119,255,.04));
            border: 1.5px solid rgba(0,212,170,.18);
            border-radius: 10px;
            padding: .7rem 1rem;
            text-align: right;
            white-space: nowrap;
        }
        .total-label { font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--tm); }
        .total-amount { font-size: 1.2rem; font-weight: 700; color: var(--tp); font-family: 'Space Mono',monospace; margin-top: 2px; }

        /* ── ITEMS CARD ── */
        .items-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 1.2rem;
            animation: cardIn .5s .08s cubic-bezier(.22,1,.36,1) both;
        }

        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .card-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm);
            display: flex; align-items: center; gap: 8px;
        }
        .card-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }

        .items-table { width: 100%; border-collapse: collapse; }
        .items-table th {
            font-size: .65rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
            color: var(--tm); text-align: left; padding: .75rem 1.5rem;
            background: #fafbfa; border-bottom: 1px solid var(--border);
        }
        .items-table th:last-child { text-align: right; }

        .items-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            font-size: .855rem;
            color: var(--tp);
            vertical-align: middle;
        }
        .items-table tr:last-child td { border-bottom: none; }
        .items-table td:last-child { text-align: right; font-family: 'Space Mono',monospace; font-size: .8rem; }

        .item-name { font-weight: 600; color: var(--tp); }
        .item-qty-badge {
            display: inline-flex; align-items: center; justify-content: center;
            width: 22px; height: 22px; border-radius: 6px;
            background: var(--light); border: 1px solid var(--border);
            font-size: .72rem; font-weight: 700; color: var(--ts);
            font-family: 'Space Mono',monospace;
            margin-right: 6px;
        }
        .item-unit-price { font-size: .78rem; color: var(--tm); }
        .item-total { color: var(--tp); font-weight: 700; }

        /* Items footer — subtotals */
        .items-footer {
            padding: .9rem 1.5rem;
            border-top: 1px solid var(--border);
            background: #fafbfa;
            display: flex; justify-content: flex-end; align-items: center; gap: 1.5rem;
        }
        .items-footer-label { font-size: .78rem; color: var(--tm); }
        .items-footer-value { font-size: .9rem; font-weight: 700; color: var(--tp); font-family: 'Space Mono',monospace; }

        /* ── UPDATE STATUS CARD ── */
        .update-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            animation: cardIn .5s .16s cubic-bezier(.22,1,.36,1) both;
        }

        .update-body {
            padding: 1.2rem 1.5rem;
            display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;
        }

        .update-label { font-size: .82rem; font-weight: 600; color: var(--tp); white-space: nowrap; }

        .field-select {
            padding: .65rem 2.4rem .65rem 1rem;
            background: var(--light);
            border: 1.5px solid var(--border);
            border-radius: 9px;
            color: var(--tp);
            font-family: 'Sora',sans-serif; font-size: .875rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            -webkit-appearance: none; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2300a878' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center;
            cursor: pointer;
        }
        .field-select:focus {
            border-color: rgba(0,168,120,.5);
            background-color: #ffffff;
            box-shadow: 0 0 0 3.5px rgba(0,168,120,.09);
        }

        .update-actions { display: flex; align-items: center; gap: .75rem; margin-left: auto; flex-wrap: wrap; }

        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .68rem 1.2rem; border: 1.5px solid var(--border); border-radius: 9px;
            background: none; color: var(--ts); font-family: 'Sora',sans-serif; font-size: .84rem; font-weight: 500;
            cursor: pointer; text-decoration: none;
            transition: background .15s, color .15s, border-color .15s;
        }
        .btn-back:hover { background: var(--light); color: var(--tp); border-color: rgba(12,26,20,.15); }

        .btn-save {
            display: inline-flex; align-items: center; gap: 7px;
            padding: .68rem 1.4rem; background: #0a1a15; border: none; border-radius: 9px;
            color: #d8f0e8; font-family: 'Sora',sans-serif; font-size: .84rem; font-weight: 600;
            cursor: pointer; letter-spacing: .03em;
            transition: background .2s, transform .15s, box-shadow .2s;
        }
        .btn-save:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,168,120,.2); }
        .btn-save:active { transform: translateY(0); }

        /* Mobile */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
            .summary-grid { grid-template-columns: 1fr 1fr; }
            .total-block { grid-column: span 2; text-align: left; }
        }

        @media (max-width: 600px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .user-name, .user-role-badge { display: none; }
            .summary-grid { grid-template-columns: 1fr; }
            .total-block { grid-column: span 1; }
            .update-body { flex-direction: column; align-items: flex-start; }
            .update-actions { margin-left: 0; width: 100%; }
            .items-table th:nth-child(2),
            .items-table td:nth-child(2) { display: none; }
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
            <a href="{{ route('admin.orders.index') }}" class="ni active">
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
                    <a href="{{ route('admin.orders.index') }}">Orders</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Order #{{ $order->id }}</span>
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
            <div class="order-wrap">

                <div class="page-header">
                    <div class="page-header-left">
                        <h1 class="page-title">
                            Order
                            <span style="font-family:'Space Mono',monospace; color: var(--teal); font-size:1.05rem;">#{{ $order->id }}</span>
                        </h1>
                        <p class="page-subtitle">View order details and update fulfillment status</p>
                    </div>
                </div>

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="flash flash-success">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="flash flash-error">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        {{ session('error') }}
                    </div>
                @endif

                {{-- Order Summary Card --}}
                <div class="summary-card">
                    <div class="summary-grid">
                        <div class="summary-field">
                            <p class="summary-label">Customer</p>
                            <p class="summary-value">{{ $order->user->name }}</p>
                            <p class="summary-sub">{{ $order->user->email }}</p>
                        </div>
                        <div class="summary-field">
                            <p class="summary-label">Placed</p>
                            <p class="summary-value">{{ $order->placed_at?->format('M d, Y') }}</p>
                            <p class="summary-sub">{{ $order->placed_at?->format('H:i') }}</p>
                        </div>
                        <div class="summary-field">
                            <p class="summary-label">Status</p>
                            @php $s = $order->status; @endphp
                            <span class="status-badge status-{{ $s }}">{{ ucfirst($s) }}</span>
                        </div>
                        <div class="total-block">
                            <p class="total-label">Total</p>
                            <p class="total-amount">${{ number_format($order->total_price, 2) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Order Items Card --}}
                <div class="items-card">
                    <div class="card-header">
                        <p class="card-title">Order Items</p>
                    </div>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Unit Price</th>
                                <th style="text-align:right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <span class="item-qty-badge">{{ $item->quantity }}</span>
                                    <span class="item-name">{{ $item->product->name }}</span>
                                </td>
                                <td>
                                    <span class="item-unit-price">${{ number_format($item->unit_price, 2) }}</span>
                                </td>
                                <td>
                                    <span class="item-total">${{ number_format($item->total_price, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="items-footer">
                        <span class="items-footer-label">Order Total</span>
                        <span class="items-footer-value">${{ number_format($order->total_price, 2) }}</span>
                    </div>
                </div>

                {{-- Update Status Card --}}
                <div class="update-card">
                    <div class="card-header">
                        <p class="card-title">Update Status</p>
                    </div>
                    <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="update-body">
                            <label class="update-label">Change order status to:</label>
                            <select name="status" class="field-select">
                                @foreach(['pending', 'approved', 'rejected', 'completed'] as $status)
                                    <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <div class="update-actions">
                                <a href="{{ route('admin.orders.index') }}" class="btn-back">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                                    Back
                                </a>
                                <button type="submit" class="btn-save">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Update Status
                                </button>
                            </div>
                        </div>
                    </form>
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