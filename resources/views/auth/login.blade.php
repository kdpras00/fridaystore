<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Friday Store POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* ── Split layout ──────────────────────────────────────────── */
        .login-split {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ── Left panel — form ─────────────────────────────────────── */
        .login-left {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 36px 48px 32px;
            background: #fff;
        }

        .login-left-inner {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 360px;
            margin: 0 auto;
            width: 100%;
        }

        .login-logo {
            margin-bottom: 0;
        }

        .login-logo img {
            height: 36px;
            object-fit: contain;
        }

        .login-heading {
            font-size: 26px;
            font-weight: 700;
            color: var(--color-ink);
            letter-spacing: -0.03em;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .login-subheading {
            font-size: 13.5px;
            color: var(--color-ink-3);
            margin-bottom: 32px;
        }

        .login-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
        }

        .login-field label {
            font-size: 13px;
            font-weight: 500;
            color: var(--color-ink-2);
        }

        /* Input wrapper with icon support */
        .login-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .login-input-wrap .input-icon-left {
            position: absolute;
            left: 14px;
            display: flex;
            align-items: center;
            pointer-events: none;
            color: var(--color-ink-4);
        }

        .login-input-wrap .input-icon-right {
            position: absolute;
            right: 12px;
            display: flex;
            align-items: center;
            background: none;
            border: none;
            padding: 4px;
            cursor: pointer;
            color: var(--color-ink-4);
            border-radius: 4px;
            transition: color 120ms;
        }

        .login-input-wrap .input-icon-right:hover {
            color: var(--color-ink-2);
        }

        .login-field input {
            width: 100%;
            height: 44px;
            padding: 0 14px 0 40px; /* left padding for icon */
            border: 1px solid var(--color-border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--color-ink);
            background: #fff;
            outline: none;
            font-family: var(--font-sans);
            transition: border-color 140ms;
        }

        /* Password field has icon on both sides */
        .login-field input.has-right-icon {
            padding-right: 42px;
        }

        .login-field input:focus {
            border-color: var(--color-amber);
        }

        .login-field input::placeholder {
            color: var(--color-ink-4);
        }

        .login-field input.is-error {
            border-color: var(--color-danger);
        }

        .login-error {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            background: var(--color-danger-dim);
            border: 1px solid var(--color-danger);
            border-radius: 8px;
            font-size: 13px;
            color: var(--color-danger);
            margin-bottom: 18px;
        }

        .login-remember {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            margin-top: -4px;
        }

        .login-remember input[type="checkbox"] {
            width: 14px;
            height: 14px;
            accent-color: var(--color-amber);
            cursor: pointer;
        }

        .login-remember label {
            font-size: 13px;
            color: var(--color-ink-3);
            cursor: pointer;
        }

        .login-submit {
            width: 100%;
            height: 46px;
            background: var(--color-amber);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: var(--font-sans);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 140ms, opacity 140ms;
            letter-spacing: -0.01em;
        }

        .login-submit:hover {
            background: var(--color-amber-dim);
        }

        .login-submit:active {
            opacity: 0.88;
        }

        .login-footer-note {
            font-size: 11.5px;
            color: var(--color-ink-4);
            text-align: center;
            margin-top: 28px;
        }

        .login-bottom {
            font-size: 11px;
            color: var(--color-ink-4);
        }

        /* ── Right panel — showcase ────────────────────────────────── */
        .login-right {
            background: oklch(0.96 0.015 148);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 0 48px 0;
            position: relative;
            overflow: hidden;
            /* Match left panel's vertical rhythm by using same padding */
        }

        /* Subtle radial glow top-right */
        .login-right::before {
            content: '';
            position: absolute;
            top: -80px;
            right: -80px;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, oklch(0.30 0.10 148 / 0.12), transparent 70%);
            pointer-events: none;
        }

        .login-right-copy {
            position: relative;
            z-index: 1;
            margin-bottom: 32px;
            margin-top: 0;
        }

        .login-right-kicker {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 11px;
            font-weight: 600;
            color: var(--color-amber);
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .login-right-kicker span {
            width: 16px;
            height: 2px;
            background: var(--color-amber);
            border-radius: 1px;
        }

        .login-right-title {
            font-size: 30px;
            font-weight: 700;
            color: var(--color-ink);
            letter-spacing: -0.035em;
            line-height: 1.18;
            margin-bottom: 12px;
        }

        .login-right-sub {
            font-size: 14px;
            color: var(--color-ink-3);
            line-height: 1.55;
            max-width: 360px;
        }

        /*
         * Showcase: in-flow, meleber ke kanan, terpotong oleh overflow:hidden panel.
         * Static 3D tilt — no hover, matches reference image.
         */
        .login-showcase-wrap {
            position: relative;
            z-index: 2;
            width: calc(100% + 48px); /* melebar keluar padding kanan */
            margin-right: -48px;
            perspective: 1200px;
            perspective-origin: 20% 60%;
            flex-shrink: 0;
        }

        .login-right {
            /* override: flex-direction buat spacer + copy + showcase stack */
            flex-direction: column;
            justify-content: flex-start;
        }

        .login-showcase {
            border-radius: 10px 10px 0 0;
            overflow: hidden;
            background: #fff;
            border: 1px solid oklch(0 0 0 / 0.10);
            box-shadow:
                0 2px 4px oklch(0 0 0 / 0.06),
                0 12px 40px oklch(0 0 0 / 0.18),
                0 32px 64px oklch(0 0 0 / 0.12);
            /* Static 3D tilt — left-bottom anchor, tilted toward viewer */
            transform:
                rotateX(8deg)
                rotateY(-12deg)
                rotateZ(1deg);
            transform-origin: bottom left;
            transform-style: preserve-3d;
        }

        .login-showcase img {
            width: 100%;
            height: auto;
            display: block;
        }

        /* Placeholder when no screenshot yet */
        .login-showcase-placeholder {
            width: 100%;
            aspect-ratio: 16/10;
            background: linear-gradient(135deg, oklch(0.92 0.02 148), oklch(0.88 0.03 148));
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .login-showcase-placeholder svg { opacity: 0.3; }

        .login-showcase-placeholder p {
            font-size: 12px;
            color: var(--color-ink-3);
            text-align: center;
        }

        /* ── Responsive: stack on mobile ───────────────────────────── */
        @media (max-width: 768px) {
            .login-split {
                grid-template-columns: 1fr;
            }
            .login-right {
                display: none;
            }
            .login-left {
                padding: 28px 24px;
            }
        }
    </style>
</head>
<body style="margin:0;padding:0;">

<div class="login-split">

    {{-- ── LEFT: Form ─────────────────────────────────────────── --}}
    <div class="login-left">

        {{-- Top logo --}}
        <div class="login-logo">
            <img src="{{ asset('images/fridaylogo.png') }}" alt="Friday Store">
        </div>

        {{-- Center: form --}}
        <div class="login-left-inner">

            <h1 class="login-heading">Selamat datang kembali</h1>
            <p class="login-subheading">Masuk ke sistem kasir Friday Store</p>

            @if($errors->any())
            <div class="login-error">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="flex-shrink:0;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                </svg>
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
                @csrf

                <div class="login-field">
                    <label for="username">Username</label>
                    <div class="login-input-wrap">
                        <span class="input-icon-left">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </span>
                        <input
                            id="username" type="text" name="username"
                            value="{{ old('username') }}"
                            required autofocus
                            placeholder="JohnDoe"
                            autocomplete="username"
                            class="{{ $errors->has('username') ? 'is-error' : '' }}"
                        >
                    </div>
                </div>

                <div class="login-field">
                    <label for="password">Password</label>
                    <div class="login-input-wrap">
                        <span class="input-icon-left">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                            </svg>
                        </span>
                        <input
                            id="password" type="password" name="password"
                            required placeholder="••••••••"
                            autocomplete="current-password"
                            class="has-right-icon"
                        >
                        <button type="button" class="input-icon-right" onclick="togglePassword()" aria-label="Tampilkan password" id="pw-toggle">
                            {{-- Eye open (password hidden) --}}
                            <svg id="icon-eye" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{-- Eye off (password visible) --}}
                            <svg id="icon-eye-off" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="display:none;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="login-remember">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Ingat saya</label>
                </div>

                <button type="submit" class="login-submit">
                    Masuk
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </button>

                <p class="login-footer-note">
                    Friday Store POS &mdash; Sistem Internal
                </p>
            </form>

        </div>

        {{-- Bottom copyright --}}
        <p class="login-bottom">&copy; {{ date('Y') }} Friday Store. All rights reserved.</p>

    </div>

    {{-- ── RIGHT: Showcase ─────────────────────────────────────── --}}
    <div class="login-right">

        {{-- Spacer atas — match tinggi logo kiri (36px logo + 36px padding) --}}
        <div style="height:108px;flex-shrink:0;"></div>

        <div class="login-right-copy">
            <div class="login-right-kicker">
                <span></span>
                Point of Sale System
            </div>
            <h2 class="login-right-title">Kelola toko lebih mudah</h2>
            <p class="login-right-sub">
                Sistem kasir terpadu untuk Friday Store — transaksi cepat,
                stok real-time, dan laporan lengkap dalam satu tampilan.
            </p>
        </div>

        {{-- Showcase: in-flow, meleber ke kanan dan terpotong bawah --}}
        <div class="login-showcase-wrap">
            <div class="login-showcase">
                @if(file_exists(public_path('images/login-showcase.png')))
                    <img src="{{ asset('images/login-showcase.png') }}" alt="Friday Store POS Dashboard">
                @else
                    <div class="login-showcase-placeholder">
                        <svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--color-amber)" stroke-width="1.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0H3"/>
                        </svg>
                        <p>Taruh screenshot dashboard di<br><strong>public/images/login-showcase.png</strong></p>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>

<script>
function togglePassword() {
    var input   = document.getElementById('password');
    var iconEye    = document.getElementById('icon-eye');
    var iconEyeOff = document.getElementById('icon-eye-off');
    var isHidden = input.type === 'password';
    input.type           = isHidden ? 'text' : 'password';
    iconEye.style.display    = isHidden ? 'none'  : '';
    iconEyeOff.style.display = isHidden ? ''      : 'none';
}
</script>

</body>
</html>
