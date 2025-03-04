@section('title')
    {{ 'Log in' }}
@endsection
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
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container" style="max-width: 1300px;"> <!-- Restrict navbar width -->
        <a class="navbar-brand text-warning" href="#">Happy Lottery 2888</a>
        <div class=" navbar-collapse justify-content-end" id="navbarNav">
            <form action="{{ route('login') }}" method="POST" class="d-flex flex-column flex-lg-row align-items-lg-center">
                @csrf
                <div class="d-flex flex-column me-2">
                    <input id="username" class="form-control mb-2" type="username" placeholder="Username" name="username" required>
                    <input id="password" class="form-control mb-2" type="password" name="password" required placeholder="Password">
                    <div class="form-check"> <!-- Checkbox now properly placed below password -->
                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                        <label class="form-check-label text-white" for="remember">Remember Me</label>
                    </div>
                </div>
                <button class="btn btn-warning">Submit</button>
            </form>
        </div>
    </div>
</nav>


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
                <a href="#lottoLink">
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
