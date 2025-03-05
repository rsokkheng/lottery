<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Happy Lottery 2888</title>

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
            margin-top: 20px;
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
    </style>
</head>
<body>

<div class="header-center" style="max-width: 1300px;">
    <div class="d-flex justify-content-between align-items-center p-3 " >
        <!-- Left: Logo -->
        <div class="logo">
            <h2 class="text-warning">Happy Lottery 2888</h2>
            <p class="text-white">The more you play, the more you win</p>
        </div>

        <!-- Right: Login Form -->
        <div class="login-form p-3 shadow-sm">
            <!-- Display Username -->
            <div class="mb-3">
                <strong>Welcome, {{ Auth::user()->name ?? 'Guest' }}!</strong>
            </div>

            <!-- Account Management Link -->
            <div class="mb-3">
                <a href="{{ url('/admin/dashboard') }}" class="btn btn-secondary btn-sm">Manage Account</a>
            </div>

            <!-- Logout Form -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Log out</button>
            </form>
        </div>
    </div>
</div>

<!-- Carousel -->
<div class="container">
    <div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner mx-auto" style="max-width: 1300px;">
            <div class="carousel-item active" data-bs-interval="3000">
                <img src="{{ asset('images/ads1.png') }}" class="d-block w-100 img-fluid" alt="Slide 1">
            </div>
            <div class="carousel-item" data-bs-interval="3000">
                <img src="{{ asset('images/ads2.png') }}" class="d-block w-100 img-fluid" alt="Slide 2">
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="container mt-4">
    <div class="row g-4">
        <!-- Lotto -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <a href="{{ url('lotto_vn/bet') }}">
                    <img src="{{ asset('images/ad_intro_lotto.jpg') }}" class="card-img-top img-fluid" alt="Lotto">
                </a>
                <div class="card-body">
                    <h5 class="card-title">LOTTO</h5>
                    <p class="card-text">Lotto Vietnam & Lotto Cambodia</p>
                </div>
            </div>
        </div>

        <!-- Sports -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <a href="#sportsLink">
                    <img src="{{ asset('images/ad_intro_sports.jpg') }}" class="card-img-top img-fluid" alt="Sports">
                </a>
                <div class="card-body">
                    <h5 class="card-title">SPORTS</h5>
                    <p class="card-text">Enjoy Football & Basketball</p>
                </div>
            </div>
        </div>

        <!-- Live Casino -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <a href="#casinoLink">
                    <img src="{{ asset('images/ad_intro_sports.jpg') }}" class="card-img-top img-fluid" alt="Live Casino">
                </a>
                <div class="card-body">
                    <h5 class="card-title">LIVE CASINO</h5>
                    <p class="card-text">Welcome to Shanghai Resort</p>
                </div>
            </div>
        </div>

        <!-- Games -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <a href="#gamesLink">
                    <img src="{{ asset('images/ad_intro_sports.jpg') }}" class="card-img-top img-fluid" alt="Games">
                </a>
                <div class="card-body">
                    <h5 class="card-title">Games</h5>
                    <p class="card-text">Keno & Slot & Roulette</p>
                </div>
            </div>
        </div>

        <!-- Binary Options -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card">
                <a href="#binaryOptionsLink">
                    <img src="{{ asset('images/ad_intro_sports.jpg') }}" class="card-img-top img-fluid" alt="Binary Options">
                </a>
                <div class="card-body">
                    <h5 class="card-title">BINARY OPTIONS</h5>
                    <p class="card-text">Trading and Investments</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
