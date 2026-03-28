<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Products — Storix</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sw: 240px;
            --th: 60px;
            --dark:   #060a11;
            --light:  #f2f5f3;
            --card:   #ffffff;
            --teal:   #00d4aa;
            --teal-dim: rgba(0,212,170,0.11);
            --tp: #0c1a14;
            --ts: rgba(12,26,20,0.5);
            --tm: rgba(12,26,20,0.35);
            --border: rgba(12,26,20,0.08);
            --st: rgba(220,240,232,0.52);
            --sab: rgba(0,212,170,0.1);
        }

        html, body { height: 100%; font-family: 'Sora', sans-serif; background: var(--light); color: var(--tp); }

        /* ── SHELL ── */
        .shell { display: flex; min-height: 100vh; }

        /* ════════════════════
           SIDEBAR
        ════════════════════ */
        .sidebar {
            width: var(--sw);
            flex-shrink: 0;
            background: var(--dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1);
            overflow: hidden;
        }

        .sidebar::before {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(rgba(0,212,170,0.13) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none; z-index: 0;
        }

        .sidebar::after {
            content: '';
            position: absolute;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(0,212,170,0.16), transparent 65%);
            top: -60px; left: -80px;
            border-radius: 50%;
            pointer-events: none; z-index: 0;
            animation: glowPulse 10s ease-in-out infinite alternate;
        }

        @keyframes glowPulse { 0%{opacity:.6;transform:scale(1)} 100%{opacity:1;transform:scale(1.2)} }

        .sb-brand {
            position: relative; z-index: 2;
            display: flex; align-items: center; gap: 10px;
            padding: 1.4rem 1.4rem 1rem;
            border-bottom: 1px solid rgba(0,212,170,0.07);
        }

        .sb-logomark {
            width: 36px; height: 36px;
            background: linear-gradient(135deg,#00d4aa,#0077ff);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 18px rgba(0,212,170,0.4);
            flex-shrink: 0;
        }

        .sb-logomark svg { width: 20px; height: 20px; fill: #060a11; }

        .sb-wordmark { font-family: 'Space Mono',monospace; font-size: 1.15rem; font-weight: 700; color: #e2eeea; letter-spacing: .09em; }
        .sb-wordmark em { font-style: normal; color: #00d4aa; }

        .sb-section { position: relative; z-index: 2; font-size: .62rem; letter-spacing: .12em; text-transform: uppercase; color: rgba(0,212,170,0.32); padding: 1.2rem 1.4rem 0.5rem; font-weight: 600; }

        .sb-nav { position: relative; z-index: 2; flex: 1; padding: 0.4rem 0.75rem; overflow-y: auto; }
        .sb-nav::-webkit-scrollbar { width: 0; }

        .ni {
            display: flex; align-items: center; gap: 10px;
            padding: .62rem .75rem;
            border-radius: 9px;
            text-decoration: none;
            color: var(--st);
            font-size: .84rem; font-weight: 500;
            transition: background .18s, color .18s;
            margin-bottom: 2px;
            position: relative;
        }

        .ni:hover { background: rgba(0,212,170,0.07); color: rgba(220,240,232,0.85); }

        .ni.active { background: var(--sab); color: #00d4aa; }
        .ni.active::before { content: ''; position: absolute; left: 0; top: 20%; bottom: 20%; width: 3px; background: #00d4aa; border-radius: 0 3px 3px 0; }
        .ni.active .ni-icon { color: #00d4aa; }

        .ni-icon { width: 18px; height: 18px; color: rgba(180,220,205,0.38); flex-shrink: 0; transition: color .18s; }

        .ni-badge { margin-left: auto; font-size: .62rem; font-weight: 700; font-family: 'Space Mono',monospace; background: rgba(255,180,50,0.15); color: #fbbf24; border-radius: 20px; padding: 1px 7px; }

        .sb-divider { height: 1px; background: rgba(0,212,170,0.06); margin: .6rem .5rem; }

        .sb-footer { position: relative; z-index: 2; padding: 1rem 1.1rem; border-top: 1px solid rgba(0,212,170,0.07); }
        .sb-version { font-size: .63rem; letter-spacing: .08em; color: rgba(0,212,170,0.22); font-family: 'Space Mono',monospace; text-align: center; }

        /* ════════════════════
           MAIN AREA
        ════════════════════ */
        .main { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* ════════════════════
           TOPBAR
        ════════════════════ */
        .topbar {
            height: var(--th);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.8rem;
            position: sticky; top: 0; z-index: 50;
            gap: 1rem;
        }

        .topbar-left { display: flex; align-items: center; gap: .75rem; }

        .sb-toggle { display: none; background: none; border: none; cursor: pointer; color: var(--ts); padding: 4px; border-radius: 6px; transition: background .15s; }
        .sb-toggle:hover { background: var(--light); }

        .topbar-title { font-size: 1.05rem; font-weight: 700; color: var(--tp); letter-spacing: -.01em; }

        .topbar-right { display: flex; align-items: center; gap: .85rem; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .5rem 1rem;
            background: var(--dark); color: #d8f0e8;
            font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            border: none; border-radius: 8px; cursor: pointer;
            text-decoration: none;
            transition: background .2s, transform .15s, box-shadow .2s;
            letter-spacing: .02em;
        }

        .btn-primary:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 4px 14px rgba(0,168,120,0.18); }

        .topbar-icon-btn {
            width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border);
            background: transparent; display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--ts); transition: background .15s,color .15s; position: relative;
        }

        .topbar-icon-btn:hover { background: var(--light); color: var(--tp); }
        .notif-dot { position: absolute; top: 7px; right: 7px; width: 7px; height: 7px; background: #00d4aa; border-radius: 50%; border: 2px solid #fff; }

        /* User dropdown */
        .user-menu { position: relative; }

        .user-trigger {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 10px 5px 6px;
            border: 1px solid var(--border); border-radius: 10px;
            cursor: pointer; background: transparent; font-family: 'Sora',sans-serif;
            transition: background .15s;
        }

        .user-trigger:hover { background: var(--light); }

        .user-avatar {
            width: 30px; height: 30px; border-radius: 8px;
            background: linear-gradient(135deg,#00d4aa,#0077ff);
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 700; font-family: 'Space Mono',monospace;
            color: #060a11; flex-shrink: 0;
        }

        .user-name { font-size: .82rem; font-weight: 600; color: var(--tp); max-width: 100px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .user-role-badge { font-size: .6rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; background: var(--teal-dim); color: #00a878; border-radius: 5px; padding: 1px 6px; }
        .chevron { color: var(--tm); transition: transform .2s; flex-shrink: 0; }
        .user-menu.open .chevron { transform: rotate(180deg); }

        .user-dropdown {
            position: absolute; top: calc(100% + 8px); right: 0; width: 220px;
            background: var(--card); border: 1px solid var(--border); border-radius: 12px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.12); overflow: hidden;
            opacity: 0; transform: translateY(-8px) scale(0.97); pointer-events: none;
            transition: opacity .18s, transform .18s; z-index: 200;
        }

        .user-menu.open .user-dropdown { opacity: 1; transform: translateY(0) scale(1); pointer-events: all; }

        .dd-header { padding: .9rem 1rem .75rem; border-bottom: 1px solid var(--border); }
        .dd-name { font-size: .85rem; font-weight: 600; color: var(--tp); }
        .dd-email { font-size: .74rem; color: var(--tm); margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-body { padding: .45rem .5rem; }

        .dd-item {
            display: flex; align-items: center; gap: 9px;
            padding: .58rem .7rem; border-radius: 7px;
            font-size: .82rem; color: var(--ts); text-decoration: none; cursor: pointer;
            transition: background .14s, color .14s;
            border: none; background: none; width: 100%;
            font-family: 'Sora',sans-serif; text-align: left;
        }

        .dd-item:hover { background: var(--light); color: var(--tp); }
        .dd-item.danger { color: #c0392b; }
        .dd-item.danger:hover { background: rgba(192,57,43,0.07); }
        .dd-divider { height: 1px; background: var(--border); margin: .35rem .5rem; }

        /* ════════════════════
           CONTENT
        ════════════════════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.6rem; flex-wrap: wrap; gap: .75rem; }
        .page-title { font-size: 1.25rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        /* ── FLASH ── */
        .flash-success {
            display: flex; align-items: center; gap: 10px;
            background: rgba(34,197,94,0.08); border: 1px solid rgba(34,197,94,0.2);
            border-radius: 10px; padding: .75rem 1rem;
            font-size: .82rem; color: #15803d;
            margin-bottom: 1.2rem;
            animation: fadeIn .4s ease;
        }

        @keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }

        /* ── SEARCH / FILTER BAR ── */
        .filter-bar {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1rem 1.2rem;
            display: flex; align-items: center; gap: .75rem;
            margin-bottom: 1.2rem;
            flex-wrap: wrap;
        }

        .filter-search-wrap {
            position: relative;
            flex: 1;
            min-width: 200px;
        }

        .filter-search-icon {
            position: absolute; left: 12px; top: 50%; transform: translateY(-50%);
            color: rgba(0,168,120,0.45); display: flex; pointer-events: none;
        }

        .filter-input {
            width: 100%;
            padding: .62rem 1rem .62rem 2.45rem;
            background: var(--light);
            border: 1.5px solid var(--border);
            border-radius: 9px;
            color: var(--tp);
            font-family: 'Sora',sans-serif; font-size: .84rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .filter-input::placeholder { color: var(--tm); }
        .filter-input:focus { border-color: rgba(0,168,120,0.45); box-shadow: 0 0 0 3px rgba(0,168,120,0.08); }

        .filter-select {
            padding: .62rem 2.2rem .62rem .9rem;
            background: var(--light);
            border: 1.5px solid var(--border);
            border-radius: 9px;
            color: var(--tp);
            font-family: 'Sora',sans-serif; font-size: .84rem;
            outline: none;
            cursor: pointer;
            appearance: none; -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%2300a878' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 10px center;
            transition: border-color .2s, box-shadow .2s;
            min-width: 150px;
        }

        .filter-select:focus { border-color: rgba(0,168,120,0.45); box-shadow: 0 0 0 3px rgba(0,168,120,0.08); }

        .btn-search {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .62rem 1.1rem;
            background: var(--dark); color: #d8f0e8;
            font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            border: none; border-radius: 9px; cursor: pointer;
            transition: background .2s, transform .15s;
            letter-spacing: .02em; white-space: nowrap;
        }

        .btn-search:hover { background: #122a20; transform: translateY(-1px); }

        .btn-clear {
            display: inline-flex; align-items: center; gap: 5px;
            padding: .62rem .9rem;
            background: transparent;
            border: 1.5px solid var(--border); border-radius: 9px; cursor: pointer;
            font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 500;
            color: var(--ts); text-decoration: none;
            transition: background .15s, color .15s;
            white-space: nowrap;
        }

        .btn-clear:hover { background: var(--light); color: var(--tp); }

        /* ── TABLE CARD ── */
        .table-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        .table-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.4rem;
            border-bottom: 1px solid var(--border);
        }

        .table-title {
            font-size: .9rem; font-weight: 700; color: var(--tp);
            display: flex; align-items: center; gap: 8px;
        }

        .title-dot { width: 7px; height: 7px; border-radius: 50%; background: var(--teal); box-shadow: 0 0 8px rgba(0,212,170,0.4); }

        .table-count { font-size: .72rem; color: var(--tm); font-weight: 400; }

        /* ── TABLE ── */
        .storix-table { width: 100%; border-collapse: collapse; font-size: .82rem; }

        .storix-table thead th {
            padding: .65rem 1.1rem;
            text-align: left; font-size: .63rem; font-weight: 600;
            letter-spacing: .09em; text-transform: uppercase;
            color: var(--tm); background: #fafbfa;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        .storix-table tbody td {
            padding: .82rem 1.1rem;
            border-bottom: 1px solid rgba(12,26,20,0.05);
            color: var(--ts); vertical-align: middle;
        }

        .storix-table tbody tr:last-child td { border-bottom: none; }
        .storix-table tbody tr { transition: background .12s; }
        .storix-table tbody tr:hover { background: #fafcfa; }

        /* Product thumb */
        .product-thumb {
            width: 40px; height: 40px;
            border-radius: 9px;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .product-thumb-placeholder {
            width: 40px; height: 40px;
            border-radius: 9px;
            background: var(--light);
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            color: var(--tm);
        }

        /* Product name cell */
        .td-name { font-weight: 600; color: var(--tp); }
        .td-mono { font-family: 'Space Mono',monospace; font-size: .76rem; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 9px; border-radius: 20px; font-size: .68rem; font-weight: 600; letter-spacing: .03em; white-space: nowrap; }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .badge-green  { background: rgba(34,197,94,0.1);  color: #16a34a; } .badge-green::before  { background: #16a34a; }
        .badge-yellow { background: rgba(251,191,36,0.1); color: #b45309; } .badge-yellow::before { background: #d97706; }
        .badge-red    { background: rgba(239,68,68,0.1);  color: #dc2626; } .badge-red::before    { background: #dc2626; }

        /* Action buttons */
        .actions { display: flex; align-items: center; gap: .5rem; }

        .act-btn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 6px;
            font-size: .73rem; font-weight: 500;
            text-decoration: none; cursor: pointer;
            border: 1px solid transparent;
            font-family: 'Sora',sans-serif;
            transition: background .15s, color .15s, border-color .15s;
            background: none;
        }

        .act-view  { color: #2563eb; border-color: rgba(37,99,235,0.18); } .act-view:hover  { background: rgba(37,99,235,0.07); }
        .act-edit  { color: #00a878; border-color: rgba(0,168,120,0.18); } .act-edit:hover  { background: rgba(0,168,120,0.07); }
        .act-del   { color: #dc2626; border-color: rgba(220,38,38,0.18); } .act-del:hover   { background: rgba(220,38,38,0.07); }

        /* ── EMPTY STATE ── */
        .empty-state {
            padding: 3.5rem 1.4rem; text-align: center;
        }

        .empty-icon {
            width: 52px; height: 52px; border-radius: 14px;
            background: var(--teal-dim);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; color: #00a878;
        }

        .empty-title { font-size: .92rem; font-weight: 600; color: var(--tp); margin-bottom: .35rem; }
        .empty-sub   { font-size: .8rem; color: var(--tm); }

        /* ── PAGINATION ── */
        .pagination-wrap {
            padding: .85rem 1.2rem;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: between;
        }

        /* Override Laravel default pagination to match Storix style */
        .pagination-wrap nav { width: 100%; }
        .pagination-wrap nav > div:first-child { font-size: .78rem; color: var(--tm); }
        .pagination-wrap nav > div:last-child { display: flex; gap: 4px; }

        .pagination-wrap [aria-label="Pagination Navigation"] span,
        .pagination-wrap [aria-label="Pagination Navigation"] a {
            display: inline-flex; align-items: center; justify-content: center;
            min-width: 32px; height: 32px; padding: 0 8px;
            border-radius: 7px; font-size: .78rem; font-weight: 500;
            text-decoration: none; border: 1px solid var(--border);
            color: var(--ts); background: var(--card); font-family: 'Sora',sans-serif;
            transition: background .15s, color .15s;
        }

        .pagination-wrap [aria-label="Pagination Navigation"] a:hover { background: var(--light); color: var(--tp); }
        .pagination-wrap [aria-current="page"] span { background: var(--dark); color: #d8f0e8; border-color: transparent; }

        /* Mobile overlay */
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
            .filter-bar { gap: .5rem; }
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

        <p class="sb-section">Main</p>
        <nav class="sb-nav">

            <a href="{{ route('admin.dashboard') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ auth()->user()->isAdmin() ? route('admin.products.index') : route('user.products.index') }}" class="ni active">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Products
            </a>

            <a href="{{ route('admin.orders.index') }}" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                Orders
                @if(isset($pendingOrders) && $pendingOrders > 0)
                    <span class="ni-badge">{{ $pendingOrders }}</span>
                @endif
            </a>

            <div class="sb-divider"></div>
            <p class="sb-section" style="padding-top:0">Management</p>

            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Users
            </a>

            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/>
                    <line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                Categories
            </a>

            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                Reports
            </a>

            <div class="sb-divider"></div>

            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M21 12h-2M5 12H3M16.24 16.24l1.41 1.41M6.34 17.66L4.93 19.07M12 21v-2M12 5V3"/>
                </svg>
                Settings
            </a>

        </nav>

        <div class="sb-footer">
            <p class="sb-version">STORIX v1.0 · {{ auth()->user()->isAdmin() ? 'ADMIN' : 'USER' }}</p>
        </div>
    </aside>

    {{-- ════ MAIN ════ --}}
    <div class="main">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="sb-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <p class="topbar-title">Products</p>
            </div>

            <div class="topbar-right">

                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.products.create') }}" class="btn-primary">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                        </svg>
                        Add Product
                    </a>
                @endif

                <button class="topbar-icon-btn" aria-label="Notifications">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                </button>

                {{-- User Dropdown --}}
                <div class="user-menu" id="userMenu">
                    <button class="user-trigger" onclick="toggleDropdown()" aria-label="User menu">
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</div>
                        <span class="user-name">{{ auth()->user()->name ?? 'User' }}</span>
                        <span class="user-role-badge">{{ auth()->user()->isAdmin() ? 'Admin' : 'User' }}</span>
                        <svg class="chevron" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
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

            <div class="page-header">
                <div>
                    <h1 class="page-title">Products</h1>
                    <p class="page-subtitle">Manage and browse your inventory catalogue</p>
                </div>
            </div>

            {{-- Flash --}}
            @if(session('success'))
                <div class="flash-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            {{-- ── SEARCH & FILTER BAR ── --}}
            <form method="GET" action="{{ auth()->user()->isAdmin() ? route('admin.products.index') : route('user.products.index') }}">
                <div class="filter-bar">
                    {{-- Search --}}
                    <div class="filter-search-wrap">
                        <span class="filter-search-icon">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </span>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by name or SKU..."
                            class="filter-input"
                        />
                    </div>

                    {{-- Category filter --}}
                    <select name="category_id" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Search button --}}
                    <button type="submit" class="btn-search">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        Search
                    </button>

                    {{-- Clear --}}
                    @if(request('search') || request('category_id'))
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.products.index') : route('user.products.index') }}" class="btn-clear">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                            Clear
                        </a>
                    @endif
                </div>
            </form>

            {{-- ── TABLE CARD ── --}}
            <div class="table-card">
                <div class="table-header">
                    <h3 class="table-title">
                        <span class="title-dot"></span>
                        All Products
                        <span class="table-count">{{ $products->total() }} items</span>
                    </h3>
                </div>

                @if($products->count() > 0)
                    <div style="overflow-x:auto">
                        <table class="storix-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    @if(auth()->user()->isAdmin())
                                        <th>Actions</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        {{-- Image --}}
                                        <td>
                                            @if($product->image)
                                                <img
                                                    src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="product-thumb"
                                                />
                                            @else
                                                <div class="product-thumb-placeholder">
                                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                                                        <polyline points="21 15 16 10 5 21"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </td>

                                        <td class="td-name">{{ $product->name }}</td>
                                        <td class="td-mono">{{ $product->sku }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td class="td-mono">{{ $product->quantity }}</td>
                                        <td class="td-mono">${{ number_format($product->price, 2) }}</td>

                                        {{-- Status --}}
                                        <td>
                                            @if($product->stock_status == 'In Stock')
                                                <span class="badge badge-green">In Stock</span>
                                            @elseif($product->stock_status == 'Low Stock')
                                                <span class="badge badge-yellow">Low Stock</span>
                                            @else
                                                <span class="badge badge-red">Out of Stock</span>
                                            @endif
                                        </td>

                                        {{-- Admin actions --}}
                                        @if(auth()->user()->isAdmin())
                                            <td>
                                                <div class="actions">
                                                    <a href="{{ route('admin.products.show', $product) }}" class="act-btn act-view">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                        View
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product) }}" class="act-btn act-edit">
                                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                        Edit
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline" onsubmit="return confirmDelete(event, '{{ $product->name }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="act-btn act-del">
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($products->hasPages())
                        <div class="pagination-wrap">
                            {{ $products->withQueryString()->links() }}
                        </div>
                    @endif

                @else
                    <div class="empty-state">
                        <div class="empty-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <p class="empty-title">No products found</p>
                        <p class="empty-sub">
                            @if(request('search') || request('category_id'))
                                Try adjusting your search or filter.
                            @else
                                Get started by adding your first product.
                            @endif
                        </p>
                    </div>
                @endif
            </div>

        </main>
    </div>
</div>

{{-- Delete confirmation modal --}}
<div id="deleteModal" style="display:none;position:fixed;inset:0;z-index:500;background:rgba(0,0,0,0.45);align-items:center;justify-content:center">
    <div style="background:#fff;border-radius:16px;padding:1.75rem 2rem;max-width:380px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.2);animation:cardIn .25s cubic-bezier(.22,1,.36,1)">
        <div style="width:44px;height:44px;border-radius:12px;background:rgba(220,38,38,0.08);display:flex;align-items:center;justify-content:center;margin-bottom:1rem;color:#dc2626">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
        </div>
        <p style="font-size:.98rem;font-weight:700;color:#0c1a14;margin-bottom:.4rem">Delete product?</p>
        <p id="deleteModalName" style="font-size:.82rem;color:rgba(12,26,20,0.5);margin-bottom:1.4rem;line-height:1.5"></p>
        <div style="display:flex;gap:.65rem">
            <button onclick="closeDeleteModal()" style="flex:1;padding:.68rem;border:1.5px solid rgba(12,26,20,0.1);border-radius:9px;background:none;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:500;color:rgba(12,26,20,0.5);cursor:pointer;transition:background .15s" onmouseover="this.style.background='#f2f5f3'" onmouseout="this.style.background='none'">Cancel</button>
            <button id="deleteConfirmBtn" style="flex:1;padding:.68rem;border:none;border-radius:9px;background:#dc2626;font-family:'Sora',sans-serif;font-size:.82rem;font-weight:600;color:#fff;cursor:pointer;transition:background .15s" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">Delete</button>
        </div>
    </div>
</div>

<script>
    // Dropdown
    function toggleDropdown() {
        document.getElementById('userMenu').classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        var m = document.getElementById('userMenu');
        if (m && !m.contains(e.target)) m.classList.remove('open');
    });

    // Sidebar mobile
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sbOverlay').classList.toggle('open');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sbOverlay').classList.remove('open');
    }

    // Delete confirm modal
    var pendingDeleteForm = null;

    function confirmDelete(e, productName) {
        e.preventDefault();
        pendingDeleteForm = e.target.closest('form');
        document.getElementById('deleteModalName').textContent =
            'Are you sure you want to permanently delete "' + productName + '"? This action cannot be undone.';
        var modal = document.getElementById('deleteModal');
        modal.style.display = 'flex';
        return false;
    }

    document.getElementById('deleteConfirmBtn').addEventListener('click', function() {
        if (pendingDeleteForm) pendingDeleteForm.submit();
    });

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        pendingDeleteForm = null;
    }

    // Close modal on backdrop click
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>

</body>
</html>