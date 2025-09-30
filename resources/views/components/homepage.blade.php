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
            --success-green: #28a745;
            --danger-red: #dc3545;
            --info-blue: #17a2b8;
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

        /* Header Section */
        .header-center {
            background: linear-gradient(135deg, rgba(0,0,0,0.95) 0%, rgba(26,26,26,0.95) 100%);
            backdrop-filter: blur(10px);
            border-bottom: 3px solid var(--primary-gold);
            box-shadow: 0 8px 32px rgba(255, 215, 0, 0.2);
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }

        .logo img {
            filter: drop-shadow(0 0 15px var(--primary-gold));
            transition: all 0.3s ease;
        }

        .logo img:hover {
            filter: drop-shadow(0 0 25px var(--primary-gold));
            transform: scale(1.05);
        }

        /* User Dashboard Panel */
        .login-form {
            background: var(--card-bg);
            border: 2px solid transparent;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 
                0 10px 40px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(255,255,255,0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            min-width: 300px;
        }

        .login-form::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--primary-gold), var(--accent-gold), var(--primary-gold));
            border-radius: 20px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .login-form:hover::before {
            opacity: 1;
        }

        /* Welcome Message */
        .welcome-message {
            color: var(--primary-gold);
            font-family: 'Orbitron', monospace;
            font-weight: 700;
            font-size: 1.1rem;
            text-shadow: var(--text-shadow);
            margin-bottom: 1rem;
            text-align: center;
        }

        .welcome-message i {
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        /* Action Buttons */
        .btn-secondary {
            background: linear-gradient(135deg, var(--info-blue) 0%, #138496 100%);
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(23, 162, 184, 0.3);
            margin-bottom: 0.5rem;
            width: 100%;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #138496 0%, var(--info-blue) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 162, 184, 0.5);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-red) 0%, #c82333 100%);
            border: none;
            border-radius: 12px;
            padding: 10px 20px;
            font-weight: 600;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            width: 100%;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #c82333 0%, var(--danger-red) 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.5);
            color: white;
        }

        /* Carousel */
        .carousel {
            margin: 2rem 0;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 15px 50px rgba(0,0,0,0.6);
        }

        .carousel-item img {
            filter: brightness(0.8) contrast(1.2);
            transition: all 0.5s ease;
            height: 400px;
            object-fit: cover;
        }

        .carousel-item.active img {
            filter: brightness(1) contrast(1.3);
        }

        .carousel-control-prev,
        .carousel-control-next {
            width: 60px;
            height: 60px;
            background: rgba(255, 215, 0, 0.8);
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
            transition: all 0.3s ease;
        }

        .carousel-control-prev {
            left: 20px;
        }

        .carousel-control-next {
            right: 20px;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            background: var(--primary-gold);
            transform: translateY(-50%) scale(1.1);
        }

        .carousel-indicators button {
            background: var(--primary-gold);
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 5px;
            border: 2px solid rgba(255, 215, 0, 0.5);
        }

        .carousel-indicators button.active {
            background: var(--accent-gold);
            transform: scale(1.2);
        }

        /* Product Cards */
        .product_list {
            list-style: none;
            overflow: hidden;
            position: relative;
            border-radius: 25px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        }

        .product_list:hover {
            transform: translateY(-15px) scale(1.03);
            box-shadow: 0 25px 50px rgba(255, 215, 0, 0.4);
        }

        .lotto-img {
            border: 3px solid transparent;
            transition: all 0.4s ease;
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 25px;
            filter: brightness(0.7) saturate(1.3);
        }

        .product_list:hover .lotto-img {
            border-color: var(--primary-gold);
            filter: brightness(1.1) saturate(1.5);
            box-shadow: 
                0 0 40px var(--hover-glow),
                inset 0 0 30px rgba(255, 215, 0, 0.1);
        }

        .pro_text {
            pointer-events: none;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 100%);
            transition: all 0.4s ease;
        }

        .pro_text h3 {
            color: var(--primary-gold) !important;
            font-weight: 900 !important;
            font-family: 'Orbitron', monospace !important;
            text-shadow: 
                3px 3px 6px rgba(0,0,0,0.9),
                0 0 25px var(--primary-gold);
            background: rgba(0,0,0,0.7) !important;
            border: 3px solid var(--primary-gold);
            border-radius: 20px !important;
            padding: 1.2rem 2.5rem !important;
            font-size: 1.4rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            animation: titlePulse 3s ease-in-out infinite;
        }

        @keyframes titlePulse {
            0% { 
                text-shadow: 3px 3px 6px rgba(0,0,0,0.9), 0 0 25px var(--primary-gold);
                border-color: var(--primary-gold);
            }
            50% { 
                text-shadow: 3px 3px 6px rgba(0,0,0,0.9), 0 0 35px var(--primary-gold), 0 0 50px var(--primary-gold);
                border-color: var(--accent-gold);
            }
            100% { 
                text-shadow: 3px 3px 6px rgba(0,0,0,0.9), 0 0 25px var(--primary-gold);
                border-color: var(--primary-gold);
            }
        }

        .product_list:hover .pro_text h3 {
            transform: scale(1.1);
            color: #fff !important;
            text-shadow: 
                3px 3px 6px rgba(0,0,0,0.9),
                0 0 40px var(--primary-gold),
                0 0 60px var(--primary-gold);
            animation: none;
        }

        /* Link styling */
        .product_list a {
            text-decoration: none;
            display: block;
        }

        .product_list a:hover {
            text-decoration: none;
        }

        /* Container styling */
        .container {
            max-width: 1400px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header-center {
                padding: 1rem;
            }
            
            .login-form {
                padding: 1.5rem;
                margin: 0 1rem;
                min-width: auto;
            }
            
            .pro_text h3 {
                font-size: 1.1rem !important;
                padding: 1rem 1.5rem !important;
                letter-spacing: 1px;
            }
            
            .logo img {
                max-width: 150px !important;
            }

            .welcome-message {
                font-size: 1rem;
            }

            .carousel-item img {
                height: 250px;
            }

            .lotto-img {
                height: 200px;
            }
        }

        @media (max-width: 576px) {
            .d-flex.flex-column.flex-md-row {
                gap: 1rem;
            }

            .login-form {
                width: 100%;
            }

            .pro_text h3 {
                font-size: 0.9rem !important;
                padding: 0.8rem 1.2rem !important;
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #1a1a1a;
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-gold), var(--accent-gold));
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--accent-gold), var(--primary-gold));
        }

        /* Additional enhancements */
        .user-info-card {
            background: linear-gradient(145deg, rgba(255,215,0,0.1) 0%, rgba(255,165,0,0.1) 100%);
            border: 1px solid rgba(255,215,0,0.3);
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

<div class="header-center container-fluid">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3">
            <!-- Left: Logo -->
            <div class="logo mb-3 mb-md-0">
                <a class="navbar-brand text-warning" href="#">
                    <img src="{{ asset('images/logo-2888.png') }}" style="max-width: 200px; height: auto;">
                </a>
            </div>

            <!-- Right: User Dashboard -->
            <div class="login-form">
                <!-- Welcome Message -->
                <div class="welcome-message">
                    <i class="fas fa-crown"></i>{{ __('message.welcome') }}, {{ Auth::user()->name ?? 'Guest' }}!
                </div>

        

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <!-- Account Management Link -->
                    <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-cog me-2"></i>{{ __('message.manage_account') }}
                    </a>

                    <!-- Logout Form -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">{{ __('message.log_out') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
                         alt="Slide {{ $index + 1 }}">
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
                    @auth
                        @php
                            $user = auth()->user();
                            $hasVND = $user->currencies()->where('currency', 'VND')->exists();
                            $hasUSD = $user->currencies()->where('currency', 'USD')->exists();
                            $isAdminOrManager = $user->roles->pluck('name')->intersect(['admin', 'manager'])->isNotEmpty();
                        @endphp

                        @php
                            $link = null;

                            if (!$isAdminOrManager) {
                                // Normal user
                                if ($hasVND && !$hasUSD) {
                                    $link = url('lotto_vn/bet');
                                } elseif ($hasUSD && !$hasVND) {
                                    $link = url('lotto_usd/bet');
                                }
                            } else {
                                // Admin or Manager
                                if ($hasVND && !$hasUSD) {
                                    $link = url('lotto_vn/receipt-list');
                                } elseif ($hasUSD && !$hasVND) {
                                    $link = url('lotto_usd/receipt-list');
                                }
                            }
                        @endphp

                        @if($link)
                            <a href="{{ $link }}">
                                <img src="{{ asset('uploads/images/' . ($menu->image ?? 'default_banner.jpg')) }}" 
                                     class="img-fluid lotto-img" 
                                     alt="{{ $menu->title ?? 'Lotto Image' }}">
                            </a>
                        @else
                            <img src="{{ asset('uploads/images/' . ($menu->image ?? 'default_banner.jpg')) }}" 
                                 class="img-fluid lotto-img" 
                                 alt="{{ $menu->title ?? 'Lotto Image' }}">
                        @endif

                        <div class="pro_text position-absolute w-100 h-100 top-0 start-0 d-flex">
                            <h3 class="text-center">
                                <i class="fas fa-dice me-2"></i>
                                {{ $menu->title ?? 'LOTTO' }}
                            </h3>
                        </div>
                    @endauth
                </li>
            </div>
        @endforeach
    </div>
</div>

<!-- Footer -->
<div class="footer mt-5 py-4" style="background: linear-gradient(135deg, rgba(0,0,0,0.9) 0%, rgba(26,26,26,0.9) 100%); border-top: 3px solid var(--primary-gold);">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <!-- Copyright -->
                <p style="color: var(--primary-gold); font-family: 'Orbitron', monospace; font-weight: 600; margin: 0;">
                    <i class="fas fa-crown me-2"></i>
                    © 2025 Lottery2888 - Your Premium Gaming Destination
                    <i class="fas fa-crown ms-2"></i>
                </p>

                <!-- Contact -->
                <p style="color: rgba(255,255,255,0.9); font-size: 0.95rem; margin-top: 1rem;">
                    <i class="fas fa-phone-alt me-2" style="color: var(--primary-gold);"></i>
                    <a href="tel:+85570956667" style="color: rgba(255,255,255,0.9); text-decoration: none;">+855 70 956 667</a> | 
                    <a href="tel:+855977900022" style="color: rgba(255,255,255,0.9); text-decoration: none;">+855 97 790 0022</a>
                </p>

                <!-- Telegram -->
                <p style="margin-top: 0.5rem;">
                    <a href="https://t.me/lottery2888" target="_blank" style="color: var(--primary-gold); font-size: 1rem; text-decoration: none;">
                        <i class="fab fa-telegram-plane me-1"></i> Join us on Telegram
                    </a>
                </p>
                 <!-- Bank Logos -->
                <div class="bank-logos mt-3">
                    <img src="/images/wing.jpg" alt="Wing" style="height: 50px; margin: 0 1px;">
                    <img src="/images/aba.jpg" alt="ABA" style="height: 50px; margin: 0 1px;">
                    <img src="/images/acleda.jpg" alt="ACLEDA" style="height: 50px; margin: 0 1px;">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Enhanced interactions and animations
    document.addEventListener('DOMContentLoaded', function() {
        // Smooth hover effects for product cards
        const productCards = document.querySelectorAll('.product_list');
        
        productCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-15px) scale(1.03)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Enhanced carousel functionality
        const carousel = document.querySelector('#carouselExample');
        if (carousel) {
            const carouselInstance = new bootstrap.Carousel(carousel, {
                interval: 4000,
                wrap: true,
                keyboard: true,
                pause: 'hover'
            });
        }

        // Add loading animation to buttons
        const actionButtons = document.querySelectorAll('.btn-secondary, .btn-danger');
        actionButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // Add loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                this.disabled = true;
                
                // Re-enable after a short delay (for forms that don't redirect immediately)
                setTimeout(() => {
                    if (this.innerHTML.includes('Loading...')) {
                        this.innerHTML = originalText;
                        this.disabled = false;
                    }
                }, 3000);
            });
        });

        // Add welcome animation
        const welcomeMessage = document.querySelector('.welcome-message');
        if (welcomeMessage) {
            welcomeMessage.style.opacity = '0';
            welcomeMessage.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                welcomeMessage.style.transition = 'all 0.5s ease';
                welcomeMessage.style.opacity = '1';
                welcomeMessage.style.transform = 'translateY(0)';
            }, 500);
        }
    });
</script>

</body>
</html>