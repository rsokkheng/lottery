<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Happy Lottery 888</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #000;
            color: white;
        }
        .header-center {
            width: 1200px;
             height: auto;
            margin: auto;
        }
        .carousel-inner{
            width: 1200px;
             height: 335px;
            margin: auto;
        }
        .container {
            margin-top: 20px;
            width: 1200px;
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

<div class="header-center">
<div class="d-flex justify-content-between align-items-center p-3">
    <!-- Left: Logo -->
    <div class="logo">
        <h2 class="text-warning">HAPPY SPORT 888</h2>
        <p class="text-white">The more you play, the more you win</p>
    </div>

    <!-- Right: Login Form -->
    <div class="login-form">
        <form action="{{ route('login') }}" method="POST" class="d-flex">
        @csrf
            <input  id="email" class="form-control me-2" type="email" placeholder="username" name="email" :value="old('email')"
                            required autofocus autocomplete="username">
            <input id="password" class="form-control me-2" type="password" name="password" required
                            autocomplete="current-password" placeholder="Password">
            <button class="btn btn-warning">Submit</button>
        </form>
    </div>
</div>
</div>


   <!-- Carousel (Slider) -->
<div id="carouselExample" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="3000"> <!-- 3 seconds -->
            <img src="{{ asset('images/ads1.png') }}" width="1200" height="400" class="d-block w-100" alt="Slide 1">
        </div>
        <div class="carousel-item" data-bs-interval="3000">
            <img src="{{ asset('images/ads2.png') }}" width="1200" height="400" class="d-block w-100" alt="Slide 2">
        </div>
        <div class="carousel-item" data-bs-interval="3000">
            <img src="{{ asset('images/ads3.png') }}" width="1200" height="400" class="d-block w-100" alt="Slide 3">
        </div>
        <div class="carousel-item" data-bs-interval="3000">
            <img src="{{ asset('images/ads4.png') }}" width="1200" height="400" class="d-block w-100" alt="Slide 4">
        </div>
    </div>
  
</div>

   <!-- Main Content -->
        <div class="container">
            <div class="row">
                <!-- Lotto -->
                <div class="col-md-4">
                    <div class="card">
                        <a href="#lottoLink">
                            <img src="{{ asset('images/ad_intro_lotto.jpg') }}" width="400" height="200" class="card-img-top" alt="Lotto" data-bs-toggle="popover" title="Lotto" data-bs-content="Lotto Vietnam & Lotto Cambodia">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">LOTTO</h5>
                            <p class="card-text">Lotto Vietnam & Lotto Cambodia</p>
                        </div>
                    </div>
                </div>

                <!-- Sports -->
                <div class="col-md-4">
                    <div class="card">
                        <a href="#sportsLink">
                            <img src="{{ asset('images/ad_intro_sports.jpg') }}" width="400" height="200" class="card-img-top" alt="Sports" data-bs-toggle="popover" title="Sports" data-bs-content="Enjoy Football & Basketball">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">SPORTS</h5>
                            <p class="card-text">Enjoy Football & Basketball</p>
                        </div>
                    </div>
                </div>

                <!-- Live Casino -->
                <div class="col-md-4">
                    <div class="card">
                        <a href="#casinoLink">
                            <img src="{{ asset('images/ad_intro_sports.jpg') }}" width="400" height="200" class="card-img-top" alt="Live Casino" data-bs-toggle="popover" title="Live Casino" data-bs-content="Welcome to Shanghai Resort">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">LIVE CASINO</h5>
                            <p class="card-text">Welcome to Shanghai Resort</p>
                        </div>
                    </div>
                </div>

                <!-- Games -->
                <div class="col-md-4 mt-3">
                    <div class="card">
                        <a href="#gamesLink">
                            <img src="{{ asset('images/ad_intro_sports.jpg') }}" width="400" height="200" class="card-img-top" alt="Games" data-bs-toggle="popover" title="Games" data-bs-content="Keno & Slot & Roulette">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">Games</h5>
                            <p class="card-text">Keno & Slot & Roulette</p>
                        </div>
                    </div>
                </div>

                <!-- Binary Options -->
                <div class="col-md-4 mt-3">
                    <div class="card">
                        <a href="#binaryOptionsLink">
                            <img src="{{ asset('images/ad_intro_sports.jpg') }}" width="400" height="200" class="card-img-top" alt="Binary Options" data-bs-toggle="popover" title="Binary Options" data-bs-content="Trading and Investments">
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">BINARY OPTIONS</h5>
                            <p class="card-text">Trading and Investments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    <!-- Footer -->
    <div class="footer">
        © 2017-2018 happylottery888.com
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    
</script>
</body>
</html>
