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
        .header-center {
            width: 100%; /* Full width on smaller screens */
            height: auto;
            margin: auto;
        }
        .carousel-inner{
            width: 100%; /* Full width carousel */
            margin: auto;
        }
        .container {
            width: 100%; /* Full width container */
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
        /* Media Query for Mobile Devices */
        @media (max-width: 767px) {
            .header-center {
                width: 100%; /* Full width header on small screens */
            }
            .carousel-inner {
                width: 100%; /* Ensure carousel takes full width */
            }
            .container {
                width: 100%; /* Full width container */
            }
            .col-md-4 {
                width: 100%; /* Stack columns on small screens */
                margin-bottom: 20px; /* Space between stacked cards */
            }
            .card {
                margin-bottom: 20px;
            }
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
<div class="header-center container" style="max-width: 1300;">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center p-3">
        <!-- Left: Logo -->
        <div class="logo mb-9 mb-md-0">
            <a class="navbar-brand text-warning" href="#">
                <img src="{{ asset('images/logo2888.png') }}" style="max-width: 1200px; height: auto;">
            </a>
        </div>

        <!-- Right: Login Form -->
        <div class="login-form p-3 shadow-sm w-10 w-md-auto">
            <!-- Display Username -->
            <div class="mb-2 text-center text-md-start">
                <strong>Welcome, {{ Auth::user()->name ?? 'Guest' }}!</strong>
            </div>

            <!-- Account Management Link -->
            <div class="mb-2 text-center text-md-start">
                <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary btn-sm">Manage Account</a>
            </div>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}" class="text-center text-md-start">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Log out</button>
            </form>
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
                     <a href="{{ url('lotto_vn/bet') }}">
                    <img src="{{ asset('uploads/images/' .$menu->image ?? 'uploads/default_banner.jpg') }}" class="img-fluid lotto-img" alt="{{ $menu->title ?? 'Lotto Image' }}">
                    </a>
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
