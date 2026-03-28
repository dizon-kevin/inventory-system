<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management — Storix</title>

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

        /* Flash */
        .flash { display: flex; align-items: center; gap: 10px; border-radius: 10px; padding: .75rem 1rem; font-size: .82rem; margin-bottom: 1.2rem; animation: fadeIn .4s ease; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(-6px)} to{opacity:1;transform:translateY(0)} }
        .flash-success { background: rgba(34,197,94,0.08);  border: 1px solid rgba(34,197,94,0.2);  color: #15803d; }
        .flash-error   { background: rgba(239,68,68,0.08);  border: 1px solid rgba(239,68,68,0.2);  color: #dc2626; }

        /* ── SUMMARY STRIP ── */
        .summary-strip { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 1.5rem; }

        .scard { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 1.1rem 1.2rem; display: flex; align-items: center; gap: .9rem; transition: box-shadow .18s, transform .18s; animation: cardIn .5s cubic-bezier(.22,1,.36,1) both; }
        .scard:nth-child(1){animation-delay:.05s} .scard:nth-child(2){animation-delay:.10s} .scard:nth-child(3){animation-delay:.15s}
        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .scard:hover { box-shadow: 0 5px 18px rgba(0,0,0,.06); transform: translateY(-2px); }

        .scard-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .scard-icon.all   { background: rgba(0,212,170,0.1);  color: #00a878; }
        .scard-icon.admin { background: rgba(124,58,237,0.1);  color: #7c3aed; }
        .scard-icon.user  { background: rgba(77,166,255,0.1);  color: #2563eb; }

        .scard-val { font-family: 'Space Mono',monospace; font-size: 1.55rem; font-weight: 700; color: var(--tp); line-height: 1; }
        .scard-lbl { font-size: .72rem; color: var(--tm); margin-top: 3px; font-weight: 500; }

        /* ── SEARCH BAR ── */
        .filter-bar { background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: .85rem 1.1rem; display: flex; align-items: center; gap: .75rem; margin-bottom: 1.2rem; flex-wrap: wrap; }

        .filter-search-wrap { position: relative; flex: 1; min-width: 200px; }
        .filter-search-icon { position: absolute; left: 11px; top: 50%; transform: translateY(-50%); color: rgba(0,168,120,.42); display: flex; pointer-events: none; }
        .filter-input { width: 100%; padding: .6rem .9rem .6rem 2.4rem; background: var(--light); border: 1.5px solid var(--border); border-radius: 8px; color: var(--tp); font-family: 'Sora',sans-serif; font-size: .83rem; outline: none; transition: border-color .2s, box-shadow .2s; }
        .filter-input::placeholder { color: var(--tm); }
        .filter-input:focus { border-color: rgba(0,168,120,.45); box-shadow: 0 0 0 3px rgba(0,168,120,.08); }

        .filter-select { padding: .6rem 2.2rem .6rem .9rem; background: var(--light); border: 1.5px solid var(--border); border-radius: 8px; color: var(--tp); font-family: 'Sora',sans-serif; font-size: .83rem; outline: none; appearance: none; -webkit-appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' viewBox='0 0 24 24' fill='none' stroke='%2300a878' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 10px center; cursor: pointer; transition: border-color .2s; min-width: 140px; }
        .filter-select:focus { border-color: rgba(0,168,120,.45); box-shadow: 0 0 0 3px rgba(0,168,120,.08); }

        /* ── TABLE CARD ── */
        .table-card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden; animation: cardIn .5s .22s cubic-bezier(.22,1,.36,1) both; }

        .table-header { display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.4rem; border-bottom: 1px solid var(--border); }
        .table-title  { font-size: .9rem; font-weight: 700; color: var(--tp); display: flex; align-items: center; gap: 8px; }
        .title-dot    { width: 7px; height: 7px; border-radius: 50%; background: var(--teal); box-shadow: 0 0 8px rgba(0,212,170,0.4); }
        .table-count  { font-size: .72rem; color: var(--tm); font-weight: 400; }

        /* Table */
        .storix-table { width: 100%; border-collapse: collapse; font-size: .82rem; }
        .storix-table thead th { padding: .65rem 1.2rem; text-align: left; font-size: .63rem; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--tm); background: #fafbfa; border-bottom: 1px solid var(--border); white-space: nowrap; }
        .storix-table tbody td { padding: .9rem 1.2rem; border-bottom: 1px solid rgba(12,26,20,0.05); color: var(--ts); vertical-align: middle; }
        .storix-table tbody tr:last-child td { border-bottom: none; }
        .storix-table tbody tr { transition: background .12s; }
        .storix-table tbody tr:hover { background: #fafcfa; }

        /* User cell */
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .u-avatar { width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center; font-size: .68rem; font-weight: 700; font-family: 'Space Mono',monospace; flex-shrink: 0; }
        .u-avatar.admin { background: linear-gradient(135deg,rgba(124,58,237,.18),rgba(124,58,237,.08)); border: 1px solid rgba(124,58,237,.15); color: #7c3aed; }
        .u-avatar.user  { background: linear-gradient(135deg,rgba(0,212,170,.15),rgba(0,119,255,.1));  border: 1px solid rgba(0,212,170,.15); color: #00a878; }
        .u-name  { font-size: .84rem; font-weight: 600; color: var(--tp); }
        .u-you   { font-size: .65rem; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; background: rgba(0,212,170,.1); color: #00a878; border-radius: 4px; padding: 1px 5px; margin-left: 5px; }

        .td-email { font-size: .8rem; color: var(--ts); }
        .td-mono  { font-family: 'Space Mono',monospace; font-size: .76rem; }
        .td-date  { font-size: .78rem; color: var(--ts); }

        /* Role badge */
        .role-badge { display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; border-radius: 20px; font-size: .68rem; font-weight: 700; letter-spacing: .04em; }
        .role-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .role-admin { background: rgba(124,58,237,.1);  color: #7c3aed; } .role-admin::before { background: #7c3aed; }
        .role-user  { background: rgba(77,166,255,.1);  color: #2563eb; } .role-user::before  { background: #2563eb; }

        /* Actions */
        .actions { display: flex; align-items: center; gap: .45rem; }
        .act-btn { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 6px; font-size: .73rem; font-weight: 500; text-decoration: none; cursor: pointer; border: 1px solid transparent; font-family: 'Sora',sans-serif; transition: background .15s; background: none; }
        .act-edit { color: #00a878; border-color: rgba(0,168,120,.18); } .act-edit:hover { background: rgba(0,168,120,.07); }
        .act-del  { color: #dc2626; border-color: rgba(220,38,38,.18); } .act-del:hover  { background: rgba(220,38,38,.07); }

        /* Self badge — can't delete yourself */
        .act-self { font-size: .68rem; color: var(--tm); font-style: italic; padding: 4px 6px; }

        /* Pagination */
        .pagination-wrap { padding: .85rem 1.2rem; border-top: 1px solid var(--border); }

        /* Empty state */
        .empty-wrap { padding: 3.5rem 2rem; text-align: center; }
        .empty-icon { width: 52px; height: 52px; border-radius: 14px; background: var(--teal-dim); display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; color: #00a878; }
        .empty-title { font-size: .92rem; font-weight: 700; color: var(--tp); margin-bottom: .35rem; }
        .empty-sub   { font-size: .8rem; color: var(--tm); }

        /* Delete modal */
        .modal-backdrop { display: none; position: fixed; inset: 0; z-index: 500; background: rgba(0,0,0,.45); align-items: center; justify-content: center; }
        .modal-backdrop.open { display: flex; }
        .modal-box { background: var(--card); border-radius: 16px; padding: 1.75rem 2rem; max-width: 380px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,.2); animation: cardIn .25s cubic-bezier(.22,1,.36,1); }
        .modal-icon { width: 44px; height: 44px; border-radius: 12px; background: rgba(220,38,38,.08); display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; color: #dc2626; }
        .modal-title { font-size: .98rem; font-weight: 700; color: var(--tp); margin-bottom: .4rem; }
        .modal-body  { font-size: .82rem; color: var(--ts); margin-bottom: 1.4rem; line-height: 1.5; }
        .modal-actions { display: flex; gap: .65rem; }
        .modal-cancel { flex: 1; padding: .68rem; border: 1.5px solid var(--border); border-radius: 9px; background: none; font-family: 'Sora',sans-serif; font-size: .82rem; font-weight: 500; color: var(--ts); cursor: pointer; transition: background .15s; }
        .modal-cancel:hover { background: var(--light); color: var(--tp); }
        .modal-confirm { flex: 1; padding: .68rem; border: none; border-radius: 9px; background: #dc2626; font-family: 'Sora',sans-serif; font-size: .82rem; font-weight: 600; color: #fff; cursor: pointer; transition: background .15s; }
        .modal-confirm:hover { background: #b91c1c; }

        /* Mobile */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
            .summary-strip { grid-template-columns: repeat(3,1fr); }
        }

        @media (max-width: 600px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .summary-strip { grid-template-columns: 1fr; }
            .user-name, .user-role-badge { display: none; }
        }
        @include('admin-notifications-styles')
    </style>
</head>
<body>

{{-- Delete modal --}}
<div class="modal-backdrop" id="deleteModal">
    <div class="modal-box">
        <div class="modal-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
        </div>
        <p class="modal-title">Delete user?</p>
        <p class="modal-body" id="deleteModalMsg">This will permanently remove the user account and all associated data. This action cannot be undone.</p>
        <div class="modal-actions">
            <button class="modal-cancel" onclick="closeDeleteModal()">Cancel</button>
            <button class="modal-confirm" id="deleteConfirmBtn">Delete user</button>
        </div>
    </div>
</div>

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
            <a href="#" class="ni active">
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
                <p class="topbar-title">User Management</p>
            </div>
            <div class="topbar-right">
                @include('admin-notifications-dropdown')
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

            <div class="page-header">
                <div>
                    <h1 class="page-title">Users</h1>
                    <p class="page-subtitle">Manage accounts, roles, and access control</p>
                </div>
            </div>

            {{-- Flash --}}
            @if(session('success'))
                <div class="flash flash-success">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash flash-error">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            @if($users->count())

                {{-- Summary strip --}}
                @php
                    $totalUsers  = $users->total();
                    $adminCount  = $users->getCollection()->where('role','admin')->count();
                    $userCount   = $users->getCollection()->where('role','user')->count();
                @endphp

                <div class="summary-strip">
                    <div class="scard">
                        <div class="scard-icon all">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <div>
                            <div class="scard-val">{{ $totalUsers }}</div>
                            <div class="scard-lbl">Total Users</div>
                        </div>
                    </div>
                    <div class="scard">
                        <div class="scard-icon admin">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div>
                            <div class="scard-val">{{ $adminCount }}</div>
                            <div class="scard-lbl">Admins</div>
                        </div>
                    </div>
                    <div class="scard">
                        <div class="scard-icon user">
                            <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div>
                            <div class="scard-val">{{ $userCount }}</div>
                            <div class="scard-lbl">Regular Users</div>
                        </div>
                    </div>
                </div>

                {{-- Search/filter bar --}}
                <div class="filter-bar">
                    <div class="filter-search-wrap">
                        <span class="filter-search-icon">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        </span>
                        <input type="text" class="filter-input" placeholder="Search by name or email..." />
                    </div>
                    <select class="filter-select">
                        <option value="">All roles</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>

                {{-- Table --}}
                <div class="table-card">
                    <div class="table-header">
                        <h3 class="table-title">
                            <span class="title-dot"></span>
                            All Users
                            <span class="table-count">{{ $users->total() }} total</span>
                        </h3>
                    </div>

                    <div style="overflow-x:auto">
                        <table class="storix-table">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        {{-- User cell with avatar --}}
                                        <td>
                                            <div class="user-cell">
                                                <div class="u-avatar {{ $user->role === 'admin' ? 'admin' : 'user' }}">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <span class="u-name">{{ $user->name }}</span>
                                                    @if(auth()->id() === $user->id)
                                                        <span class="u-you">You</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>

                                        <td class="td-email">{{ $user->email }}</td>

                                        {{-- Role badge --}}
                                        <td>
                                            @if($user->role === 'admin')
                                                <span class="role-badge role-admin">Admin</span>
                                            @else
                                                <span class="role-badge role-user">User</span>
                                            @endif
                                        </td>

                                        <td class="td-date">{{ $user->created_at->format('M d, Y') }}</td>

                                        {{-- Actions --}}
                                        <td>
                                            <div class="actions">
                                                <a href="{{ route('admin.users.edit', $user) }}" class="act-btn act-edit">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                    Edit
                                                </a>
                                                @if(auth()->id() !== $user->id)
                                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" id="deleteForm-{{ $user->id }}" style="display:inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button
                                                            type="button"
                                                            class="act-btn act-del"
                                                            onclick="confirmDelete({{ $user->id }}, '{{ addslashes($user->name) }}')"
                                                        >
                                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
                                                            Delete
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="act-self">current session</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($users->hasPages())
                        <div class="pagination-wrap">
                            {{ $users->links() }}
                        </div>
                    @endif
                </div>

            @else
                <div class="table-card">
                    <div class="empty-wrap">
                        <div class="empty-icon">
                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        </div>
                        <p class="empty-title">No users found</p>
                        <p class="empty-sub">Registered users will appear here.</p>
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

    var pendingDeleteId = null;

    function confirmDelete(userId, userName) {
        pendingDeleteId = userId;
        document.getElementById('deleteModalMsg').textContent =
            'Are you sure you want to permanently delete "' + userName + '"? This will remove their account and all associated data.';
        document.getElementById('deleteModal').classList.add('open');
    }

    document.getElementById('deleteConfirmBtn').addEventListener('click', function() {
        if (pendingDeleteId) {
            document.getElementById('deleteForm-' + pendingDeleteId).submit();
        }
    });

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.remove('open');
        pendingDeleteId = null;
    }

    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
</script>

@include('admin-notifications-script')
</body>
</html>
