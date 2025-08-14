@section('title')
    {{ 'Log in' }}
@endsection
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Lottery2888') }}</title>
    <link rel="icon" href="{{ asset('images/snooker.png') }}" type="image/png">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Exo+2:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gold: #FFD700;
            --accent-gold: #FFA500;
            --dark-bg: #0a0a0a;
            --card-bg: linear-gradient(145deg, #1a1a1a 0%, #2d2d2d 100%);
            --hover-glow: rgba(255, 215, 0, 0.3);
            --text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--dark-bg);
            background-image: 
                radial-gradient(circle at 25% 25%, #1a1a1a 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, #2d2d2d 0%, transparent 50%),
                linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            color: white;
            font-family: 'Exo 2', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated background particles */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(2px 2px at 20px 30px, var(--primary-gold), transparent),
                radial-gradient(2px 2px at 40px 70px, var(--accent-gold), transparent),
                radial-gradient(1px 1px at 90px 40px, var(--primary-gold), transparent);
            background-repeat: repeat;
            background-size: 200px 200px;
            animation: sparkle 20s linear infinite;
            opacity: 0.1;
            z-index: -1;
        }

        @keyframes sparkle {
            0% { transform: translateY(0); }
            100% { transform: translateY(-200px); }
        }

        /* Navbar Styling */
        .navbar {
            background: linear-gradient(135deg, rgba(0,0,0,0.95) 0%, rgba(26,26,26,0.95) 100%);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--primary-gold);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
            padding: 1rem 0;
        }

        .navbar-brand img {
            filter: drop-shadow(0 0 10px var(--primary-gold));
            transition: all 0.3s ease;
        }

        .navbar-brand img:hover {
            filter: drop-shadow(0 0 20px var(--primary-gold));
            transform: scale(1.05);
        }

        /* Language Selector */
        .form-select {
            background: linear-gradient(145deg, #2d2d2d, #1a1a1a) !important;
            border: 2px solid transparent;
            color: var(--primary-gold) !important;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-select:hover, .form-select:focus {
            border-color: var(--primary-gold);
            box-shadow: 0 0 15px var(--hover-glow);
            background: linear-gradient(145deg, #3d3d3d, #2a2a2a) !important;
        }

        /* Login Form */
        .login-form {
            background: var(--card-bg);
            border: 2px solid transparent;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 
                0 8px 32px rgba(0,0,0,0.5),
                inset 0 1px 0 rgba(255,255,255,0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-form::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--primary-gold), var(--accent-gold), var(--primary-gold));
            border-radius: 15px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .login-form:hover::before {
            opacity: 1;
        }

        .form-control {
            background: rgba(26, 26, 26, 0.8);
            border: 2px solid #333;
            color: white;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(26, 26, 26, 0.9);
            border-color: var(--primary-gold);
            box-shadow: 0 0 15px var(--hover-glow);
            color: white;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--primary-gold) 0%, var(--accent-gold) 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 700;
            color: #000;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Orbitron', monospace;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .btn-warning:hover {
            background: linear-gradient(135deg, var(--accent-gold) 0%, var(--primary-gold) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 215, 0, 0.5);
            color: #000;
        }

        .form-check-label {
            font-weight: 500;
            text-shadow: var(--text-shadow);
        }

        /* Carousel */
        .carousel {
            margin: 2rem 0;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0,0,0,0.5);
        }

        .carousel-item img {
            filter: brightness(0.8) contrast(1.1);
            transition: all 0.5s ease;
        }

        .carousel-item.active img {
            filter: brightness(1) contrast(1.2);
        }

        /* Product Cards */
        .product_list {
            list-style: none;
            overflow: hidden;
            position: relative;
            border-radius: 20px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
        }

        .product_list:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(255, 215, 0, 0.3);
        }

        .lotto-img {
            border: 3px solid transparent;
            transition: all 0.4s ease;
            width: 100%;
            height: auto;
            border-radius: 20px;
            filter: brightness(0.7) saturate(1.2);
        }

        .product_list:hover .lotto-img {
            border-color: var(--primary-gold);
            filter: brightness(1) saturate(1.4);
            box-shadow: 
                0 0 30px var(--hover-glow),
                inset 0 0 20px rgba(255, 215, 0, 0.1);
        }

        .pro_text {
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.3) 100%);
            transition: all 0.4s ease;
        }

        .pro_text h3 {
            color: var(--primary-gold) !important;
            font-weight: 900 !important;
            font-family: 'Orbitron', monospace !important;
            text-shadow: 
                2px 2px 4px rgba(0,0,0,0.8),
                0 0 20px var(--primary-gold);
            background: rgba(0,0,0,0.6) !important;
            border: 2px solid var(--primary-gold);
            border-radius: 15px !important;
            padding: 1rem 2rem !important;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { text-shadow: 2px 2px 4px rgba(0,0,0,0.8), 0 0 20px var(--primary-gold); }
            50% { text-shadow: 2px 2px 4px rgba(0,0,0,0.8), 0 0 30px var(--primary-gold), 0 0 40px var(--primary-gold); }
            100% { text-shadow: 2px 2px 4px rgba(0,0,0,0.8), 0 0 20px var(--primary-gold); }
        }

        .product_list:hover .pro_text h3 {
            transform: scale(1.1);
            color: #fff !important;
            text-shadow: 
                2px 2px 4px rgba(0,0,0,0.8),
                0 0 30px var(--primary-gold),
                0 0 50px var(--primary-gold);
        }

        /* Container max-width */
        .container {
            max-width: 1400px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-form {
                padding: 1rem;
                margin: 0 1rem;
            }
            
            .pro_text h3 {
                font-size: 1.2rem !important;
                padding: 0.8rem 1.5rem !important;
            }
            
            .navbar-brand img {
                max-width: 150px !important;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-gold), var(--accent-gold));
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--accent-gold), var(--primary-gold));
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand text-warning" href="#">
            <img src="{{ asset('images/logo-2888.png') }}" style="max-width: 200px; height: auto;">
        </a>
        
        <div class="navbar-collapse justify-content-end" id="navbarNav">            
            <div class="login-form">
                <form action="{{ route('login') }}" method="POST" class="d-flex flex-column flex-lg-row align-items-lg-center">
                    @csrf
                    <div class="d-flex flex-column me-2">
                        <div class="position-relative mb-2">
                            <i class="fas fa-user position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--primary-gold);"></i>
                            <input id="username" class="form-control ps-5" type="username" placeholder="{{ __('message.username') }}" name="username" required>
                        </div>
                        <div class="position-relative mb-2">
                            <i class="fas fa-lock position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--primary-gold);"></i>
                            <input id="password" class="form-control ps-5" type="password" name="password" required placeholder="{{ __('message.password') }}">
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label text-white" for="remember">
                                <i class="fas fa-remember-me me-1"></i>{{ __('message.remember_me') }}
                            </label>
                        </div>
                    </div>
                    <div class="d-flex flex-column align-items-center">
                    <form action="{{ route('lang.switch', app()->getLocale()) }}" method="GET" class="mt-6 mb-3">
                            <select onchange="location = this.value;" class="form-select form-select-sm" style="min-width: 140px;">
                                <option value="{{ route('lang.switch', 'en') }}" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>
                                    🇺🇸 English
                                </option>
                                <option value="{{ route('lang.switch', 'vi') }}" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>
                                    🇻🇳 Tiếng Việt
                                </option>
                            </select>
                        </form>
                        <button class="btn btn-warning ">
                            <i class="fas fa-sign-in-alt me-2"></i>{{ __('message.submit') }}
                        </button>
                       
                    </div>
                </form>
            </div>
        </div>
    </div>
</nav>

@php
    use App\Models\Menu;
    $betMenus = Menu::all();
@endphp

<!-- Carousel -->
<div class="container">
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            @foreach ($betMenus as $index => $menu)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" data-bs-interval="4000">
                    <img src="{{ asset('uploads/banners/' .($menu->banner ?? 'uploads/default_banner.jpg')) }}" 
                         class="d-block w-100 img-fluid" 
                         alt="Slide {{ $index + 1 }}" 
                         style="width: 100%; height: 400px; object-fit: cover;">
                </div>
            @endforeach
        </div>
        
        <!-- Carousel Controls -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        
        <!-- Carousel Indicators -->
        <div class="carousel-indicators">
            @foreach ($betMenus as $index => $menu)
                <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="{{ $index }}" 
                        class="{{ $index === 0 ? 'active' : '' }}" aria-current="true" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-5">
    <div class="row g-4">
        @foreach ($betMenus as $menu)
            <div class="col-12 col-md-6 col-lg-4">
                <li class="product_list position-relative">
                    <img src="{{ asset('uploads/images/' .($menu->image ?? 'uploads/default_banner.jpg')) }}" 
                         class="img-fluid lotto-img" 
                         alt="{{ $menu->title ?? 'Lotto Image' }}">
                    
                    <div class="pro_text position-absolute w-100 h-100 top-0 start-0 d-flex">
                        <h3 class="text-center">
                            <i class="fas fa-dice me-2"></i>
                            {{ $menu->title ?? 'LOTTO' }}
                        </h3>
                    </div>
                </li>
            </div>
        @endforeach
    </div>
</div>

<!-- Footer -->
<div class="footer mt-5 py-4" style="background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(26,26,26,0.9) 100%); border-top: 2px solid var(--primary-gold);">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <p style="color: var(--primary-gold); font-family: 'Orbitron', monospace; font-weight: 600;">
                    <i class="fas fa-crown me-2"></i>
                    © 2025 Lottery2888 - Your Premium Gaming Destination
                    <i class="fas fa-crown ms-2"></i>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Add smooth scrolling and enhanced interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth hover effects for product cards
        const productCards = document.querySelectorAll('.product_list');
        
        productCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Enhanced carousel auto-play
        const carousel = document.querySelector('#carouselExample');
        if (carousel) {
            const carouselInstance = new bootstrap.Carousel(carousel, {
                interval: 4000,
                wrap: true,
                keyboard: true
            });
        }
    });
</script>

</body>
</html>