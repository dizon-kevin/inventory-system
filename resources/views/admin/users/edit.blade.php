<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edit User — Storix</title>

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

        /* ════════════════════
           CONTENT
        ════════════════════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; display: flex; align-items: flex-start; justify-content: center; }

        .edit-wrap { width: 100%; max-width: 620px; }

        .page-header { margin-bottom: 1.6rem; }
        .page-title    { font-size: 1.25rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        /* ── USER PROFILE CARD ── */
        .profile-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 1.4rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.2rem;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        .profile-avatar-wrap { position: relative; flex-shrink: 0; }

        .profile-avatar {
            width: 58px; height: 58px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; font-weight: 700; font-family: 'Space Mono',monospace;
        }

        .profile-avatar.admin { background: linear-gradient(135deg, rgba(124,58,237,.2), rgba(124,58,237,.08)); border: 1.5px solid rgba(124,58,237,.2); color: #7c3aed; }
        .profile-avatar.user  { background: linear-gradient(135deg, rgba(0,212,170,.18), rgba(0,119,255,.1));  border: 1.5px solid rgba(0,212,170,.18); color: #00a878; }

        .profile-role-dot {
            position: absolute; bottom: -3px; right: -3px;
            width: 16px; height: 16px; border-radius: 50%;
            border: 2px solid var(--card);
            display: flex; align-items: center; justify-content: center;
        }

        .profile-role-dot.admin { background: #7c3aed; }
        .profile-role-dot.user  { background: #00a878; }

        .profile-info { flex: 1; min-width: 0; }
        .profile-name  { font-size: 1rem; font-weight: 700; color: var(--tp); }
        .profile-email { font-size: .8rem; color: var(--tm); margin-top: 2px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

        .profile-meta { display: flex; align-items: center; gap: .5rem; margin-top: 6px; }

        .role-badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 20px; font-size: .66rem; font-weight: 700; letter-spacing: .04em; }
        .role-badge::before { content: ''; width: 5px; height: 5px; border-radius: 50%; }
        .role-admin { background: rgba(124,58,237,.1); color: #7c3aed; } .role-admin::before { background: #7c3aed; }
        .role-user  { background: rgba(77,166,255,.1); color: #2563eb; } .role-user::before  { background: #2563eb; }

        .joined-label { font-size: .7rem; color: var(--tm); }

        /* ── FORM CARD ── */
        .form-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            animation: cardIn .5s .08s cubic-bezier(.22,1,.36,1) both;
        }

        .form-card-header {
            padding: 1rem 1.4rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 8px;
        }

        .form-card-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm);
            display: flex; align-items: center; gap: 8px;
        }

        .form-card-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }

        .form-body { padding: 1.4rem 1.5rem; display: flex; flex-direction: column; gap: 1.1rem; }

        /* Fields */
        .field { display: flex; flex-direction: column; gap: .4rem; }

        .field-label { font-size: .72rem; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: rgba(12,26,20,0.42); }
        .field-label .req { color: #dc2626; margin-left: 2px; }

        .field-input,
        .field-select {
            width: 100%;
            padding: .75rem 1rem;
            background: var(--light);
            border: 1.5px solid var(--border);
            border-radius: 9px;
            color: var(--tp);
            font-family: 'Sora',sans-serif; font-size: .875rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            -webkit-appearance: none; appearance: none;
        }

        .field-input:focus,
        .field-select:focus {
            border-color: rgba(0,168,120,.5);
            background: #ffffff;
            box-shadow: 0 0 0 3.5px rgba(0,168,120,.09);
        }

        .field-input:hover:not(:focus),
        .field-select:hover:not(:focus) { border-color: rgba(12,26,20,.14); }

        .field-input:-webkit-autofill { -webkit-box-shadow: 0 0 0 100px #f2f5f3 inset !important; -webkit-text-fill-color: #0c1a14 !important; }

        /* Select custom arrow */
        .field-select {
            padding-right: 2.4rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2300a878' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center;
            background-color: var(--light); cursor: pointer;
        }

        /* Role cards (visual selector) */
        .role-options { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }

        .role-option input[type="radio"] { display: none; }

        .role-option-label {
            display: flex; align-items: center; gap: 10px;
            padding: .85rem 1rem;
            background: var(--light);
            border: 1.5px solid var(--border);
            border-radius: 10px;
            cursor: pointer;
            transition: border-color .2s, background .2s, box-shadow .2s;
            user-select: none;
        }

        .role-option-label:hover { border-color: rgba(12,26,20,.18); }

        .role-option input:checked + .role-option-label {
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(0,168,120,.08);
        }

        .role-option input[value="admin"]:checked + .role-option-label {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124,58,237,.09);
        }

        .role-option input[value="user"]:checked + .role-option-label {
            border-color: #00a878;
        }

        .role-option-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }

        .role-option-icon.admin { background: rgba(124,58,237,.1); color: #7c3aed; }
        .role-option-icon.user  { background: rgba(0,168,120,.1);   color: #00a878; }

        .role-option-text { flex: 1; }
        .role-option-name { font-size: .84rem; font-weight: 600; color: var(--tp); }
        .role-option-desc { font-size: .7rem; color: var(--tm); margin-top: 1px; }

        .role-option-check {
            width: 18px; height: 18px; border-radius: 50%;
            border: 1.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: all .18s;
        }

        .role-option input[value="admin"]:checked ~ .role-option-label .role-option-check { background: #7c3aed; border-color: #7c3aed; }
        .role-option input[value="user"]:checked  ~ .role-option-label .role-option-check { background: #00a878; border-color: #00a878; }

        /* Field error */
        .field-error { font-size: .74rem; color: #dc2626; display: flex; align-items: center; gap: 4px; margin-top: -2px; }

        /* Password section hint */
        .field-hint { font-size: .74rem; color: var(--tm); margin-top: -4px; line-height: 1.4; }

        /* Divider */
        .form-divider { height: 1px; background: var(--border); margin: .3rem 0; }

        /* Form actions */
        .form-actions {
            display: flex; align-items: center; justify-content: flex-end; gap: .75rem;
            padding: 1.1rem 1.5rem;
            border-top: 1px solid var(--border);
            background: #fafbfa;
        }

        .btn-cancel {
            display: inline-flex; align-items: center; gap: 6px;
            padding: .68rem 1.2rem; border: 1.5px solid var(--border); border-radius: 9px;
            background: none; color: var(--ts); font-family: 'Sora',sans-serif; font-size: .84rem; font-weight: 500;
            cursor: pointer; text-decoration: none;
            transition: background .15s, color .15s, border-color .15s;
        }

        .btn-cancel:hover { background: var(--light); color: var(--tp); border-color: rgba(12,26,20,.15); }

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
        }

        @media (max-width: 600px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .role-options { grid-template-columns: 1fr; }
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
            <a href="{{ route('admin.users.index') }}" class="ni active">
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
                    <a href="{{ route('admin.users.index') }}">Users</a>
                    <span class="breadcrumb-sep">/</span>
                    <span class="breadcrumb-current">Edit — {{ $user->name }}</span>
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
            <div class="edit-wrap">

                <div class="page-header">
                    <h1 class="page-title">Edit User</h1>
                    <p class="page-subtitle">Update account details, email address, and role</p>
                </div>

                {{-- Profile snapshot --}}
                <div class="profile-card">
                    <div class="profile-avatar-wrap">
                        <div class="profile-avatar {{ $user->role === 'admin' ? 'admin' : 'user' }}">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                        <div class="profile-role-dot {{ $user->role === 'admin' ? 'admin' : 'user' }}"></div>
                    </div>
                    <div class="profile-info">
                        <p class="profile-name">{{ $user->name }}</p>
                        <p class="profile-email">{{ $user->email }}</p>
                        <div class="profile-meta">
                            @if($user->role === 'admin')
                                <span class="role-badge role-admin">Admin</span>
                            @else
                                <span class="role-badge role-user">User</span>
                            @endif
                            <span class="joined-label">Joined {{ $user->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Edit form --}}
                <div class="form-card">
                    <div class="form-card-header">
                        <p class="form-card-title">Account Details</p>
                    </div>

                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-body">

                            {{-- Name --}}
                            <div class="field">
                                <label class="field-label" for="name">Full Name <span class="req">*</span></label>
                                <input
                                    id="name" name="name" type="text"
                                    class="field-input"
                                    value="{{ old('name', $user->name) }}"
                                    placeholder="Full name"
                                    required
                                />
                                @error('name')
                                    <span class="field-error">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="field">
                                <label class="field-label" for="email">Email Address <span class="req">*</span></label>
                                <input
                                    id="email" name="email" type="email"
                                    class="field-input"
                                    value="{{ old('email', $user->email) }}"
                                    placeholder="email@example.com"
                                    required
                                />
                                @error('email')
                                    <span class="field-error">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                            <div class="form-divider"></div>

                            {{-- Role visual selector --}}
                            <div class="field">
                                <label class="field-label">Role <span class="req">*</span></label>
                                <div class="role-options">

                                    {{-- User --}}
                                    <label class="role-option">
                                        <input
                                            type="radio" name="role" value="user"
                                            {{ old('role', $user->role) === 'user' ? 'checked' : '' }}
                                            required
                                        />
                                        <div class="role-option-label">
                                            <div class="role-option-icon user">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                            </div>
                                            <div class="role-option-text">
                                                <div class="role-option-name">User</div>
                                                <div class="role-option-desc">Browse & place orders</div>
                                            </div>
                                            <div class="role-option-check">
                                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            </div>
                                        </div>
                                    </label>

                                    {{-- Admin --}}
                                    <label class="role-option">
                                        <input
                                            type="radio" name="role" value="admin"
                                            {{ old('role', $user->role) === 'admin' ? 'checked' : '' }}
                                            required
                                        />
                                        <div class="role-option-label">
                                            <div class="role-option-icon admin">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                            </div>
                                            <div class="role-option-text">
                                                <div class="role-option-name">Admin</div>
                                                <div class="role-option-desc">Full system access</div>
                                            </div>
                                            <div class="role-option-check">
                                                <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            </div>
                                        </div>
                                    </label>

                                </div>
                                @error('role')
                                    <span class="field-error">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                        </div>

                        {{-- Actions --}}
                        <div class="form-actions">
                            <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Cancel
                            </a>
                            <button type="submit" class="btn-save">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                Save Changes
                            </button>
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

    // Live preview: update avatar initials + role color when name changes
    var nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            var val = this.value.trim();
            var initials = val ? val.split(' ').map(function(w){return w[0]||'';}).slice(0,2).join('').toUpperCase() : '??';
            var av = document.querySelector('.profile-avatar');
            if (av) av.textContent = initials;
        });
    }

    // Live preview: update role badge + avatar class on role change
    document.querySelectorAll('input[name="role"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var av   = document.querySelector('.profile-avatar');
            var dot  = document.querySelector('.profile-role-dot');
            var badge = document.querySelector('.profile-meta .role-badge');

            if (this.value === 'admin') {
                av.className   = 'profile-avatar admin';
                dot.className  = 'profile-role-dot admin';
                if (badge) { badge.className = 'role-badge role-admin'; badge.textContent = 'Admin'; }
            } else {
                av.className   = 'profile-avatar user';
                dot.className  = 'profile-role-dot user';
                if (badge) { badge.className = 'role-badge role-user'; badge.textContent = 'User'; }
            }
        });
    });
</script>

</body>
</html>