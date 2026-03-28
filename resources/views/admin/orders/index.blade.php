<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Orders — Storix</title>

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

        /* ════════════════════════
           SIDEBAR
        ════════════════════════ */
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

        .sb-brand {
            position: relative; z-index: 2;
            display: flex; align-items: center; gap: 10px;
            padding: 1.4rem 1.4rem 1rem;
            border-bottom: 1px solid rgba(0,212,170,0.07);
        }

        .sb-logomark {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #00d4aa, #0077ff);
            border-radius: 9px; display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 18px rgba(0,212,170,0.4); flex-shrink: 0;
        }

        .sb-logomark svg { width: 20px; height: 20px; fill: #060a11; }
        .sb-wordmark { font-family: 'Space Mono',monospace; font-size: 1.15rem; font-weight: 700; color: #e2eeea; letter-spacing: .09em; }
        .sb-wordmark em { font-style: normal; color: #00d4aa; }

        .sb-section { position: relative; z-index: 2; font-size: .62rem; letter-spacing: .12em; text-transform: uppercase; color: rgba(0,212,170,0.32); padding: 1.2rem 1.4rem 0.5rem; font-weight: 600; }

        .sb-nav { position: relative; z-index: 2; flex: 1; padding: 0.4rem 0.75rem; overflow-y: auto; }
        .sb-nav::-webkit-scrollbar { width: 0; }

        .ni {
            display: flex; align-items: center; gap: 10px;
            padding: .62rem .75rem; border-radius: 9px;
            text-decoration: none; color: var(--st);
            font-size: .84rem; font-weight: 500;
            transition: background .18s, color .18s;
            margin-bottom: 2px; position: relative;
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

        /* ════════════════════════
           MAIN
        ════════════════════════ */
        .main { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* TOPBAR */
        .topbar {
            height: var(--th); background: var(--card);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.8rem; position: sticky; top: 0; z-index: 50; gap: 1rem;
        }

        .topbar-left { display: flex; align-items: center; gap: .75rem; }
        .sb-toggle { display: none; background: none; border: none; cursor: pointer; color: var(--ts); padding: 4px; border-radius: 6px; transition: background .15s; }
        .sb-toggle:hover { background: var(--light); }
        .topbar-title { font-size: 1.05rem; font-weight: 700; color: var(--tp); letter-spacing: -.01em; }
        .topbar-right { display: flex; align-items: center; gap: .85rem; }

        .topbar-icon-btn {
            width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border);
            background: transparent; display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--ts); transition: background .15s, color .15s; position: relative;
        }

        .topbar-icon-btn:hover { background: var(--light); color: var(--tp); }
        .notif-dot { position: absolute; top: 7px; right: 7px; width: 7px; height: 7px; background: #00d4aa; border-radius: 50%; border: 2px solid #fff; }

        /* User dropdown */
        .user-menu { position: relative; }

        .user-trigger {
            display: flex; align-items: center; gap: 8px;
            padding: 5px 10px 5px 6px; border: 1px solid var(--border); border-radius: 10px;
            cursor: pointer; background: transparent; font-family: 'Sora',sans-serif; transition: background .15s;
        }

        .user-trigger:hover { background: var(--light); }

        .user-avatar {
            width: 30px; height: 30px; border-radius: 8px;
            background: linear-gradient(135deg,#00d4aa,#0077ff);
            display: flex; align-items: center; justify-content: center;
            font-size: .72rem; font-weight: 700; font-family: 'Space Mono',monospace;
            color: #060a11; flex-shrink: 0;
        }

        .user-name { font-size: .82rem; font-weight: 600; color: var(--tp); max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
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
        .dd-name  { font-size: .85rem; font-weight: 600; color: var(--tp); }
        .dd-email { font-size: .74rem; color: var(--tm); margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-body  { padding: .45rem .5rem; }

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

        /* ════════════════════════
           CONTENT
        ════════════════════════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }

        .page-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.6rem; flex-wrap: wrap; gap: .75rem; }
        .page-title    { font-size: 1.25rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        /* ── SUMMARY STRIP ── */
        .summary-strip {
            display: grid; grid-template-columns: repeat(5, 1fr);
            gap: 12px; margin-bottom: 1.5rem;
        }

        .scard {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 12px; padding: 1rem 1.15rem;
            display: flex; align-items: center; gap: .85rem;
            transition: box-shadow .18s, transform .18s;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }

        .scard:nth-child(1){animation-delay:.04s}
        .scard:nth-child(2){animation-delay:.08s}
        .scard:nth-child(3){animation-delay:.12s}
        .scard:nth-child(4){animation-delay:.16s}
        .scard:nth-child(5){animation-delay:.20s}

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        .scard:hover { box-shadow: 0 5px 18px rgba(0,0,0,.06); transform: translateY(-2px); }

        .scard-icon { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .scard-icon.all    { background: rgba(0,212,170,0.1);   color: #00a878; }
        .scard-icon.pend   { background: rgba(251,191,36,0.1);  color: #d97706; }
        .scard-icon.appr   { background: rgba(77,166,255,0.1);  color: #2563eb; }
        .scard-icon.done   { background: rgba(34,197,94,0.1);   color: #16a34a; }
        .scard-icon.canc   { background: rgba(239,68,68,0.1);   color: #dc2626; }

        .scard-val { font-family: 'Space Mono',monospace; font-size: 1.35rem; font-weight: 700; color: var(--tp); line-height: 1; }
        .scard-lbl { font-size: .7rem; color: var(--tm); margin-top: 2px; font-weight: 500; }

        /* ── FILTER ROW ── */
        .filter-bar {
            background: var(--card); border: 1px solid var(--border); border-radius: 12px;
            padding: .85rem 1.1rem;
            display: flex; align-items: center; gap: .7rem;
            margin-bottom: 1.2rem; flex-wrap: wrap;
        }

        .filter-label { font-size: .72rem; font-weight: 600; color: var(--tm); letter-spacing: .06em; text-transform: uppercase; white-space: nowrap; }

        .filter-pills { display: flex; gap: .4rem; flex-wrap: wrap; }

        .pill {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 12px; border-radius: 20px; font-size: .75rem; font-weight: 600;
            cursor: pointer; border: 1.5px solid transparent;
            font-family: 'Sora',sans-serif; text-decoration: none;
            transition: background .15s, border-color .15s, color .15s;
        }

        .pill-all  { background: rgba(12,26,20,0.06); color: var(--ts); border-color: var(--border); }
        .pill-all.active,
        .pill-all:hover { background: #0a1a15; color: #d8f0e8; border-color: transparent; }

        .pill-pend { background: rgba(251,191,36,0.08); color: #b45309; border-color: rgba(251,191,36,0.2); }
        .pill-pend.active,
        .pill-pend:hover { background: rgba(251,191,36,0.18); border-color: rgba(251,191,36,0.4); }

        .pill-appr { background: rgba(77,166,255,0.08); color: #2563eb; border-color: rgba(77,166,255,0.2); }
        .pill-appr.active,
        .pill-appr:hover { background: rgba(77,166,255,0.18); border-color: rgba(77,166,255,0.4); }

        .pill-done { background: rgba(34,197,94,0.08); color: #16a34a; border-color: rgba(34,197,94,0.2); }
        .pill-done.active,
        .pill-done:hover { background: rgba(34,197,94,0.18); border-color: rgba(34,197,94,0.4); }

        .pill-canc { background: rgba(239,68,68,0.08); color: #dc2626; border-color: rgba(239,68,68,0.2); }
        .pill-canc.active,
        .pill-canc:hover { background: rgba(239,68,68,0.18); border-color: rgba(239,68,68,0.4); }

        .pill::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .pill-pend::before { background: #d97706; }
        .pill-appr::before { background: #2563eb; }
        .pill-done::before { background: #16a34a; }
        .pill-canc::before { background: #dc2626; }
        .pill-all::before  { display: none; }

        .filter-spacer { flex: 1; }

        /* Search in filter bar */
        .filter-search-wrap { position: relative; }
        .filter-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: rgba(0,168,120,0.42); display: flex; pointer-events: none; }
        .filter-input {
            padding: .5rem .85rem .5rem 2.2rem;
            background: var(--light); border: 1.5px solid var(--border); border-radius: 8px;
            color: var(--tp); font-family: 'Sora',sans-serif; font-size: .8rem; outline: none;
            transition: border-color .2s, box-shadow .2s; min-width: 180px;
        }
        .filter-input::placeholder { color: var(--tm); }
        .filter-input:focus { border-color: rgba(0,168,120,0.45); box-shadow: 0 0 0 3px rgba(0,168,120,0.08); }

        /* ── TABLE CARD ── */
        .table-card {
            background: var(--card); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden;
            animation: cardIn .5s .25s cubic-bezier(.22,1,.36,1) both;
        }

        .table-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem 1.4rem; border-bottom: 1px solid var(--border);
        }

        .table-title { font-size: .9rem; font-weight: 700; color: var(--tp); display: flex; align-items: center; gap: 8px; }
        .title-dot   { width: 7px; height: 7px; border-radius: 50%; background: var(--teal); box-shadow: 0 0 8px rgba(0,212,170,0.4); }
        .table-count { font-size: .72rem; color: var(--tm); font-weight: 400; }

        /* Table */
        .storix-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
        .storix-table thead th { padding: .65rem 1.2rem; text-align: left; font-size: .63rem; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--tm); background: #fafbfa; border-bottom: 1px solid var(--border); white-space: nowrap; }
        .storix-table tbody td { padding: .9rem 1.2rem; border-bottom: 1px solid rgba(12,26,20,0.05); color: var(--ts); vertical-align: middle; }
        .storix-table tbody tr:last-child td { border-bottom: none; }
        .storix-table tbody tr { transition: background .12s; }
        .storix-table tbody tr:hover { background: #fafcfa; }

        .td-primary { font-weight: 600; color: var(--tp); }
        .td-mono    { font-family: 'Space Mono',monospace; font-size: .78rem; }

        /* User cell */
        .user-cell { display: flex; align-items: center; gap: 9px; }
        .u-avatar {
            width: 30px; height: 30px; border-radius: 8px;
            background: linear-gradient(135deg, rgba(0,212,170,0.15), rgba(0,119,255,0.15));
            border: 1px solid rgba(0,212,170,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: .65rem; font-weight: 700; font-family: 'Space Mono',monospace;
            color: #00a878; flex-shrink: 0;
        }
        .u-name  { font-size: .82rem; font-weight: 600; color: var(--tp); }
        .u-email { font-size: .7rem; color: var(--tm); margin-top: 1px; }

        /* Date */
        .td-date-main { color: var(--ts); font-size: .8rem; }
        .td-date-time { font-size: .7rem; color: var(--tm); margin-top: 1px; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 9px; border-radius: 20px; font-size: .68rem; font-weight: 600; letter-spacing: .03em; white-space: nowrap; }
        .badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .badge-yellow { background: rgba(251,191,36,0.1);  color: #b45309; } .badge-yellow::before { background: #d97706; }
        .badge-blue   { background: rgba(77,166,255,0.1);  color: #2563eb; } .badge-blue::before   { background: #2563eb; }
        .badge-green  { background: rgba(34,197,94,0.1);   color: #16a34a; } .badge-green::before  { background: #16a34a; }
        .badge-red    { background: rgba(239,68,68,0.1);   color: #dc2626; } .badge-red::before    { background: #dc2626; }

        /* Action */
        .act-btn {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 11px; border-radius: 6px;
            font-size: .74rem; font-weight: 500;
            text-decoration: none; cursor: pointer;
            border: 1px solid rgba(37,99,235,0.18);
            color: #2563eb;
            font-family: 'Sora',sans-serif;
            transition: background .15s;
        }

        .act-btn:hover { background: rgba(37,99,235,0.07); }

        /* Pagination */
        .pagination-wrap { padding: .85rem 1.2rem; border-top: 1px solid var(--border); }

        /* Empty state */
        .empty-wrap { padding: 4rem 2rem; text-align: center; }
        .empty-icon { width: 56px; height: 56px; border-radius: 16px; background: var(--teal-dim); display: flex; align-items: center; justify-content: center; margin: 0 auto 1.1rem; color: #00a878; }
        .empty-title { font-size: .95rem; font-weight: 700; color: var(--tp); margin-bottom: .35rem; }
        .empty-sub   { font-size: .8rem; color: var(--tm); }

        /* Mobile */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 1100px) {
            .summary-strip { grid-template-columns: repeat(3,1fr); }
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
            .summary-strip { grid-template-columns: repeat(2,1fr); }
        }

        @media (max-width: 560px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .user-name, .user-role-badge { display: none; }
            .summary-strip { grid-template-columns: repeat(2,1fr); }
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
                @php $pendingCount = $orders->where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ni-badge">{{ $pendingCount }}</span>
                @endif
            </a>

            <div class="sb-divider"></div>
            <p class="sb-section" style="padding-top:0">Management</p>

            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                Users
            </a>

            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                Categories
            </a>

            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Reports
            </a>

            <div class="sb-divider"></div>

            <a href="#" class="ni">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M21 12h-2M5 12H3M16.24 16.24l1.41 1.41M6.34 17.66L4.93 19.07M12 21v-2M12 5V3"/></svg>
                Settings
            </a>

        </nav>

        <div class="sb-footer">
            <p class="sb-version">STORIX v1.0 · ADMIN</p>
        </div>
    </aside>

    {{-- ════ MAIN ════ --}}
    <div class="main">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="topbar-left">
                <button class="sb-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <p class="topbar-title">Admin Orders</p>
            </div>

            <div class="topbar-right">
                <button class="topbar-icon-btn" aria-label="Notifications">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span class="notif-dot"></span>
                    @endif
                </button>

                {{-- User Dropdown --}}
                <div class="user-menu" id="userMenu">
                    <button class="user-trigger" onclick="toggleDropdown()" aria-label="User menu">
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

            <div class="page-header">
                <div>
                    <h1 class="page-title">Orders</h1>
                    <p class="page-subtitle">Manage and process all customer orders</p>
                </div>
            </div>

            @if($orders->count())

                {{-- ── SUMMARY STRIP ── --}}
                @php
                    $totalOrders     = $orders->total();
                    $cntPending      = $orders->getCollection()->where('status','pending')->count();
                    $cntApproved     = $orders->getCollection()->where('status','approved')->count();
                    $cntCompleted    = $orders->getCollection()->where('status','completed')->count();
                    $cntCancelled    = $orders->getCollection()->whereNotIn('status',['pending','approved','completed'])->count();
                @endphp

                <div class="summary-strip">
                    <div class="scard">
                        <div class="scard-icon all">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/></svg>
                        </div>
                        <div>
                            <div class="scard-val">{{ $totalOrders }}</div>
                            <div class="scard-lbl">Total Orders</div>
                        </div>
                    </div>
                    <div class="scard">
                        <div class="scard-icon pend">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <div class="scard-val">{{ $cntPending }}</div>
                            <div class="scard-lbl">Pending</div>
                        </div>
                    </div>
                    <div class="scard">
                        <div class="scard-icon appr">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <div class="scard-val">{{ $cntApproved }}</div>
                            <div class="scard-lbl">Approved</div>
                        </div>
                    </div>
                    <div class="scard">
                        <div class="scard-icon done">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div class="scard-val">{{ $cntCompleted }}</div>
                            <div class="scard-lbl">Completed</div>
                        </div>
                    </div>
                    <div class="scard">
                        <div class="scard-icon canc">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </div>
                        <div>
                            <div class="scard-val">{{ $cntCancelled }}</div>
                            <div class="scard-lbl">Cancelled</div>
                        </div>
                    </div>
                </div>

                {{-- ── STATUS FILTER PILLS ── --}}
                <div class="filter-bar">
                    <span class="filter-label">Filter</span>
                    <div class="filter-pills">
                        <a href="{{ route('admin.orders.index') }}" class="pill pill-all {{ !request('status') ? 'active' : '' }}">All orders</a>
                        <a href="{{ route('admin.orders.index', ['status'=>'pending']) }}"   class="pill pill-pend {{ request('status')==='pending'   ? 'active' : '' }}"><span></span>Pending</a>
                        <a href="{{ route('admin.orders.index', ['status'=>'approved']) }}"  class="pill pill-appr {{ request('status')==='approved'  ? 'active' : '' }}"><span></span>Approved</a>
                        <a href="{{ route('admin.orders.index', ['status'=>'completed']) }}" class="pill pill-done {{ request('status')==='completed' ? 'active' : '' }}"><span></span>Completed</a>
                        <a href="{{ route('admin.orders.index', ['status'=>'cancelled']) }}" class="pill pill-canc {{ request('status')==='cancelled' ? 'active' : '' }}"><span></span>Cancelled</a>
                    </div>
                    <div class="filter-spacer"></div>
                    <div class="filter-search-wrap">
                        <span class="filter-search-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input type="text" placeholder="Search order or user..." class="filter-input" />
                    </div>
                </div>

                {{-- ── TABLE CARD ── --}}
                <div class="table-card">
                    <div class="table-header">
                        <h3 class="table-title">
                            <span class="title-dot"></span>
                            All Orders
                            <span class="table-count">{{ $orders->total() }} total</span>
                        </h3>
                    </div>

                    <div style="overflow-x:auto">
                        <table class="storix-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Placed</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                    <tr>
                                        <td class="td-primary td-mono">#{{ $order->id }}</td>

                                        {{-- Customer cell with avatar --}}
                                        <td>
                                            <div class="user-cell">
                                                <div class="u-avatar">{{ strtoupper(substr($order->user->name ?? 'U', 0, 2)) }}</div>
                                                <div>
                                                    <div class="u-name">{{ $order->user->name }}</div>
                                                    <div class="u-email">{{ $order->user->email }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td style="font-size:.76rem;color:var(--ts)">
                                            {{ $order->items->count() ?? '—' }} {{ ($order->items->count() ?? 0) === 1 ? 'item' : 'items' }}
                                        </td>

                                        <td class="td-primary td-mono">${{ number_format($order->total_price, 2) }}</td>

                                        <td>
                                            @if($order->status === 'pending')
                                                <span class="badge badge-yellow">Pending</span>
                                            @elseif($order->status === 'approved')
                                                <span class="badge badge-blue">Approved</span>
                                            @elseif($order->status === 'completed')
                                                <span class="badge badge-green">Completed</span>
                                            @else
                                                <span class="badge badge-red">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="td-date-main">{{ $order->placed_at?->format('M d, Y') }}</div>
                                            <div class="td-date-time">{{ $order->placed_at?->format('H:i') }}</div>
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.orders.show', $order) }}" class="act-btn">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="pagination-wrap">
                            {{ $orders->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

            @else

                <div class="table-card">
                    <div class="empty-wrap">
                        <div class="empty-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>
                        </div>
                        <p class="empty-title">No orders found</p>
                        <p class="empty-sub">Orders placed by users will appear here.</p>
                    </div>
                </div>

            @endif

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