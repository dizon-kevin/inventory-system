{{-- resources/views/user/products/index.blade.php --}}
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
        .topbar-icon-btn { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ts); transition: background .15s, color .15s; text-decoration: none; position: relative; }
        .topbar-icon-btn:hover { background: var(--light); color: var(--tp); }
        .tb-badge { position: absolute; top: -4px; right: -4px; width: 16px; height: 16px; border-radius: 50%; background: #00a878; color: #fff; font-size: .55rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid var(--light); font-family: 'Space Mono',monospace; }
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

        /* PAGE HEADER */
        .page-header { margin-bottom: 1.5rem; display: flex; align-items: flex-end; justify-content: space-between; flex-wrap: wrap; gap: 1rem; }
        .page-greeting { font-size: .75rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; color: #00a878; margin-bottom: 3px; }
        .page-title { font-size: 1.3rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }
        .page-header-actions { display: flex; align-items: center; gap: .6rem; }

        .btn-action {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .58rem 1.1rem; border-radius: 9px;
            font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            text-decoration: none; cursor: pointer; border: none;
            transition: background .15s, transform .13s, box-shadow .15s; position: relative;
        }
        .btn-action:active { transform: translateY(0) !important; }
        .btn-cart { background: var(--teal-dim); color: #00a878; border: 1.5px solid rgba(0,212,170,.22); }
        .btn-cart:hover { background: rgba(0,212,170,.18); transform: translateY(-1px); }
        .cart-badge { position: absolute; top: -5px; right: -5px; width: 17px; height: 17px; border-radius: 50%; background: #00a878; color: #fff; font-size: .57rem; font-weight: 700; font-family: 'Space Mono',monospace; display: flex; align-items: center; justify-content: center; border: 2px solid var(--light); }

        /* FILTER BAR */
        .filter-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            padding: 1rem 1.3rem; margin-bottom: 1.3rem;
            display: flex; align-items: center; gap: .75rem; flex-wrap: wrap;
            animation: cardIn .45s cubic-bezier(.22,1,.36,1) both;
        }
        @keyframes cardIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

        .filter-label { font-size: .7rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase; color: var(--tm); flex-shrink: 0; }

        .filter-search-wrap { position: relative; flex: 1; min-width: 180px; }
        .filter-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--tm); pointer-events: none; }
        .filter-input {
            width: 100%; padding: .6rem .9rem .6rem 2.2rem;
            background: var(--light); border: 1.5px solid var(--border); border-radius: 9px;
            font-family: 'Sora',sans-serif; font-size: .82rem; color: var(--tp); outline: none;
            transition: border-color .2s, background .2s, box-shadow .2s;
        }
        .filter-input:focus { border-color: rgba(0,168,120,.45); background: #fff; box-shadow: 0 0 0 3.5px rgba(0,168,120,.08); }
        .filter-input:hover:not(:focus) { border-color: rgba(12,26,20,.14); }

        .filter-select {
            padding: .6rem 2.2rem .6rem .9rem;
            background: var(--light); border: 1.5px solid var(--border); border-radius: 9px;
            font-family: 'Sora',sans-serif; font-size: .82rem; color: var(--tp); outline: none;
            -webkit-appearance: none; appearance: none; cursor: pointer; min-width: 160px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2300a878' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 10px center;
            transition: border-color .2s, background-color .2s, box-shadow .2s;
        }
        .filter-select:focus { border-color: rgba(0,168,120,.45); background-color: #fff; box-shadow: 0 0 0 3.5px rgba(0,168,120,.08); }

        .btn-filter {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .6rem 1.1rem; background: #0a1a15; border: none; border-radius: 9px;
            color: #d8f0e8; font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            cursor: pointer; transition: background .18s, transform .13s, box-shadow .18s;
        }
        .btn-filter:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 5px 14px rgba(0,168,120,.18); }
        .btn-filter:active { transform: translateY(0); }

        .btn-clear {
            display: inline-flex; align-items: center; gap: 5px;
            padding: .6rem .9rem; border-radius: 9px; border: 1.5px solid var(--border);
            background: none; font-family: 'Sora',sans-serif; font-size: .8rem; color: var(--tm);
            text-decoration: none; transition: background .14s, color .14s;
        }
        .btn-clear:hover { background: var(--light); color: var(--tp); }

        /* RESULTS BAR */
        .results-bar { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; flex-wrap: wrap; gap: .5rem; }
        .results-count { font-size: .76rem; color: var(--tm); }
        .results-count strong { color: var(--tp); font-weight: 600; }

        /* VIEW TOGGLE */
        .view-toggle { display: flex; gap: 3px; background: var(--light); border-radius: 8px; padding: 3px; border: 1px solid var(--border); }
        .vt-btn { width: 28px; height: 28px; border-radius: 6px; border: none; background: none; cursor: pointer; color: var(--tm); display: flex; align-items: center; justify-content: center; transition: background .14s, color .14s; }
        .vt-btn.active { background: var(--card); color: var(--tp); box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .vt-btn:hover:not(.active) { color: var(--ts); }

        /* PRODUCT GRID */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1rem;
        }

        .product-grid.list-view { grid-template-columns: 1fr; }

        /* PRODUCT CARD */
        .product-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px;
            overflow: hidden; display: flex; flex-direction: column;
            transition: box-shadow .2s, transform .2s, border-color .2s;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }
        .product-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.09); transform: translateY(-2px); border-color: rgba(0,212,170,.18); }

        /* Stagger animation per card */
        .product-card:nth-child(1)  { animation-delay: .04s; }
        .product-card:nth-child(2)  { animation-delay: .08s; }
        .product-card:nth-child(3)  { animation-delay: .12s; }
        .product-card:nth-child(4)  { animation-delay: .16s; }
        .product-card:nth-child(5)  { animation-delay: .20s; }
        .product-card:nth-child(6)  { animation-delay: .24s; }
        .product-card:nth-child(7)  { animation-delay: .28s; }
        .product-card:nth-child(8)  { animation-delay: .32s; }
        .product-card:nth-child(9)  { animation-delay: .36s; }

        /* IMAGE */
        .card-image {
            width: 100%; aspect-ratio: 4/3; overflow: hidden; position: relative;
            background: #eef1ef;
        }

        .card-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s cubic-bezier(.22,1,.36,1); display: block; }
        .product-card:hover .card-image img { transform: scale(1.04); }

        .card-no-image {
            width: 100%; height: 100%;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            gap: 6px; color: rgba(12,26,20,.18);
        }
        .card-no-image span { font-size: .72rem; font-weight: 500; color: var(--tm); }

        .card-stock-overlay {
            position: absolute; top: 10px; right: 10px;
        }

        /* CARD BODY */
        .card-body { padding: 1rem 1.1rem; flex: 1; display: flex; flex-direction: column; gap: .35rem; }

        .card-category {
            font-size: .66rem; font-weight: 700; letter-spacing: .09em; text-transform: uppercase;
            color: #00a878;
        }

        .card-name { font-size: .93rem; font-weight: 700; color: var(--tp); line-height: 1.3; }

        .card-price {
            font-family: 'Space Mono', monospace; font-size: 1rem; font-weight: 700;
            color: var(--tp); letter-spacing: -.02em; margin-top: auto; padding-top: .5rem;
        }

        /* CARD FOOTER */
        .card-footer {
            padding: .75rem 1.1rem; border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between; gap: .5rem;
        }

        .card-view-btn {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: .78rem; font-weight: 500; color: var(--ts); text-decoration: none;
            padding: .4rem .7rem; border-radius: 7px; border: 1.5px solid var(--border);
            background: none; transition: background .14s, color .14s, border-color .14s;
        }
        .card-view-btn:hover { background: var(--light); color: var(--tp); border-color: rgba(12,26,20,.15); }

        .card-add-btn {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: .78rem; font-weight: 600; color: #00a878;
            padding: .4rem .85rem; border-radius: 7px;
            border: 1.5px solid rgba(0,212,170,.25); background: rgba(0,212,170,.08);
            font-family: 'Sora',sans-serif; cursor: pointer;
            transition: background .14s, transform .12s, box-shadow .14s;
        }
        .card-add-btn:hover { background: rgba(0,212,170,.16); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,168,120,.15); }
        .card-add-btn:active { transform: translateY(0); }

        /* STOCK BADGES */
        .stock-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 8px; border-radius: 20px;
            font-size: .63rem; font-weight: 700; letter-spacing: .04em; white-space: nowrap;
        }
        .stock-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }
        .stock-in     { background: rgba(0,168,120,.12); color: #00875a; } .stock-in::before     { background: #00a878; }
        .stock-low    { background: rgba(234,179,8,.12);  color: #92400e; } .stock-low::before    { background: #eab308; }
        .stock-out    { background: rgba(220,38,38,.08);  color: #b91c1c; } .stock-out::before    { background: #dc2626; }

        /* LIST VIEW card overrides */
        .list-view .product-card { flex-direction: row; max-height: 120px; }
        .list-view .card-image { width: 120px; min-width: 120px; aspect-ratio: unset; border-radius: 0; }
        .list-view .card-body { flex-direction: row; align-items: center; gap: 1rem; flex-wrap: wrap; padding: .85rem 1.1rem; }
        .list-view .card-name { flex: 1; min-width: 120px; }
        .list-view .card-price { padding-top: 0; margin-top: 0; white-space: nowrap; }
        .list-view .card-footer { border-top: none; border-left: 1px solid var(--border); padding: .85rem 1rem; flex-direction: column; min-width: 130px; justify-content: center; }
        .list-view .card-stock-overlay { position: static; }
        .list-view .card-category { display: none; }

        /* EMPTY STATE */
        .empty-wrap { padding: 4rem 1.5rem; text-align: center; background: var(--card); border: 1px solid var(--border); border-radius: 14px; }
        .empty-icon  { width: 52px; height: 52px; margin: 0 auto 1rem; color: rgba(12,26,20,.12); }
        .empty-title { font-size: .95rem; font-weight: 600; color: var(--ts); }
        .empty-sub   { font-size: .78rem; color: var(--tm); margin-top: 5px; }
        .empty-cta   { display: inline-flex; align-items: center; gap: 6px; margin-top: 1.1rem; padding: .6rem 1.2rem; border-radius: 9px; background: var(--teal-dim); color: #00a878; border: 1.5px solid rgba(0,212,170,.22); font-size: .82rem; font-weight: 600; text-decoration: none; transition: background .14s; }
        .empty-cta:hover { background: rgba(0,212,170,.18); }

        /* PAGINATION */
        .pagination-wrap { margin-top: 1.3rem; display: flex; justify-content: flex-end; }

        /* MOBILE */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
        }
        @media (max-width: 600px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .user-name, .user-role-badge { display: none; }
            .filter-card { gap: .5rem; }
            .filter-select { min-width: 130px; }
            .list-view .card-image { width: 90px; min-width: 90px; }
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
            <a href="{{ route('user.products.index') }}" class="ni active">
                <svg class="ni-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Products
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
                    <a href="{{ route('user.dashboard') }}">Dashboard</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Products</span>
                </div>
            </div>

            <div class="topbar-right">
                <a href="{{ route('user.cart.index') }}" class="topbar-icon-btn" aria-label="Cart">
                    <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    @php $cartCount = \App\Models\Cart::where('user_id', auth()->id())->count(); @endphp
                    @if($cartCount > 0)<span class="tb-badge">{{ $cartCount }}</span>@endif
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
                <div>
                    <p class="page-greeting">Browse</p>
                    <h1 class="page-title">Products</h1>
                    <p class="page-subtitle">Find and add items to your cart</p>
                </div>
                <div class="page-header-actions">
                    <a href="{{ route('user.cart.index') }}" class="btn-action btn-cart">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Cart
                        @if($cartCount > 0)<span class="cart-badge">{{ $cartCount }}</span>@endif
                    </a>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="filter-card">
                <span class="filter-label">Filter</span>
                <form method="GET" action="{{ route('user.products.index') }}" style="display:contents;">
                    <div class="filter-search-wrap">
                        <svg class="filter-search-icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input
                            class="filter-input" type="text" name="search"
                            value="{{ request('search') }}"
                            placeholder="Search by name or SKU…"
                        >
                    </div>

                    <select class="filter-select" name="category_id">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="btn-filter">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                        Apply
                    </button>

                    @if(request()->anyFilled(['search','category_id']))
                        <a href="{{ route('user.products.index') }}" class="btn-clear">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Results bar --}}
            <div class="results-bar">
                <p class="results-count">
                    @if($products->total() > 0)
                        Showing <strong>{{ $products->firstItem() }}–{{ $products->lastItem() }}</strong> of <strong>{{ $products->total() }}</strong> products
                        @if(request('search')) for &ldquo;<strong>{{ request('search') }}</strong>&rdquo; @endif
                    @else
                        No products found
                    @endif
                </p>
                <div class="view-toggle">
                    <button class="vt-btn active" id="gridBtn" onclick="setView('grid')" title="Grid view">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </button>
                    <button class="vt-btn" id="listBtn" onclick="setView('list')" title="List view">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </button>
                </div>
            </div>

            {{-- Product Grid / Empty --}}
            @if($products->count())

                <div class="product-grid" id="productGrid">
                    @foreach($products as $product)
                        @php
                            $statusClass = match($product->stock_status) {
                                'In Stock'  => 'stock-in',
                                'Low Stock' => 'stock-low',
                                default     => 'stock-out',
                            };
                        @endphp
                        <div class="product-card">

                            <div class="card-image">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                    <div class="card-no-image">
                                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        <span>No image</span>
                                    </div>
                                @endif
                                <div class="card-stock-overlay">
                                    <span class="stock-badge {{ $statusClass }}">{{ $product->stock_status }}</span>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="card-category">{{ $product->category?->name ?? 'Uncategorized' }}</p>
                                <p class="card-name">{{ $product->name }}</p>
                                <p class="card-price">${{ number_format($product->price, 2) }}</p>
                            </div>

                            <div class="card-footer">
                                <a href="{{ route('user.products.show', $product) }}" class="card-view-btn">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    View
                                </a>

                                @if($product->stock_status !== 'Out of Stock')
                                    <form method="POST" action="{{ route('user.cart.store') }}">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="card-add-btn">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3h2l.4 2M7 13h10l4-8H5.4"/><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><line x1="12" y1="5" x2="12" y2="11"/><line x1="9" y1="8" x2="15" y2="8"/></svg>
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <span style="font-size:.74rem;color:var(--tm);font-style:italic;">Unavailable</span>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="pagination-wrap">{{ $products->appends(request()->query())->links() }}</div>

            @else

                <div class="empty-wrap">
                    <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <p class="empty-title">No products found</p>
                    <p class="empty-sub">Try adjusting your search or filter to find what you're looking for.</p>
                    @if(request()->anyFilled(['search','category_id']))
                        <a href="{{ route('user.products.index') }}" class="empty-cta">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            Clear Filters
                        </a>
                    @endif
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

    // Grid / List toggle
    function setView(mode) {
        var grid    = document.getElementById('productGrid');
        var gridBtn = document.getElementById('gridBtn');
        var listBtn = document.getElementById('listBtn');
        if (!grid) return;
        if (mode === 'list') {
            grid.classList.add('list-view');
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
        } else {
            grid.classList.remove('list-view');
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        }
        localStorage.setItem('storix_product_view', mode);
    }

    // Restore saved view preference
    (function() {
        var saved = localStorage.getItem('storix_product_view');
        if (saved === 'list') setView('list');
    })();
</script>

</body>
</html>