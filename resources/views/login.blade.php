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
    
    <style>
        body {
            background-color: #000;
            color: white;
        }
        .card {
            background-color: black;
            border: none;
            color: white;
            transition: 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .card img {
            opacity: 0.7;
        }
        .footer {
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            background-color: #111;
        }
        .lotto-img {
            border: 2px solid #ddd;
            transition: border-color 0.3s ease;
            width: 100%;
            height: auto;
        }

        .lotto-img:hover {
            border-color: yellow; /* or any hover color you like */
        }

        .product_list {
            list-style: none;
            overflow: hidden;
            position: relative;
        }

        .pro_text {
            pointer-events: none; /* So the hover works on the image */
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container" style="max-width: 1300px;"> <!-- Restrict navbar width -->
        <a class="navbar-brand text-warning" href="#">
        <img src="{{ asset('images/logo-2888.png') }}" style="max-width: 200px; height: auto;" >
        </a>
        <div class=" navbar-collapse justify-content-end" id="navbarNav">
        <form action="{{ route('lang.switch', app()->getLocale()) }}" method="GET" class="me-3">
        <select onchange="location = this.value;" class="form-select form-select-sm bg-dark text-white border-light">
            <option value="{{ route('lang.switch', 'en') }}" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>🇺🇸 English</option>
            <option value="{{ route('lang.switch', 'vi') }}" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>🇻🇳 Tiếng Việt</option>
        </select>

            </form>
            <form action="{{ route('login') }}" method="POST" class="d-flex flex-column flex-lg-row align-items-lg-center">
                @csrf
                <div class="d-flex flex-column me-2">
                    <input id="username" class="form-control mb-2" type="username" placeholder="{{ __('message.username') }}" name="username" required>
                    <input id="password" class="form-control mb-2" type="password" name="password" required placeholder="{{ __('message.password') }}">
                    <div class="form-check"> <!-- Checkbox now properly placed below password -->
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label text-white" for="remember">{{ __('message.remember_me') }}</label>
                    </div>
                </div>
                <button class="btn btn-warning">{{ __('message.submit') }}</button>
            </form>
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
        <div class="carousel-inner mx-auto" style="max-width: 1300px;">
        @foreach ($betMenus as $menu)
            <div class="carousel-item active" data-bs-interval="3000">
                <img src="{{ asset('uploads/banners/' .$menu->banner ?? 'uploads/default_banner.jpg') }}" class="d-block w-100 img-fluid" alt="Slide 1" style="width: 100%;">
            </div>
         @endforeach
        </div>
    </div>
</div>


<!-- Main Content -->
<div class="container mt-4">
    <div class="row g-4">
        <!-- Lotto -->
        @foreach ($betMenus as $menu)
            <div class="col-12 col-md-6 col-lg-4">
                <li class="product_list position-relative">
                    <img src="{{ asset('uploads/images/' .$menu->image ?? 'uploads/default_banner.jpg') }}" class="img-fluid lotto-img" alt="{{ $menu->title ?? 'Lotto Image' }}">
                    
                    <div class="pro_text position-absolute w-100 h-100 top-0 start-0 d-flex">
                        <h3 style="color: yellow; font-weight: 600;" class="bg-opacity-50 px-3 py-2 rounded">
                            {{ $menu->title ?? 'LOTTO' }}
                        </h3>
                    </div>
                </li>
            </div>
        @endforeach
    </div>
</div>


<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
