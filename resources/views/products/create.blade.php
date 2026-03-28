<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Product — Storix</title>

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

        /* ════════════════════════
           MAIN
        ════════════════════════ */
        .main { margin-left: var(--sw); flex: 1; display: flex; flex-direction: column; min-height: 100vh; }

        /* TOPBAR */
        .topbar { height: var(--th); background: var(--card); border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; padding: 0 1.8rem; position: sticky; top: 0; z-index: 50; gap: 1rem; }
        .topbar-left { display: flex; align-items: center; gap: .75rem; }
        .sb-toggle { display: none; background: none; border: none; cursor: pointer; color: var(--ts); padding: 4px; border-radius: 6px; transition: background .15s; }
        .sb-toggle:hover { background: var(--light); }
        .topbar-title { font-size: 1.05rem; font-weight: 700; color: var(--tp); letter-spacing: -.01em; }

        /* Breadcrumb */
        .breadcrumb { display: flex; align-items: center; gap: 6px; font-size: .78rem; color: var(--tm); }
        .breadcrumb a { color: var(--tm); text-decoration: none; transition: color .15s; }
        .breadcrumb a:hover { color: #00a878; }
        .breadcrumb-sep { color: var(--tm); opacity: .5; }
        .breadcrumb-current { color: var(--tp); font-weight: 500; }

        .topbar-right { display: flex; align-items: center; gap: .85rem; }

        .topbar-icon-btn { width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--ts); transition: background .15s, color .15s; }
        .topbar-icon-btn:hover { background: var(--light); color: var(--tp); }

        /* User dropdown */
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
        .dd-name { font-size: .85rem; font-weight: 600; color: var(--tp); }
        .dd-email { font-size: .74rem; color: var(--tm); margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .dd-body { padding: .45rem .5rem; }
        .dd-item { display: flex; align-items: center; gap: 9px; padding: .58rem .7rem; border-radius: 7px; font-size: .82rem; color: var(--ts); text-decoration: none; cursor: pointer; transition: background .14s, color .14s; border: none; background: none; width: 100%; font-family: 'Sora',sans-serif; text-align: left; }
        .dd-item:hover { background: var(--light); color: var(--tp); }
        .dd-item.danger { color: #c0392b; }
        .dd-item.danger:hover { background: rgba(192,57,43,0.07); }
        .dd-divider { height: 1px; background: var(--border); margin: .35rem .5rem; }

        /* ════════════════════════
           CONTENT
        ════════════════════════ */
        .content { flex: 1; padding: 1.8rem 2rem 2.5rem; }

        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.6rem; flex-wrap: wrap; gap: .75rem; }
        .page-title    { font-size: 1.25rem; font-weight: 700; color: var(--tp); letter-spacing: -.02em; }
        .page-subtitle { font-size: .78rem; color: var(--tm); margin-top: 2px; }

        /* ════════════════════════
           FORM LAYOUT
        ════════════════════════ */
        .form-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 1.4rem;
            align-items: start;
        }

        /* ── FORM CARD ── */
        .form-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
        }

        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }

        .form-section {
            padding: 1.3rem 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        .form-section:last-child { border-bottom: none; }

        .form-section-title {
            font-size: .78rem; font-weight: 700; letter-spacing: .07em;
            text-transform: uppercase; color: var(--tm); margin-bottom: 1.1rem;
            display: flex; align-items: center; gap: 8px;
        }

        .form-section-title::before {
            content: ''; width: 3px; height: 14px;
            background: var(--teal); border-radius: 2px;
        }

        .form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .form-grid-1 { display: grid; grid-template-columns: 1fr; gap: 1rem; }
        .col-span-2 { grid-column: span 2; }

        /* ── FIELD ── */
        .field { display: flex; flex-direction: column; gap: .4rem; }

        .field-label {
            font-size: .72rem; font-weight: 600; letter-spacing: .07em;
            text-transform: uppercase; color: rgba(12,26,20,0.45);
        }

        .field-label .req { color: #dc2626; margin-left: 2px; }

        .field-input,
        .field-select,
        .field-textarea {
            width: 100%;
            padding: .7rem .95rem;
            background: var(--light);
            border: 1.5px solid var(--border);
            border-radius: 9px;
            color: var(--tp);
            font-family: 'Sora',sans-serif; font-size: .875rem;
            outline: none;
            transition: border-color .2s, box-shadow .2s, background .2s;
            -webkit-appearance: none; appearance: none;
        }

        .field-input::placeholder,
        .field-textarea::placeholder { color: var(--tm); }

        .field-input:focus,
        .field-select:focus,
        .field-textarea:focus {
            border-color: rgba(0,168,120,0.5);
            background: #ffffff;
            box-shadow: 0 0 0 3.5px rgba(0,168,120,0.09);
        }

        .field-input:hover:not(:focus),
        .field-select:hover:not(:focus),
        .field-textarea:hover:not(:focus) {
            border-color: rgba(12,26,20,0.15);
        }

        /* Autofill */
        .field-input:-webkit-autofill { -webkit-box-shadow: 0 0 0 100px #f2f5f3 inset !important; -webkit-text-fill-color: #0c1a14 !important; }

        /* Select arrow */
        .field-select {
            padding-right: 2.4rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2300a878' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 12px center;
            background-color: var(--light); cursor: pointer;
        }

        /* Textarea */
        .field-textarea { resize: vertical; min-height: 100px; line-height: 1.6; }

        /* Input with prefix icon */
        .field-input-wrap { position: relative; }
        .field-input-prefix {
            position: absolute; left: 0; top: 0; bottom: 0;
            width: 38px; display: flex; align-items: center; justify-content: center;
            color: var(--tm); font-size: .85rem; font-weight: 600;
            border-right: 1.5px solid var(--border);
            pointer-events: none;
            border-radius: 9px 0 0 9px;
        }

        .field-input-wrap .field-input { padding-left: 2.6rem; }
        .field-input-wrap .field-input:focus ~ .field-input-prefix,
        .field-input-wrap .field-input:focus + .field-input-prefix { border-color: rgba(0,168,120,0.5); }

        /* Error */
        .field-error { font-size: .74rem; color: #dc2626; display: flex; align-items: center; gap: 4px; margin-top: -2px; }

        /* ── RIGHT COLUMN ── */
        .right-col { display: flex; flex-direction: column; gap: 1.2rem; animation: cardIn .5s .1s cubic-bezier(.22,1,.36,1) both; }

        /* Image upload card */
        .img-card {
            background: var(--card); border: 1px solid var(--border); border-radius: 14px; overflow: hidden;
        }

        .img-card-header { padding: 1rem 1.2rem .85rem; border-bottom: 1px solid var(--border); }
        .img-card-title { font-size: .78rem; font-weight: 700; letter-spacing: .07em; text-transform: uppercase; color: var(--tm); display: flex; align-items: center; gap: 8px; }
        .img-card-title::before { content: ''; width: 3px; height: 14px; background: var(--teal); border-radius: 2px; }

        .img-card-body { padding: 1.2rem; }

        /* Preview area */
        .img-preview-wrap {
            width: 100%; aspect-ratio: 1;
            border: 2px dashed rgba(0,168,120,0.2);
            border-radius: 12px;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            background: var(--light);
            margin-bottom: 1rem;
            overflow: hidden;
            cursor: pointer;
            transition: border-color .2s, background .2s;
            position: relative;
        }

        .img-preview-wrap:hover { border-color: rgba(0,168,120,0.4); background: rgba(0,168,120,0.03); }

        .img-preview-wrap.has-img { border-style: solid; }

        #imagePreview {
            width: 100%; height: 100%;
            object-fit: cover; object-position: center;
            display: none; border-radius: 10px;
        }

        .img-preview-placeholder {
            display: flex; flex-direction: column; align-items: center; gap: .6rem;
            color: var(--tm); text-align: center; padding: 1rem;
        }

        .img-preview-placeholder .ph-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: var(--teal-dim); display: flex; align-items: center; justify-content: center;
            color: #00a878;
        }

        .img-preview-placeholder p { font-size: .78rem; color: var(--tm); line-height: 1.5; }
        .img-preview-placeholder span { font-size: .7rem; color: rgba(12,26,20,0.28); }

        .img-change-overlay {
            display: none; position: absolute; inset: 0;
            background: rgba(6,10,17,0.55); border-radius: 10px;
            align-items: center; justify-content: center;
            flex-direction: column; gap: 6px;
            color: #e2eeea; font-size: .78rem; font-weight: 500;
        }

        .img-preview-wrap.has-img:hover .img-change-overlay { display: flex; }

        /* Hidden file input */
        #imageInput { display: none; }

        .btn-upload {
            width: 100%; padding: .65rem 1rem;
            border: 1.5px solid rgba(0,168,120,0.25); border-radius: 9px;
            background: rgba(0,168,120,0.06); color: #00a878;
            font-family: 'Sora',sans-serif; font-size: .8rem; font-weight: 600;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 7px;
            transition: background .2s, border-color .2s;
        }

        .btn-upload:hover { background: rgba(0,168,120,0.12); border-color: rgba(0,168,120,0.4); }

        .img-hint { font-size: .7rem; color: var(--tm); text-align: center; margin-top: .6rem; line-height: 1.5; }

        /* Tips card */
        .tips-card {
            background: rgba(0,212,170,0.04); border: 1px solid rgba(0,212,170,0.12);
            border-radius: 12px; padding: 1rem 1.15rem;
        }

        .tips-title { font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #00a878; margin-bottom: .6rem; display: flex; align-items: center; gap: 6px; }
        .tips-title svg { color: #00a878; }

        .tips-list { display: flex; flex-direction: column; gap: 6px; }
        .tip-item { display: flex; align-items: flex-start; gap: 7px; font-size: .75rem; color: var(--ts); line-height: 1.45; }
        .tip-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--teal); flex-shrink: 0; margin-top: 5px; }

        /* ── FORM ACTIONS ── */
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

        .btn-cancel:hover { background: var(--light); color: var(--tp); border-color: rgba(12,26,20,0.15); }

        .btn-submit {
            display: inline-flex; align-items: center; gap: 7px;
            padding: .68rem 1.4rem; background: #0a1a15; border: none; border-radius: 9px;
            color: #d8f0e8; font-family: 'Sora',sans-serif; font-size: .84rem; font-weight: 600;
            cursor: pointer; letter-spacing: .03em;
            transition: background .2s, transform .15s, box-shadow .2s;
        }

        .btn-submit:hover { background: #122a20; transform: translateY(-1px); box-shadow: 0 6px 18px rgba(0,168,120,0.2); }
        .btn-submit:active { transform: translateY(0); box-shadow: none; }

        /* Mobile */
        .sb-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.45); z-index: 90; }

        @media (max-width: 1100px) {
            .form-layout { grid-template-columns: 1fr 280px; }
        }

        @media (max-width: 900px) {
            .sidebar { transform: translateX(calc(-1 * var(--sw))); }
            .sidebar.open { transform: translateX(0); }
            .sb-overlay.open { display: block; }
            .main { margin-left: 0; }
            .sb-toggle { display: flex; }
            .form-layout { grid-template-columns: 1fr; }
            .right-col { order: -1; }
            .img-preview-wrap { aspect-ratio: 16/9; }
        }

        @media (max-width: 640px) {
            .content { padding: 1.2rem 1rem 2rem; }
            .form-grid-2 { grid-template-columns: 1fr; }
            .col-span-2 { grid-column: span 1; }
            .user-name, .user-role-badge { display: none; }
        }
        @include('admin-notifications-styles')
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
                <div>
                    <div class="breadcrumb">
                        <a href="{{ route('admin.products.index') }}">Products</a>
                        <span class="breadcrumb-sep">/</span>
                        <span class="breadcrumb-current">Add Product</span>
                    </div>
                </div>
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
                    <div class="user-dropdown">
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
                    <h1 class="page-title">Add Product</h1>
                    <p class="page-subtitle">Fill in the details to add a new product to your inventory</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-layout">

                    {{-- ── LEFT: FORM FIELDS ── --}}
                    <div>

                        {{-- Basic Info --}}
                        <div class="form-card" style="margin-bottom:1.2rem">
                            <div class="form-section">
                                <p class="form-section-title">Basic Information</p>
                                <div class="form-grid-2">

                                    <div class="field">
                                        <label class="field-label" for="name">Product Name <span class="req">*</span></label>
                                        <input id="name" name="name" type="text" class="field-input" value="{{ old('name') }}" placeholder="e.g. Wireless Keyboard Pro" required>
                                        @error('name')
                                            <span class="field-error">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="field">
                                        <label class="field-label" for="sku">SKU <span class="req">*</span></label>
                                        <input id="sku" name="sku" type="text" class="field-input" value="{{ old('sku') }}" placeholder="e.g. WKP-001" required>
                                        @error('sku')
                                            <span class="field-error">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="field">
                                        <label class="field-label" for="category_id">Category <span class="req">*</span></label>
                                        <select id="category_id" name="category_id" class="field-select" required>
                                            <option value="">Select category...</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_id')
                                            <span class="field-error">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="field col-span-2">
                                        <label class="field-label" for="description">Description</label>
                                        <textarea id="description" name="description" class="field-textarea" rows="3" placeholder="Describe the product features, specifications, and details...">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="field-error">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            {{-- Pricing & Stock --}}
                            <div class="form-section">
                                <p class="form-section-title">Pricing & Stock</p>
                                <div class="form-grid-2">

                                    <div class="field">
                                        <label class="field-label" for="price">Price <span class="req">*</span></label>
                                        <div class="field-input-wrap">
                                            <input id="price" name="price" type="number" class="field-input" value="{{ old('price') }}" min="0" step="0.01" placeholder="0.00" required>
                                            <span class="field-input-prefix">$</span>
                                        </div>
                                        @error('price')
                                            <span class="field-error">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="field">
                                        <label class="field-label" for="quantity">Quantity <span class="req">*</span></label>
                                        <input id="quantity" name="quantity" type="number" class="field-input" value="{{ old('quantity') }}" min="0" placeholder="0" required>
                                        @error('quantity')
                                            <span class="field-error">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                </div>
                            </div>

                            {{-- Form actions --}}
                            <div class="form-actions">
                                <a href="{{ route('admin.products.index') }}" class="btn-cancel">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Cancel
                                </a>
                                <button type="submit" class="btn-submit">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    Create Product
                                </button>
                            </div>
                        </div>

                    </div>

                    {{-- ── RIGHT: IMAGE + TIPS ── --}}
                    <div class="right-col">

                        {{-- Image upload --}}
                        <div class="img-card">
                            <div class="img-card-header">
                                <p class="img-card-title">Product Image</p>
                            </div>
                            <div class="img-card-body">

                                <div class="img-preview-wrap" id="previewWrap" onclick="document.getElementById('imageInput').click()">
                                    <img id="imagePreview" src="" alt="Preview" />
                                    <div class="img-preview-placeholder" id="previewPlaceholder">
                                        <div class="ph-icon">
                                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        </div>
                                        <p>Click to upload<br>or drag & drop</p>
                                        <span>PNG, JPG, WEBP up to 2MB</span>
                                    </div>
                                    <div class="img-change-overlay">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                        Change image
                                    </div>
                                </div>

                                <input type="file" id="imageInput" name="image" accept="image/*">

                                <button type="button" class="btn-upload" onclick="document.getElementById('imageInput').click()">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    Choose File
                                </button>

                                <p class="img-hint">Recommended: square image, min 400×400px</p>

                                @error('image')
                                    <span class="field-error" style="margin-top:.5rem;display:flex">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- Tips --}}
                        <div class="tips-card">
                            <p class="tips-title">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Tips
                            </p>
                            <div class="tips-list">
                                <div class="tip-item"><div class="tip-dot"></div>Use a unique, descriptive product name for easy searching.</div>
                                <div class="tip-item"><div class="tip-dot"></div>SKU should be unique across all products — no duplicates.</div>
                                <div class="tip-item"><div class="tip-dot"></div>Set quantity to 0 if the product is out of stock on launch.</div>
                                <div class="tip-item"><div class="tip-dot"></div>A square product image looks best in listings and cards.</div>
                            </div>
                        </div>

                    </div>
                </div>
            </form>

        </main>
    </div>
</div>

<script>
    // Dropdown
    function toggleDropdown() { document.getElementById('userMenu').classList.toggle('open'); }
    document.addEventListener('click', function(e) {
        var m = document.getElementById('userMenu');
        if (m && !m.contains(e.target)) m.classList.remove('open');
    });

    // Sidebar
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
        document.getElementById('sbOverlay').classList.toggle('open');
    }
    function closeSidebar() {
        document.getElementById('sidebar').classList.remove('open');
        document.getElementById('sbOverlay').classList.remove('open');
    }

    // Image preview
    document.getElementById('imageInput').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (!file) return;

        var reader = new FileReader();
        reader.onload = function(ev) {
            var preview  = document.getElementById('imagePreview');
            var placeholder = document.getElementById('previewPlaceholder');
            var wrap     = document.getElementById('previewWrap');

            preview.src = ev.target.result;
            preview.style.display = 'block';
            placeholder.style.display = 'none';
            wrap.classList.add('has-img');
        };
        reader.readAsDataURL(file);
    });

    // Drag and drop
    var wrap = document.getElementById('previewWrap');

    wrap.addEventListener('dragover', function(e) {
        e.preventDefault();
        wrap.style.borderColor = 'rgba(0,168,120,0.5)';
        wrap.style.background  = 'rgba(0,168,120,0.05)';
    });

    wrap.addEventListener('dragleave', function() {
        wrap.style.borderColor = '';
        wrap.style.background  = '';
    });

    wrap.addEventListener('drop', function(e) {
        e.preventDefault();
        wrap.style.borderColor = '';
        wrap.style.background  = '';

        var file = e.dataTransfer.files[0];
        if (!file || !file.type.startsWith('image/')) return;

        // Assign to the real input
        var dt = new DataTransfer();
        dt.items.add(file);
        document.getElementById('imageInput').files = dt.files;

        var reader = new FileReader();
        reader.onload = function(ev) {
            var preview = document.getElementById('imagePreview');
            document.getElementById('previewPlaceholder').style.display = 'none';
            preview.src = ev.target.result;
            preview.style.display = 'block';
            wrap.classList.add('has-img');
        };
        reader.readAsDataURL(file);
    });
</script>

@include('admin-notifications-script')
</body>
</html>
