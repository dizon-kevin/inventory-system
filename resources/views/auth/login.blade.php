<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In — Storix</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=Space+Mono:wght@400;700&family=DM+Serif+Display:ital@0;1&display=swap" rel="stylesheet">

    <style>
        /* ── RESET & BASE ── */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            width: 100%;
            font-family: 'Sora', sans-serif;
            background: #080c14;
            overflow: hidden;
        }

        /* ════════════════════════════════
           ROOT: SPLIT LAYOUT
        ════════════════════════════════ */
        .storix-root {
            display: grid;
            grid-template-columns: 45% 55%;
            height: 100vh;
            width: 100vw;
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
            padding: 2.8rem 2.8rem 2.5rem;
            overflow: hidden;
        }

        /* Dot-grid background */
        .storix-left::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: radial-gradient(rgba(0, 212, 170, 0.18) 1px, transparent 1px);
            background-size: 32px 32px;
            animation: dotDrift 28s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes dotDrift {
            0%   { transform: translate(0, 0); }
            100% { transform: translate(32px, 32px); }
        }

        /* Ambient glows */
        .storix-glow {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            pointer-events: none;
            z-index: 0;
        }

        .storix-glow-1 {
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(0, 212, 170, 0.2), transparent 68%);
            top: -140px;
            left: -100px;
            animation: glowPulse 10s ease-in-out infinite alternate;
        }

        .storix-glow-2 {
            width: 340px;
            height: 340px;
            background: radial-gradient(circle, rgba(0, 90, 255, 0.18), transparent 68%);
            bottom: -80px;
            right: -80px;
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
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #00d4aa, #0077ff);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 24px rgba(0, 212, 170, 0.45);
            flex-shrink: 0;
        }

        .storix-logomark svg {
            width: 22px;
            height: 22px;
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

        /* Center visual */
        .storix-visual {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex: 1;
            padding: 2rem 0 1rem;
        }

        .storix-chart {
            display: flex;
            align-items: flex-end;
            gap: 14px;
            height: 220px;
            margin-bottom: 1.8rem;
        }

        .storix-bar {
            width: 42px;
            border-radius: 7px 7px 0 0;
            transform: scaleY(0);
            transform-origin: bottom;
            animation: barRise 0.9s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        .storix-bar::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 40%;
            background: linear-gradient(180deg, rgba(255,255,255,0.14), transparent);
            border-radius: 7px 7px 0 0;
        }

        .storix-bar:nth-child(1) { height: 52%;  background: linear-gradient(180deg, #7c6dfa, #4c38c8); animation-delay: 0.10s; }
        .storix-bar:nth-child(2) { height: 80%;  background: linear-gradient(180deg, #00d4aa, #009978); animation-delay: 0.20s; }
        .storix-bar:nth-child(3) { height: 38%;  background: linear-gradient(180deg, #00d4aa, #009978); animation-delay: 0.30s; }
        .storix-bar:nth-child(4) { height: 100%; background: linear-gradient(180deg, #00d4aa, #009978); animation-delay: 0.40s; }
        .storix-bar:nth-child(5) { height: 62%;  background: linear-gradient(180deg, #4da6ff, #0055cc); animation-delay: 0.50s; }
        .storix-bar:nth-child(6) { height: 83%;  background: linear-gradient(180deg, #4da6ff, #0055cc); animation-delay: 0.60s; }
        .storix-bar:nth-child(7) { height: 45%;  background: linear-gradient(180deg, #7c6dfa, #4c38c8); animation-delay: 0.70s; }

        @keyframes barRise {
            to { transform: scaleY(1); }
        }

        .storix-chart-label {
            font-size: 0.72rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(0, 212, 170, 0.4);
            text-align: center;
            line-height: 1.8;
        }

        /* Quote */
        .storix-quote {
            position: relative;
            z-index: 2;
        }

        .storix-quote-text {
            font-family: 'DM Serif Display', serif;
            font-style: italic;
            font-size: 1.1rem;
            color: rgba(210, 235, 228, 0.55);
            line-height: 1.65;
            margin-bottom: 0.65rem;
        }

        .storix-quote-attr {
            font-size: 0.68rem;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(0, 212, 170, 0.32);
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

        /* Subtle noise texture */
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
            max-width: 420px;
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

        /* Heading */
        .storix-heading {
            font-family: 'DM Serif Display', serif;
            font-size: 2.6rem;
            color: #0a1a15;
            line-height: 1.12;
            margin-bottom: 0.55rem;
            letter-spacing: -0.02em;
        }

        .storix-subheading {
            font-size: 0.84rem;
            color: rgba(15, 35, 28, 0.45);
            margin-bottom: 2.2rem;
            line-height: 1.55;
        }

        /* Alert/Session */
        .storix-alert {
            padding: 0.75rem 1rem;
            background: rgba(0, 168, 120, 0.1);
            border: 1px solid rgba(0, 168, 120, 0.25);
            border-radius: 9px;
            font-size: 0.82rem;
            color: #007a58;
            margin-bottom: 1.4rem;
        }

        /* Field */
        .storix-field {
            margin-bottom: 1.15rem;
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

        .storix-input {
            width: 100%;
            padding: 0.78rem 1rem 0.78rem 2.65rem;
            background: #ffffff;
            border: 1.5px solid #d4e3dc;
            border-radius: 10px;
            color: #0a1a15;
            font-family: 'Sora', sans-serif;
            font-size: 0.875rem;
            outline: none;
            transition: border-color 0.22s, box-shadow 0.22s;
            -webkit-appearance: none;
        }

        .storix-input::placeholder {
            color: rgba(10, 30, 22, 0.25);
        }

        .storix-input:focus {
            border-color: #00a878;
            box-shadow: 0 0 0 3.5px rgba(0, 168, 120, 0.1);
        }

        /* Autofill override */
        .storix-input:-webkit-autofill,
        .storix-input:-webkit-autofill:hover,
        .storix-input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 100px #ffffff inset !important;
            -webkit-text-fill-color: #0a1a15 !important;
        }

        .storix-input-error {
            font-size: 0.75rem;
            color: #c0392b;
            margin-top: 0.4rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Password toggle */
        .storix-eye-btn {
            position: absolute;
            right: 12px;
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

        .storix-eye-btn:hover {
            color: #00a878;
        }

        /* Remember + Forgot row */
        .storix-meta-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.6rem;
        }

        .storix-remember {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.8rem;
            color: rgba(10, 30, 22, 0.5);
            cursor: pointer;
            user-select: none;
        }

        .storix-remember input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: #00a878;
            cursor: pointer;
            border-radius: 3px;
        }

        .storix-forgot {
            font-size: 0.8rem;
            color: #00a878;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .storix-forgot:hover {
            color: #007558;
        }

        /* Submit button */
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
            margin-bottom: 1.5rem;
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

        /* OR divider */
        .storix-divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.4rem;
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

        /* Register link */
        .storix-register {
            text-align: center;
            font-size: 0.82rem;
            color: rgba(10, 30, 22, 0.45);
        }

        .storix-register a {
            color: #00a878;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .storix-register a:hover {
            color: #007558;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 860px) {
            html, body { overflow: auto; }
            .storix-root {
                grid-template-columns: 1fr;
                height: auto;
            }
            .storix-left {
                min-height: 300px;
                padding: 2rem;
            }
            .storix-chart { height: 150px; }
            .storix-bar   { width: 30px; }
            .storix-right {
                padding: 2.5rem 1.5rem;
            }
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

        {{-- Bar chart visual --}}
        <div class="storix-visual">
            <div class="storix-chart">
                <div class="storix-bar"></div>
                <div class="storix-bar"></div>
                <div class="storix-bar"></div>
                <div class="storix-bar"></div>
                <div class="storix-bar"></div>
                <div class="storix-bar"></div>
                <div class="storix-bar"></div>
            </div>
            <p class="storix-chart-label">Inventory · Metrics · Reports</p>
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

            <p class="storix-eyebrow">Welcome back</p>
            <h1 class="storix-heading">Sign in to<br>your workspace</h1>
            <p class="storix-subheading">Enter your credentials to access Storix.</p>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="storix-alert">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

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
                            autofocus
                            autocomplete="username"
                        />
                    </div>
                    @error('email')
                        <p class="storix-input-error">{{ $message }}</p>
                    @enderror
                </div>

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
                            autocomplete="current-password"
                            style="padding-right: 2.8rem;"
                        />
                        <button
                            type="button"
                            class="storix-eye-btn"
                            aria-label="Toggle password visibility"
                            onclick="
                                var p = document.getElementById('password');
                                var isHidden = p.type === 'password';
                                p.type = isHidden ? 'text' : 'password';
                                this.querySelector('.icon-show').style.display = isHidden ? 'none' : 'block';
                                this.querySelector('.icon-hide').style.display = isHidden ? 'block' : 'none';
                            "
                        >
                            <svg class="icon-show" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="icon-hide" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="storix-input-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember Me + Forgot Password --}}
                <div class="storix-meta-row">
                    <label class="storix-remember" for="remember_me">
                        <input id="remember_me" type="checkbox" name="remember">
                        Remember me
                    </label>
                    @if (Route::has('password.request'))
                        <a class="storix-forgot" href="{{ route('password.request') }}">Forgot password?</a>
                    @endif
                </div>

                {{-- Submit --}}
                <button type="submit" class="storix-btn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                    Sign in
                </button>

                {{-- Divider --}}
                <div class="storix-divider">OR</div>

                {{-- Register --}}
                <p class="storix-register">
                    Don't have an account?
                    <a href="{{ route('register') }}">Create one</a>
                </p>

            </form>
        </div>
    </div>

</div>

</body>
</html>