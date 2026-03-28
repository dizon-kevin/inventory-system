<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin Dashboard — Storix</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@700&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 240px;
            --topbar-h: 60px;
            --bg-dark:   #060a11;
            --bg-dark2:  #0a1018;
            --bg-light:  #f2f5f3;
            --bg-card:   #ffffff;
            --teal:      #00d4aa;
            --teal-dim:  rgba(0,212,170,0.12);
            --teal-glow: rgba(0,212,170,0.25);
            --blue:      #4da6ff;
            --text-pri:  #0c1a14;
            --text-sec:  rgba(12,26,20,0.5);
            --text-mute: rgba(12,26,20,0.35);
            --border:    rgba(12,26,20,0.08);
            --sidebar-text: rgba(220,240,232,0.55);
            --sidebar-active-bg: rgba(0,212,170,0.1);
        }

        html, body {
            height: 100%;
            font-family: 'Sora', sans-serif;
            background: var(--bg-light);
            color: var(--text-pri);
        }

        /* ════════════════════════
           APP SHELL
        ════════════════════════ */
        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ════════════════════════
           SIDEBAR
        ════════════════════════ */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--bg-dark);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform 0.28s cubic-bezier(0.22,1,0.36,1);
            overflow: hidden;
        }

        /* Dot-grid texture */
        .sidebar::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(0,212,170,0.13) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            z-index: 0;
        }

        /* Glow orb */
        .sidebar::after {
            content: '';
            position: absolute;
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(0,212,170,0.16), transparent 65%);
            top: -60px; left: -80px;
            border-radius: 50%;
            pointer-events: none;
            z-index: 0;
            animation: glowPulse 10s ease-in-out infinite alternate;
        }

        @keyframes glowPulse {
            0%   { opacity: 0.6; transform: scale(1); }
            100% { opacity: 1;   transform: scale(1.2); }
        }

        /* Brand */
        .sidebar-brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 1.4rem 1.4rem 1rem;
            border-bottom: 1px solid rgba(0,212,170,0.07);
        }

        .sidebar-logomark {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #00d4aa, #0077ff);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 18px rgba(0,212,170,0.4);
            flex-shrink: 0;
        }

        .sidebar-logomark svg {
            width: 20px; height: 20px;
            fill: #060a11;
        }

        .sidebar-wordmark {
            font-family: 'Space Mono', monospace;
            font-size: 1.15rem;
            font-weight: 700;
            color: #e2eeea;
            letter-spacing: 0.09em;
        }

        .sidebar-wordmark em {
            font-style: normal;
            color: #00d4aa;
        }

        /* Nav label */
        .sidebar-section-label {
            position: relative;
            z-index: 2;
            font-size: 0.62rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(0,212,170,0.35);
            padding: 1.2rem 1.4rem 0.5rem;
            font-weight: 600;
        }

        /* Nav */
        .sidebar-nav {
            position: relative;
            z-index: 2;
            flex: 1;
            padding: 0.4rem 0.75rem;
            overflow-y: auto;
        }

        .sidebar-nav::-webkit-scrollbar { width: 0; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.62rem 0.75rem;
            border-radius: 9px;
            text-decoration: none;
            color: var(--sidebar-text);
            font-size: 0.84rem;
            font-weight: 500;
            transition: background 0.18s, color 0.18s;
            margin-bottom: 2px;
            position: relative;
        }

        .nav-item:hover {
            background: rgba(0,212,170,0.07);
            color: rgba(220,240,232,0.85);
        }

        .nav-item.active {
            background: var(--sidebar-active-bg);
            color: #00d4aa;
        }

        .nav-item.active .nav-icon {
            color: #00d4aa;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: #00d4aa;
            border-radius: 0 3px 3px 0;
        }

        .nav-icon {
            width: 18px; height: 18px;
            color: rgba(180,220,205,0.38);
            flex-shrink: 0;
            transition: color 0.18s;
        }

        .nav-badge {
            margin-left: auto;
            font-size: 0.62rem;
            font-weight: 700;
            font-family: 'Space Mono', monospace;
            background: rgba(255,180,50,0.15);
            color: #fbbf24;
            border-radius: 20px;
            padding: 1px 7px;
            letter-spacing: 0.04em;
        }

        .nav-divider {
            height: 1px;
            background: rgba(0,212,170,0.06);
            margin: 0.6rem 0.5rem;
        }

        /* Sidebar footer */
        .sidebar-footer {
            position: relative;
            z-index: 2;
            padding: 1rem 1.1rem;
            border-top: 1px solid rgba(0,212,170,0.07);
        }

        .sidebar-version {
            font-size: 0.63rem;
            letter-spacing: 0.08em;
            color: rgba(0,212,170,0.22);
            font-family: 'Space Mono', monospace;
            text-align: center;
        }

        /* ════════════════════════
           MAIN AREA
        ════════════════════════ */
        .main-area {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* ════════════════════════
           TOPBAR
        ════════════════════════ */
        .topbar {
            height: var(--topbar-h);
            background: var(--bg-card);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.8rem;
            position: sticky;
            top: 0;
            z-index: 50;
            gap: 1rem;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        /* Mobile sidebar toggle */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-sec);
            padding: 4px;
            border-radius: 6px;
            transition: background 0.15s;
        }

        .sidebar-toggle:hover { background: var(--bg-light); }

        .topbar-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--text-pri);
            letter-spacing: -0.01em;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        /* Add product button */
        .btn-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 1rem;
            background: var(--bg-dark);
            color: #d8f0e8;
            font-family: 'Sora', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
            letter-spacing: 0.02em;
        }

        .btn-add:hover {
            background: #122a20;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(0,168,120,0.18);
        }

        /* Notification bell */
        .topbar-icon-btn {
            width: 36px; height: 36px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--text-sec);
            transition: background 0.15s, color 0.15s;
            position: relative;
        }

        .topbar-icon-btn:hover {
            background: var(--bg-light);
            color: var(--text-pri);
        }

        .notif-dot {
            position: absolute;
            top: 7px; right: 7px;
            width: 7px; height: 7px;
            background: #00d4aa;
            border-radius: 50%;
            border: 2px solid #fff;
        }

        /* User dropdown */
        .user-menu {
            position: relative;
        }

        .user-trigger {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px 5px 6px;
            border: 1px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            background: transparent;
            font-family: 'Sora', sans-serif;
            transition: background 0.15s;
        }

        .user-trigger:hover { background: var(--bg-light); }

        .user-avatar {
            width: 30px; height: 30px;
            border-radius: 8px;
            background: linear-gradient(135deg, #00d4aa, #0077ff);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
            font-family: 'Space Mono', monospace;
            color: #060a11;
            flex-shrink: 0;
        }

        .user-name {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-pri);
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-role-badge {
            font-size: 0.6rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            background: var(--teal-dim);
            color: #00a878;
            border-radius: 5px;
            padding: 1px 6px;
        }

        .chevron-icon {
            color: var(--text-mute);
            transition: transform 0.2s;
            flex-shrink: 0;
        }

        .user-menu.open .chevron-icon {
            transform: rotate(180deg);
        }

        /* Dropdown panel */
        .user-dropdown {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 220px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: 0 12px 36px rgba(0,0,0,0.12), 0 0 0 1px rgba(0,0,0,0.04);
            overflow: hidden;
            opacity: 0;
            transform: translateY(-8px) scale(0.97);
            pointer-events: none;
            transition: opacity 0.18s, transform 0.18s;
            z-index: 200;
        }

        .user-menu.open .user-dropdown {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: all;
        }

        .dropdown-header {
            padding: 0.9rem 1rem 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .dropdown-uname {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-pri);
        }

        .dropdown-uemail {
            font-size: 0.74rem;
            color: var(--text-mute);
            margin-top: 1px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .dropdown-body {
            padding: 0.45rem 0.5rem;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 0.58rem 0.7rem;
            border-radius: 7px;
            font-size: 0.82rem;
            color: var(--text-sec);
            text-decoration: none;
            cursor: pointer;
            transition: background 0.14s, color 0.14s;
            border: none;
            background: none;
            width: 100%;
            font-family: 'Sora', sans-serif;
            text-align: left;
        }

        .dropdown-item:hover {
            background: var(--bg-light);
            color: var(--text-pri);
        }

        .dropdown-item.danger {
            color: #c0392b;
        }

        .dropdown-item.danger:hover {
            background: rgba(192,57,43,0.07);
            color: #c0392b;
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 0.35rem 0.5rem;
        }

        /* ════════════════════════
           CONTENT AREA
        ════════════════════════ */
        .content {
            flex: 1;
            padding: 1.8rem 2rem 2.5rem;
        }

        /* Page header row */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.8rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .page-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-pri);
            letter-spacing: -0.02em;
        }

        .page-subtitle {
            font-size: 0.78rem;
            color: var(--text-mute);
            margin-top: 2px;
        }

        /* ════════════════════════
           METRIC CARDS
        ════════════════════════ */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 1.8rem;
        }

        .metric-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.25rem 1.35rem;
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
            transition: box-shadow 0.2s, transform 0.2s;
            animation: cardIn 0.5s cubic-bezier(0.22,1,0.36,1) both;
        }

        .metric-card:nth-child(1) { animation-delay: 0.05s; }
        .metric-card:nth-child(2) { animation-delay: 0.10s; }
        .metric-card:nth-child(3) { animation-delay: 0.15s; }
        .metric-card:nth-child(4) { animation-delay: 0.20s; }

        @keyframes cardIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .metric-card:hover {
            box-shadow: 0 6px 24px rgba(0,0,0,0.07);
            transform: translateY(-2px);
        }

        .metric-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .metric-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .metric-icon.teal   { background: rgba(0,212,170,0.1); color: #00a878; }
        .metric-icon.yellow { background: rgba(251,191,36,0.1); color: #d97706; }
        .metric-icon.green  { background: rgba(34,197,94,0.1);  color: #16a34a; }
        .metric-icon.blue   { background: rgba(77,166,255,0.1); color: #2563eb; }

        .metric-trend {
            font-size: 0.68rem;
            font-weight: 600;
            padding: 2px 7px;
            border-radius: 20px;
        }

        .metric-trend.up   { background: rgba(34,197,94,0.1);  color: #16a34a; }
        .metric-trend.warn { background: rgba(251,191,36,0.1); color: #d97706; }
        .metric-trend.down { background: rgba(239,68,68,0.1);  color: #dc2626; }

        .metric-value {
            font-size: 1.7rem;
            font-weight: 700;
            color: var(--text-pri);
            line-height: 1;
            font-family: 'Space Mono', monospace;
            letter-spacing: -0.03em;
        }

        .metric-label {
            font-size: 0.75rem;
            color: var(--text-mute);
            margin-top: 3px;
            font-weight: 500;
        }

        .metric-sub {
            font-size: 0.7rem;
            color: var(--text-mute);
            margin-top: 4px;
        }

        /* ════════════════════════
           TABLES
        ════════════════════════ */
        .section-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            animation: cardIn 0.5s 0.25s cubic-bezier(0.22,1,0.36,1) both;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.1rem 1.4rem;
            border-bottom: 1px solid var(--border);
        }

        .section-title {
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--text-pri);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title-dot {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--teal);
            box-shadow: 0 0 8px var(--teal-glow);
        }

        .section-link {
            font-size: 0.77rem;
            color: #00a878;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: color 0.15s;
        }

        .section-link:hover { color: #007558; }

        /* Table */
        .storix-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }

        .storix-table thead th {
            padding: 0.65rem 1.2rem;
            text-align: left;
            font-size: 0.67rem;
            font-weight: 600;
            letter-spacing: 0.09em;
            text-transform: uppercase;
            color: var(--text-mute);
            background: #fafbfa;
            border-bottom: 1px solid var(--border);
        }

        .storix-table tbody td {
            padding: 0.85rem 1.2rem;
            border-bottom: 1px solid rgba(12,26,20,0.05);
            color: var(--text-sec);
            vertical-align: middle;
        }

        .storix-table tbody tr:last-child td {
            border-bottom: none;
        }

        .storix-table tbody tr {
            transition: background 0.12s;
        }

        .storix-table tbody tr:hover {
            background: #fafcfa;
        }

        .td-primary {
            font-weight: 600;
            color: var(--text-pri) !important;
        }

        .td-mono {
            font-family: 'Space Mono', monospace;
            font-size: 0.76rem;
        }

        /* Status badges */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.04em;
        }

        .badge::before {
            content: '';
            width: 5px; height: 5px;
            border-radius: 50%;
        }

        .badge-green  { background: rgba(34,197,94,0.1);  color: #16a34a; }
        .badge-green::before  { background: #16a34a; }
        .badge-yellow { background: rgba(251,191,36,0.1); color: #b45309; }
        .badge-yellow::before { background: #d97706; }
        .badge-red    { background: rgba(239,68,68,0.1);  color: #dc2626; }
        .badge-red::before    { background: #dc2626; }
        .badge-blue   { background: rgba(77,166,255,0.1); color: #2563eb; }
        .badge-blue::before   { background: #2563eb; }

        /* Empty state */
        .empty-state {
            padding: 2.5rem 1.4rem;
            text-align: center;
            color: var(--text-mute);
            font-size: 0.83rem;
        }

        .empty-state svg {
            width: 40px; height: 40px;
            color: rgba(0,212,170,0.25);
            margin: 0 auto 0.75rem;
            display: block;
        }

        /* ════════════════════════
           MOBILE
        ════════════════════════ */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 90;
        }

        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(calc(-1 * var(--sidebar-w)));
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-overlay.open {
                display: block;
            }
            .main-area {
                margin-left: 0;
            }
            .sidebar-toggle {
                display: flex;
            }
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 560px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
            .content {
                padding: 1.2rem 1rem 2rem;
            }
            .user-name,
            .user-role-badge {
                display: none;
            }
        }
    </style>
</head>
<body>

{{-- Sidebar overlay (mobile) --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="app-shell">

    {{-- ════════════════════════════════
         SIDEBAR
    ════════════════════════════════ --}}
    <aside class="sidebar" id="sidebar">

        {{-- Brand --}}
        <div class="sidebar-brand">
            <div class="sidebar-logomark">
                <svg viewBox="0 0 24 24"><path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM17 13h4v4h-4zM13 17h4v4h-4z"/></svg>
            </div>
            <span class="sidebar-wordmark">STO<em>RIX</em></span>
        </div>

        {{-- Navigation --}}
        <p class="sidebar-section-label">Main</p>
        <nav class="sidebar-nav">

            <a href="{{ route('admin.dashboard') }}" class="nav-item active">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.products.index') }}" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Products
            </a>

            <a href="{{ route('admin.orders.index') }}" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                Orders
                @if(isset($pendingOrders) && $pendingOrders > 0)
                    <span class="nav-badge">{{ $pendingOrders }}</span>
                @endif
            </a>

            <div class="nav-divider"></div>
            <p class="sidebar-section-label" style="padding-top:0">Management</p>

            <a href="{{ route('admin.users.index') }}" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.categories.index') }}" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/>
                    <line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/>
                    <line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/>
                </svg>
                Categories
            </a>

            <a href="{{ route('admin.reports') }}" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                </svg>
                Reports
            </a>

            <div class="nav-divider"></div>

            <a href="#" class="nav-item">
                <svg class="nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M21 12h-2M5 12H3M16.24 16.24l1.41 1.41M6.34 17.66L4.93 19.07M12 21v-2M12 5V3"/>
                </svg>
                Settings
            </a>

        </nav>

        <div class="sidebar-footer">
            <p class="sidebar-version">STORIX v1.0 · ADMIN</p>
        </div>
    </aside>

    {{-- ════════════════════════════════
         MAIN AREA
    ════════════════════════════════ --}}
    <div class="main-area">

        {{-- TOPBAR --}}
        <header class="topbar">
            <div class="topbar-left">
                {{-- Mobile toggle --}}
                <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Toggle sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                    </svg>
                </button>
                <div>
                    <p class="topbar-title">Admin Dashboard</p>
                </div>
            </div>

            <div class="topbar-right">

                {{-- Add product --}}
                <a href="{{ route('admin.products.create') }}" class="btn-add">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Add Product
                </a>

                {{-- Notification bell --}}
                <button class="topbar-icon-btn" aria-label="Notifications">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                    @if(isset($pendingOrders) && $pendingOrders > 0)
                        <span class="notif-dot"></span>
                    @endif
                </button>

                {{-- User dropdown --}}
                <div class="user-menu" id="userMenu">
                    <button class="user-trigger" onclick="toggleDropdown()" aria-label="User menu">
                        <div class="user-avatar">
                            {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}
                        </div>
                        <span class="user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                        <span class="user-role-badge">Admin</span>
                        <svg class="chevron-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    <div class="user-dropdown" id="userDropdown">
                        <div class="dropdown-header">
                            <p class="dropdown-uname">{{ auth()->user()->name ?? 'Admin User' }}</p>
                            <p class="dropdown-uemail">{{ auth()->user()->email ?? 'admin@storix.com' }}</p>
                        </div>
                        <div class="dropdown-body">
                            <a href="#" class="dropdown-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                                </svg>
                                My Profile
                            </a>
                            <a href="#" class="dropdown-item">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M21 12h-2M5 12H3M16.24 16.24l1.41 1.41M6.34 17.66L4.93 19.07M12 21v-2M12 5V3"/>
                                </svg>
                                Settings
                            </a>
                            <div class="dropdown-divider"></div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item danger">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
                                    </svg>
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
                    <h1 class="page-title">Overview</h1>
                    <p class="page-subtitle">{{ now()->format('l, F j, Y') }}</p>
                </div>
            </div>

            {{-- ── METRIC CARDS ── --}}
            <div class="metrics-grid">

                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-icon teal">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <span class="metric-trend up">+12%</span>
                    </div>
                    <div>
                        <div class="metric-value">{{ $totalProducts }}</div>
                        <div class="metric-label">Total Products</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-icon yellow">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <span class="metric-trend warn">Alert</span>
                    </div>
                    <div>
                        <div class="metric-value">{{ $lowStock }}</div>
                        <div class="metric-label">Low Stock Items</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-icon green">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                            </svg>
                        </div>
                        <span class="metric-trend up">Value</span>
                    </div>
                    <div>
                        <div class="metric-value">${{ number_format($totalValue, 0) }}</div>
                        <div class="metric-label">Total Stock Value</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-top">
                        <div class="metric-icon blue">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                            </svg>
                        </div>
                        <span class="metric-trend up">Orders</span>
                    </div>
                    <div>
                        <div class="metric-value">{{ $totalOrders }}</div>
                        <div class="metric-label">Total Orders</div>
                        <div class="metric-sub">Pending: {{ $pendingOrders }} · Done: {{ $completedOrders }}</div>
                    </div>
                </div>

            </div>

            {{-- ── RECENT PRODUCTS ── --}}
            <div class="section-card">
                <div class="section-header">
                    <h3 class="section-title">
                        <span class="section-title-dot"></span>
                        Recent Products
                    </h3>
                    <a href="{{ route('admin.products.index') }}" class="section-link">
                        View all
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>

                @if($recentProducts->count() > 0)
                    <div style="overflow-x:auto">
                        <table class="storix-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>SKU</th>
                                    <th>Category</th>
                                    <th>Qty</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProducts as $product)
                                    <tr>
                                        <td class="td-primary">{{ $product->name }}</td>
                                        <td class="td-mono">{{ $product->sku }}</td>
                                        <td>{{ $product->category->name ?? 'N/A' }}</td>
                                        <td class="td-mono">{{ $product->quantity }}</td>
                                        <td class="td-mono">${{ number_format($product->price, 2) }}</td>
                                        <td>
                                            @if($product->stock_status == 'In Stock')
                                                <span class="badge badge-green">In Stock</span>
                                            @elseif($product->stock_status == 'Low Stock')
                                                <span class="badge badge-yellow">Low Stock</span>
                                            @else
                                                <span class="badge badge-red">Out of Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        No products found.
                    </div>
                @endif
            </div>

            {{-- ── RECENT ORDERS ── --}}
            <div class="section-card">
                <div class="section-header">
                    <h3 class="section-title">
                        <span class="section-title-dot"></span>
                        Recent Orders
                    </h3>
                    <a href="{{ route('admin.orders.index') }}" class="section-link">
                        View all
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
                </div>

                @if($recentOrders->count())
                    <div style="overflow-x:auto">
                        <table class="storix-table">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Placed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="td-mono td-primary">#{{ $order->id }}</td>
                                        <td class="td-primary">{{ $order->user->name }}</td>
                                        <td class="td-mono">${{ number_format($order->total_price, 2) }}</td>
                                        <td>
                                            @if($order->status == 'pending')
                                                <span class="badge badge-yellow">Pending</span>
                                            @elseif($order->status == 'approved')
                                                <span class="badge badge-blue">Approved</span>
                                            @elseif($order->status == 'completed')
                                                <span class="badge badge-green">Completed</span>
                                            @else
                                                <span class="badge badge-red">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->placed_at?->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/>
                        </svg>
                        No recent orders.
                    </div>
                @endif
            </div>

        </main>
    </div>
</div>

<script>
    // Dropdown
    function toggleDropdown() {
        document.getElementById('userMenu').classList.toggle('open');
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        var menu = document.getElementById('userMenu');
        if (!menu.contains(e.target)) {
            menu.classList.remove('open');
        }
    });

    // Sidebar (mobile)
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
    }
</script>

</body>
</html>