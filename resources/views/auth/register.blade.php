<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Create Account — Storix</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            width: 100%;
            font-family: 'Sora', sans-serif;
            background: #060a11;
        }

        /* ════════════════════════════════
           ROOT SPLIT LAYOUT
        ════════════════════════════════ */
        .storix-root {
            display: grid;
            grid-template-columns: 40% 60%;
            min-height: 100vh;
        }

        /* ════════════════════════════════
           LEFT PANEL
        ════════════════════════════════ */
        .storix-left {
            position: relative;
            background: #060a11;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 2.8rem 2.5rem 2.5rem;
            overflow: hidden;
        }

        .storix-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(0, 212, 170, 0.17) 1px, transparent 1px);
            background-size: 32px 32px;
            animation: dotDrift 28s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes dotDrift {
            0%   { transform: translate(0, 0); }
            100% { transform: translate(32px, 32px); }
        }

        .storix-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }

        .storix-glow-1 {
            width: 440px; height: 440px;
            background: radial-gradient(circle, rgba(0, 212, 170, 0.2), transparent 68%);
            top: -120px; left: -90px;
            animation: glowPulse 10s ease-in-out infinite alternate;
        }

        .storix-glow-2 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(0, 90, 255, 0.18), transparent 68%);
            bottom: -70px; right: -60px;
            animation: glowPulse 13s ease-in-out infinite alternate-reverse;
        }

        @keyframes glowPulse {
            0%   { transform: scale(1);    opacity: 0.7; }
            100% { transform: scale(1.18); opacity: 1; }
        }

        /* Brand */
        .storix-brand {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 11px;
        }

        .storix-logomark {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, #00d4aa, #0077ff);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 24px rgba(0, 212, 170, 0.45);
            flex-shrink: 0;
        }

        .storix-logomark svg {
            width: 22px; height: 22px;
            fill: #060a11;
        }

        .storix-wordmark {
            font-family: 'Space Mono', monospace;
            font-size: 1.4rem;
            font-weight: 700;
            color: #e2eeea;
            letter-spacing: 0.1em;
        }

        .storix-wordmark em {
            font-style: normal;
            color: #00d4aa;
        }

        /* Left center content */
        .storix-left-body {
            position: relative;
            z-index: 2;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2.5rem 0 1.5rem;
            gap: 2rem;
        }

        /* Stats grid */
        .storix-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .storix-stat-card {
            background: rgba(0, 212, 170, 0.05);
            border: 1px solid rgba(0, 212, 170, 0.1);
            border-radius: 12px;
            padding: 1rem 1.1rem;
            animation: statIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .storix-stat-card:nth-child(1) { animation-delay: 0.1s; }
        .storix-stat-card:nth-child(2) { animation-delay: 0.2s; }
        .storix-stat-card:nth-child(3) { animation-delay: 0.3s; }
        .storix-stat-card:nth-child(4) { animation-delay: 0.4s; }

        @keyframes statIn {
            from { opacity: 0; transform: translateY(14px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .storix-stat-value {
            font-family: 'Space Mono', monospace;
            font-size: 1.55rem;
            font-weight: 700;
            color: #00d4aa;
            line-height: 1;
            margin-bottom: 0.35rem;
        }

        .storix-stat-value.blue { color: #4da6ff; }
        .storix-stat-value.purple { color: #a78bfa; }
        .storix-stat-value.amber { color: #fbbf24; }

        .storix-stat-label {
            font-size: 0.7rem;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            color: rgba(180, 210, 200, 0.4);
            line-height: 1.4;
        }

        /* Feature list */
        .storix-features {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .storix-feature {
            display: flex;
            align-items: center;
            gap: 11px;
            animation: statIn 0.7s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        .storix-feature:nth-child(1) { animation-delay: 0.45s; }
        .storix-feature:nth-child(2) { animation-delay: 0.55s; }
        .storix-feature:nth-child(3) { animation-delay: 0.65s; }

        .storix-feature-dot {
            width: 6px; height: 6px;
            background: #00d4aa;
            border-radius: 50%;
            flex-shrink: 0;
            box-shadow: 0 0 8px rgba(0, 212, 170, 0.6);
        }

        .storix-feature-text {
            font-size: 0.8rem;
            color: rgba(200, 230, 220, 0.5);
            line-height: 1.4;
        }

        /* Quote */
        .storix-quote {
            position: relative;
            z-index: 2;
        }

        .storix-quote-text {
            font-family: 'DM Serif Display', serif;
            font-style: italic;
            font-size: 1.0rem;
            color: rgba(210, 235, 228, 0.5);
            line-height: 1.65;
            margin-bottom: 0.6rem;
        }

        .storix-quote-attr {
            font-size: 0.67rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(0, 212, 170, 0.3);
        }

        /* ════════════════════════════════
           RIGHT PANEL
        ════════════════════════════════ */
        .storix-right {
            background: #f4f7f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow-y: auto;
        }

        .storix-right::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.025'/%3E%3C/svg%3E");
            pointer-events: none;
            opacity: 0.5;
        }

        .storix-form-wrap {
            position: relative;
            width: 100%;
            max-width: 460px;
            animation: panelSlide 0.7s 0.15s cubic-bezier(0.22, 1, 0.36, 1) both;
        }

        @keyframes panelSlide {
            from { opacity: 0; transform: translateX(22px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Eyebrow */
        .storix-eyebrow {
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #00a878;
            margin-bottom: 0.75rem;
        }

        .storix-heading {
            font-family: 'DM Serif Display', serif;
            font-size: 2.4rem;
            color: #0a1a15;
            line-height: 1.12;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .storix-subheading {
            font-size: 0.83rem;
            color: rgba(15, 35, 28, 0.45);
            margin-bottom: 2rem;
            line-height: 1.55;
        }

        /* Two-column row for name + email */
        .storix-field-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 1.15rem;
        }

        .storix-field {
            margin-bottom: 1.15rem;
        }

        .storix-field-row .storix-field {
            margin-bottom: 0;
        }

        .storix-label {
            display: block;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(10, 30, 22, 0.45);
            margin-bottom: 0.45rem;
        }

        .storix-input-wrap {
            position: relative;
        }

        .storix-input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(0, 168, 120, 0.42);
            display: flex;
            pointer-events: none;
        }

        .storix-input,
        .storix-select {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.6rem;
            background: #ffffff;
            border: 1.5px solid #d4e3dc;
            border-radius: 10px;
            color: #0a1a15;
            font-family: 'Sora', sans-serif;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.22s, box-shadow 0.22s;
            -webkit-appearance: none;
            appearance: none;
        }

        .storix-input::placeholder {
            color: rgba(10, 30, 22, 0.25);
        }

        .storix-input:focus,
        .storix-select:focus {
            border-color: #00a878;
            box-shadow: 0 0 0 3.5px rgba(0, 168, 120, 0.1);
        }

        .storix-input:-webkit-autofill,
        .storix-input:-webkit-autofill:hover,
        .storix-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 100px #ffffff inset !important;
            -webkit-text-fill-color: #0a1a15 !important;
        }

        /* Select custom arrow */
        .storix-select {
            padding-right: 2.5rem;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2300a878' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            cursor: pointer;
        }

        /* Password fields row */
        .storix-pw-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 1.15rem;
        }

        .storix-pw-row .storix-field {
            margin-bottom: 0;
        }

        .storix-eye-btn {
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: rgba(10, 30, 22, 0.3);
            display: flex;
            align-items: center;
            padding: 0;
            transition: color 0.2s;
        }

        .storix-eye-btn:hover { color: #00a878; }

        .storix-input-error {
            font-size: 0.73rem;
            color: #c0392b;
            margin-top: 0.38rem;
        }

        /* Role badge selector */
        .storix-role-wrap {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 0.45rem;
        }

        .storix-role-option {
            display: none;
        }

        .storix-role-label {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 0.7rem 1rem;
            background: #fff;
            border: 1.5px solid #d4e3dc;
            border-radius: 10px;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            font-size: 0.83rem;
            color: rgba(10, 30, 22, 0.55);
            font-weight: 500;
            user-select: none;
        }

        .storix-role-label .role-icon {
            width: 28px; height: 28px;
            border-radius: 7px;
            background: rgba(0, 168, 120, 0.08);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            transition: background 0.2s;
        }

        .storix-role-label .role-icon svg {
            color: rgba(0, 168, 120, 0.5);
            transition: color 0.2s;
        }

        .storix-role-option:checked + .storix-role-label {
            border-color: #00a878;
            background: rgba(0, 168, 120, 0.05);
            box-shadow: 0 0 0 3.5px rgba(0, 168, 120, 0.1);
            color: #007558;
        }

        .storix-role-option:checked + .storix-role-label .role-icon {
            background: rgba(0, 168, 120, 0.15);
        }

        .storix-role-option:checked + .storix-role-label .role-icon svg {
            color: #00a878;
        }

        /* Submit */
        .storix-btn {
            width: 100%;
            padding: 0.88rem 1rem;
            background: #0a1a15;
            border: none;
            border-radius: 10px;
            color: #d8f0e8;
            font-family: 'Sora', sans-serif;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            transition: background 0.22s, transform 0.15s, box-shadow 0.22s;
            margin-bottom: 1.4rem;
            margin-top: 1.6rem;
        }

        .storix-btn:hover {
            background: #122a20;
            transform: translateY(-1px);
            box-shadow: 0 8px 24px rgba(0, 168, 120, 0.18);
        }

        .storix-btn:active {
            transform: translateY(0);
            box-shadow: none;
        }

        /* Divider */
        .storix-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.3rem;
            color: rgba(10, 30, 22, 0.28);
            font-size: 0.72rem;
            letter-spacing: 0.08em;
        }

        .storix-divider::before,
        .storix-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #cdddd6;
        }

        /* Login link */
        .storix-login-link {
            text-align: center;
            font-size: 0.82rem;
            color: rgba(10, 30, 22, 0.45);
        }

        .storix-login-link a {
            color: #00a878;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .storix-login-link a:hover { color: #007558; }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .storix-root {
                grid-template-columns: 1fr;
                min-height: auto;
            }
            .storix-left {
                min-height: 280px;
                padding: 2rem;
            }
            .storix-left-body { padding: 1.5rem 0 1rem; gap: 1.4rem; }
            .storix-stats { grid-template-columns: repeat(4, 1fr); }
            .storix-right { padding: 2.5rem 1.5rem; }
        }

        @media (max-width: 600px) {
            .storix-field-row,
            .storix-pw-row { grid-template-columns: 1fr; }
            .storix-stats  { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

<div class="storix-root">

    {{-- ════ LEFT PANEL ════ --}}
    <div class="storix-left">
        <div class="storix-glow storix-glow-1"></div>
        <div class="storix-glow storix-glow-2"></div>

        {{-- Brand --}}
        <div class="storix-brand">
            <div class="storix-logomark">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 3h8v8H3zM13 3h8v8h-8zM3 13h8v8H3zM17 13h4v4h-4zM13 17h4v4h-4z"/>
                </svg>
            </div>
            <span class="storix-wordmark">STO<em>RIX</em></span>
        </div>

        {{-- Stats + Features --}}
        <div class="storix-left-body">
            <div class="storix-stats">
                <div class="storix-stat-card">
                    <div class="storix-stat-value">12k+</div>
                    <div class="storix-stat-label">Active users</div>
                </div>
                <div class="storix-stat-card">
                    <div class="storix-stat-value blue">98%</div>
                    <div class="storix-stat-label">Uptime SLA</div>
                </div>
                <div class="storix-stat-card">
                    <div class="storix-stat-value purple">500+</div>
                    <div class="storix-stat-label">Integrations</div>
                </div>
                <div class="storix-stat-card">
                    <div class="storix-stat-value amber">4.9★</div>
                    <div class="storix-stat-label">User rating</div>
                </div>
            </div>

            <div class="storix-features">
                <div class="storix-feature">
                    <div class="storix-feature-dot"></div>
                    <span class="storix-feature-text">Real-time inventory tracking across all locations</span>
                </div>
                <div class="storix-feature">
                    <div class="storix-feature-dot"></div>
                    <span class="storix-feature-text">Smart analytics with automated reporting</span>
                </div>
                <div class="storix-feature">
                    <div class="storix-feature-dot"></div>
                    <span class="storix-feature-text">Role-based access control for your whole team</span>
                </div>
            </div>
        </div>

        {{-- Quote --}}
        <div class="storix-quote">
            <p class="storix-quote-text">"Data is the new oil —<br>refine it well."</p>
            <p class="storix-quote-attr">— Storix Intelligence Platform</p>
        </div>
    </div>

    {{-- ════ RIGHT PANEL ════ --}}
    <div class="storix-right">
        <div class="storix-form-wrap">

            <p class="storix-eyebrow">Get started</p>
            <h1 class="storix-heading">Create your<br>account</h1>
            <p class="storix-subheading">Join Storix and take control of your inventory.</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- Name + Email row --}}
                <div class="storix-field-row">
                    {{-- Name --}}
                    <div class="storix-field">
                        <label class="storix-label" for="name">Full Name</label>
                        <div class="storix-input-wrap">
                            <span class="storix-input-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            <input
                                id="name"
                                class="storix-input"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                placeholder="Juan Dela Cruz"
                                required
                                autofocus
                                autocomplete="name"
                            />
                        </div>
                        @error('name')
                            <p class="storix-input-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="storix-field">
                        <label class="storix-label" for="email">Email Address</label>
                        <div class="storix-input-wrap">
                            <span class="storix-input-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                                </svg>
                            </span>
                            <input
                                id="email"
                                class="storix-input"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                required
                                autocomplete="username"
                            />
                        </div>
                        @error('email')
                            <p class="storix-input-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Role --}}
                <div class="storix-field">
                    <label class="storix-label">Register as</label>
                    <div class="storix-role-wrap">
                        {{-- User --}}
                        <input
                            type="radio"
                            id="role_user"
                            name="role"
                            value="user"
                            class="storix-role-option"
                            {{ old('role', 'user') === 'user' ? 'checked' : '' }}
                        />
                        <label for="role_user" class="storix-role-label">
                            <span class="role-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </span>
                            User
                        </label>

                        {{-- Admin --}}
                        <input
                            type="radio"
                            id="role_admin"
                            name="role"
                            value="admin"
                            class="storix-role-option"
                            {{ old('role') === 'admin' ? 'checked' : '' }}
                        />
                        <label for="role_admin" class="storix-role-label">
                            <span class="role-icon">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </span>
                            Admin
                        </label>
                    </div>
                    @error('role')
                        <p class="storix-input-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password + Confirm row --}}
                <div class="storix-pw-row">
                    {{-- Password --}}
                    <div class="storix-field">
                        <label class="storix-label" for="password">Password</label>
                        <div class="storix-input-wrap">
                            <span class="storix-input-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input
                                id="password"
                                class="storix-input"
                                type="password"
                                name="password"
                                placeholder="••••••••"
                                required
                                autocomplete="new-password"
                                style="padding-right:2.6rem;"
                            />
                            <button type="button" class="storix-eye-btn" aria-label="Toggle password"
                                onclick="
                                    var p=document.getElementById('password');
                                    p.type=p.type==='password'?'text':'password';
                                    this.querySelector('.s').style.display=p.type==='text'?'none':'block';
                                    this.querySelector('.h').style.display=p.type==='text'?'block':'none';
                                ">
                                <svg class="s" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="h" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="storix-input-error">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Confirm Password --}}
                    <div class="storix-field">
                        <label class="storix-label" for="password_confirmation">Confirm</label>
                        <div class="storix-input-wrap">
                            <span class="storix-input-icon">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4"/>
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </span>
                            <input
                                id="password_confirmation"
                                class="storix-input"
                                type="password"
                                name="password_confirmation"
                                placeholder="••••••••"
                                required
                                autocomplete="new-password"
                                style="padding-right:2.6rem;"
                            />
                            <button type="button" class="storix-eye-btn" aria-label="Toggle confirm password"
                                onclick="
                                    var p=document.getElementById('password_confirmation');
                                    p.type=p.type==='password'?'text':'password';
                                    this.querySelector('.s2').style.display=p.type==='text'?'none':'block';
                                    this.querySelector('.h2').style.display=p.type==='text'?'block':'none';
                                ">
                                <svg class="s2" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="h2" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="storix-input-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="storix-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/>
                        <line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    Create account
                </button>

                {{-- Divider --}}
                <div class="storix-divider">OR</div>

                {{-- Login link --}}
                <p class="storix-login-link">
                    Already have an account?
                    <a href="{{ route('login') }}">Sign in</a>
                </p>

            </form>
        </div>
    </div>

</div>

</body>
</html>