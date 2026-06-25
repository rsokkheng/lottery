<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Lottery2888') }}</title>
    <link rel="icon" href="{{ asset('images/snooker.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Exo+2:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --gold:      #FFD700;
            --gold-dark: #FFA500;
            --bg:        #0a0a0a;
            --card-bg:   linear-gradient(145deg, #1a1a1a, #2d2d2d);
            --glow:      rgba(255,215,0,.3);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            background: var(--bg);
            background-image:
                radial-gradient(circle at 25% 25%, #1a1a1a 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, #2d2d2d 0%, transparent 50%);
            color: #fff;
            font-family: 'Exo 2', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* sparkle overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(2px 2px at 20px 30px, var(--gold), transparent),
                radial-gradient(2px 2px at 40px 70px, var(--gold-dark), transparent),
                radial-gradient(1px 1px at 90px 40px, var(--gold), transparent);
            background-repeat: repeat;
            background-size: 200px 200px;
            animation: sparkle 20s linear infinite;
            opacity: .08;
            z-index: -1;
            pointer-events: none;
        }
        @keyframes sparkle {
            from { transform: translateY(0); }
            to   { transform: translateY(-200px); }
        }

        /* ── Navbar ── */
        .site-nav {
            background: linear-gradient(135deg, rgba(0,0,0,.97), rgba(26,26,26,.97));
            border-bottom: 2px solid var(--gold);
            box-shadow: 0 4px 20px rgba(255,215,0,.15);
            padding: .9rem 0;
        }

        .brand-logo {
            filter: drop-shadow(0 0 8px var(--gold));
            transition: filter .3s, transform .3s;
            max-width: 190px;
        }
        .brand-logo:hover {
            filter: drop-shadow(0 0 18px var(--gold));
            transform: scale(1.04);
        }

        /* ── Auth panel (logged-in) ── */
        .auth-panel {
            background: linear-gradient(160deg, #1c1c1c, #111);
            border-radius: 18px;
            padding: 1.25rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            min-width: 240px;
            box-shadow: 0 8px 32px rgba(0,0,0,.6);
        }
        .welcome-title {
            color: var(--gold);
            font-family: 'Orbitron', monospace;
            font-weight: 700;
            font-size: 1.05rem;
            text-align: center;
            letter-spacing: 1px;
        }
        .welcome-title .fas { color: var(--gold-dark); }

        .btn-manager {
            background: linear-gradient(135deg, #1b8a8f, #1da7ad);
            color: #fff;
            font-family: 'Orbitron', monospace;
            font-weight: 700;
            font-size: .8rem;
            letter-spacing: 1.5px;
            border: none;
            border-radius: 10px;
            padding: .65rem;
            transition: filter .2s, transform .2s;
        }
        .btn-manager:hover {
            filter: brightness(1.15);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-logout {
            background: linear-gradient(135deg, #c0392b, #e74c3c);
            color: #fff;
            font-family: 'Orbitron', monospace;
            font-weight: 700;
            font-size: .8rem;
            letter-spacing: 1.5px;
            border: none;
            border-radius: 10px;
            padding: .65rem;
            transition: filter .2s, transform .2s;
        }
        .btn-logout:hover {
            filter: brightness(1.15);
            color: #fff;
            transform: translateY(-1px);
        }

        /* ── Login card ── */
        .login-card {
            background: linear-gradient(160deg, #1c1c1c, #111);
            border-radius: 18px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 8px 32px rgba(0,0,0,.6);
            width: 40%;
            float: inline-end;
        }

        /* ── Inputs ── */
        .form-control {
            background: #e8eaf0;
            border: none;
            color: #222;
            border-radius: 14px;
            padding: .6rem 1rem .6rem 44px;
            font-size: .95rem;
            height: 52px;
            transition: box-shadow .25s;
        }
        .form-control:focus {
            background: #f0f2f8;
            border: none;
            box-shadow: 0 0 0 3px var(--glow);
            color: #222;
        }
        .form-control::placeholder { color: #888; }

        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gold-dark);
            font-size: 1rem;
            pointer-events: none;
        }

        /* ── Language select ── */
        .lang-select {
            background: linear-gradient(145deg, #2d2d2d, #1a1a1a) !important;
            border: 2px solid #444 !important;
            color: var(--gold) !important;
            border-radius: 8px;
            font-size: .82rem;
            font-weight: 600;
            cursor: pointer;
            transition: border-color .25s;
        }
        .lang-select:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 10px var(--glow);
        }

        /* ── Buttons ── */
        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border: none;
            color: #000;
            font-weight: 700;
            font-family: 'Orbitron', monospace;
            font-size: .9rem;
            border-radius: 14px;
            letter-spacing: 1px;
            text-transform: uppercase;
            box-shadow: 0 4px 14px var(--glow);
            transition: transform .2s, box-shadow .2s, background .2s;
            white-space: nowrap;
            height: 52px;
        }
        .btn-gold:hover {
            background: linear-gradient(135deg, var(--gold-dark), var(--gold));
            color: #000;
            transform: translateY(-2px);
            box-shadow: 0 8px 22px var(--glow);
        }
        .btn-outline-gold {
            border: 2px solid var(--gold);
            color: var(--gold);
            background: transparent;
            border-radius: 10px;
            font-weight: 600;
            font-size: .85rem;
            white-space: nowrap;
            transition: background .2s;
        }
        .btn-outline-gold:hover {
            background: rgba(255,215,0,.12);
            color: var(--gold);
        }

        /* ── Error alert ── */
        .login-error {
            background: rgba(220,53,69,.15);
            border: 1px solid rgba(220,53,69,.5);
            color: #f8a8ae;
            border-radius: 8px;
            padding: .55rem .9rem;
            font-size: .83rem;
        }

        /* ── Carousel ── */
        .carousel-wrap {
            margin: 1.75rem 0 1rem;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,.55);
        }
        .carousel-wrap .carousel-item img {
            width: 100%;
            max-height: 420px;
            object-fit: cover;
            filter: brightness(.85) contrast(1.1);
        }
        .carousel-wrap .carousel-item.active img {
            filter: brightness(1) contrast(1.15);
        }

        /* ── Product cards ── */
        .product-card {
            list-style: none;
            padding: 0;
            position: relative;
            overflow: hidden;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0,0,0,.35);
            transition: transform .4s cubic-bezier(.175,.885,.32,1.275), box-shadow .4s;
        }
        .product-card:hover {
            transform: translateY(-9px) scale(1.02);
            box-shadow: 0 20px 40px var(--glow);
        }
        .product-card .card-img {
            width: 100%;
            height: 230px;
            object-fit: cover;
            border-radius: 18px;
            filter: brightness(.7) saturate(1.2);
            border: 3px solid transparent;
            transition: filter .4s, border-color .4s;
        }
        .product-card:hover .card-img {
            filter: brightness(1) saturate(1.4);
            border-color: var(--gold);
        }
        .card-label {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0,0,0,.65), rgba(0,0,0,.25));
            border-radius: 18px;
            pointer-events: none;
        }
        .card-label h3 {
            color: var(--gold);
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            font-size: 1.35rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            text-shadow: 2px 2px 4px rgba(0,0,0,.9), 0 0 20px var(--gold);
            background: rgba(0,0,0,.55);
            border: 2px solid var(--gold);
            border-radius: 12px;
            padding: .8rem 1.75rem;
            margin: 0;
            animation: pulse 2.5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%,100% { text-shadow: 2px 2px 4px rgba(0,0,0,.9), 0 0 18px var(--gold); }
            50%      { text-shadow: 2px 2px 4px rgba(0,0,0,.9), 0 0 32px var(--gold), 0 0 48px var(--gold); }
        }
        .product-card:hover .card-label h3 {
            color: #fff;
            text-shadow: 2px 2px 4px rgba(0,0,0,.9), 0 0 30px var(--gold), 0 0 50px var(--gold);
            transform: scale(1.08);
        }

        /* ── Footer ── */
        .site-footer {
            background: linear-gradient(135deg, rgba(0,0,0,.93), rgba(26,26,26,.93));
            border-top: 3px solid var(--gold);
            padding: 1.75rem 0;
            margin-top: 3rem;
            text-align: center;
        }
        .site-footer a { color: rgba(255,255,255,.85); text-decoration: none; }
        .site-footer a:hover { color: var(--gold); }
        .bank-logo { height: 48px; margin: 0 3px; border-radius: 5px; object-fit: contain; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 7px; }
        ::-webkit-scrollbar-track { background: #111; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border-radius: 4px;
        }

        /* ── Tablet & below (< 992px) ── */
        @media (max-width: 991px) {
            .brand-logo { max-width: 155px; }
            .login-card { width: 100%; float: none; padding: 1.1rem 1.25rem; }
            .auth-panel { width: 100%; }
        }

        /* ── Phone (< 576px) ── */
        @media (max-width: 575px) {
            .login-card { padding: 1rem; border-radius: 14px; }
            .form-control { height: 50px; font-size: .9rem; }
            .btn-gold { font-size: .85rem; padding-top: .65rem; padding-bottom: .65rem; }
        }
    </style>
</head>
<body>

{{-- ════════════════════════════ NAVBAR ════════════════════════════ --}}
<nav class="site-nav">
    <div class="container-xl">
        <div class="row align-items-center g-3">

            {{-- Logo --}}
            <div class="col-12 col-lg-auto text-center text-lg-start">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo-2888.png') }}" class="brand-logo" alt="Lottery2888">
                </a>
            </div>

            {{-- Right panel --}}
            <div class="col-12 col-lg d-flex justify-content-lg-end">

                @auth
                {{-- ── Logged-in state ── --}}
                <div class="auth-panel">
                    <div class="welcome-title">
                        <i class="fas fa-crown"></i> Welcome, {{ auth()->user()->username }}!
                    </div>
                    <a href="{{ route('admin.homepage') }}" class="btn btn-manager w-100">
                        <i class="fas fa-cog me-2"></i>MANAGER ACCOUNT
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-logout w-100">
                            LOGOUT
                        </button>
                    </form>
                </div>

                @else
                {{-- ── Guest state: login form ── --}}
                <div class="login-card">
                    <form action="{{ route('login') }}" method="POST" novalidate>
                        @csrf

                        @if ($errors->any())
                            <div class="login-error mb-3">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                {{ $errors->first() }}
                            </div>
                        @endif

                        {{-- Username --}}
                        <div class="input-icon mb-2">
                            <i class="fas fa-user"></i>
                            <input type="text" name="username"
                                class="form-control w-100 @error('username') is-invalid @enderror"
                                placeholder="{{ __('message.username') }}"
                                value="{{ old('username') }}"
                                required autofocus autocomplete="username">
                        </div>

                        {{-- Password --}}
                        <div class="input-icon mb-3">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password"
                                class="form-control w-100 @error('password') is-invalid @enderror"
                                placeholder="{{ __('message.password') }}"
                                required autocomplete="current-password">
                        </div>

                        {{-- Remember + Lang --}}
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="form-check mb-0">
                                <input class="form-check-input" type="checkbox"
                                       id="remember" name="remember"
                                       {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label text-white" for="remember" style="font-size:.85rem;">
                                    {{ __('message.remember_me') }}
                                </label>
                            </div>
                            <select class="form-select form-select-sm lang-select" style="width:auto;"
                                    onchange="location.href=this.value">
                                <option value="{{ route('lang.switch','en') }}" {{ app()->getLocale()==='en'?'selected':'' }}>🇺🇸 EN</option>
                                <option value="{{ route('lang.switch','vi') }}" {{ app()->getLocale()==='vi'?'selected':'' }}>🇻🇳 VI</option>
                                <option value="{{ route('lang.switch','km') }}" {{ app()->getLocale()==='km'?'selected':'' }}>🇰🇭 KM</option>
                            </select>
                        </div>

                        {{-- Submit --}}
                        <button type="submit" class="btn btn-gold w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('message.submit') }}
                        </button>

                    </form>
                </div>
                @endauth

            </div>
        </div>
    </div>
</nav>

{{-- ════════════════════════════ CONTENT ════════════════════════════ --}}
@php
    use App\Models\Menu;
    $betMenus = Menu::all();
@endphp

<div class="container-xl">

    {{-- Carousel --}}
    @if ($betMenus->isNotEmpty())
    <div class="carousel-wrap">
        <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach ($betMenus as $i => $menu)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}" data-bs-interval="4000">
                        <img src="{{ asset('uploads/banners/' . ($menu->banner ?: 'default_banner.jpg')) }}"
                             alt="{{ $menu->title }}" class="d-block w-100">
                    </div>
                @endforeach
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>

            <div class="carousel-indicators">
                @foreach ($betMenus as $i => $menu)
                    <button type="button" data-bs-target="#mainCarousel"
                            data-bs-slide-to="{{ $i }}"
                            class="{{ $i === 0 ? 'active' : '' }}"
                            aria-label="Slide {{ $i + 1 }}"></button>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Product cards --}}
    <div class="row g-4 mt-1 mb-4">
        @foreach ($betMenus as $menu)
            <div class="col-12 col-sm-6 col-lg-4">
                <li class="product-card">
                    <img src="{{ asset('uploads/images/' . ($menu->image ?: 'default_banner.jpg')) }}"
                         class="card-img" alt="{{ $menu->title }}">
                    <div class="card-label">
                        <h3><i class="fas fa-dice me-2"></i>{{ $menu->title }}</h3>
                    </div>
                </li>
            </div>
        @endforeach
    </div>

</div>

{{-- ════════════════════════════ FOOTER ════════════════════════════ --}}
<footer class="site-footer">
    <div class="container-xl">
        <p class="mb-2" style="color:var(--gold);font-family:'Orbitron',monospace;font-weight:700;">
            <i class="fas fa-crown me-1"></i>
            &copy; {{ date('Y') }} Lottery2888 — Your Premium Gaming Destination
            <i class="fas fa-crown ms-1"></i>
        </p>
        <p class="mb-2 small">
            <i class="fas fa-phone-alt me-1" style="color:var(--gold)"></i>
            <a href="tel:+85531469288">+855 031 469 2888</a>
            &nbsp;|&nbsp;
            <a href="tel:+855977900022">+855 97 790 0022</a>
        </p>
        <p class="mb-3 small">
            <a href="https://t.me/lottery2888" target="_blank" style="color:var(--gold)">
                <i class="fab fa-telegram-plane me-1"></i>Join us on Telegram
            </a>
        </p>
        <div>
            <img src="{{ asset('images/wing.jpg') }}"   alt="Wing"   class="bank-logo">
            <img src="{{ asset('images/aba.jpg') }}"    alt="ABA"    class="bank-logo">
            <img src="{{ asset('images/acleda.jpg') }}" alt="ACLEDA" class="bank-logo">
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
