<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Lottery2888') }}</title>
    <link rel="icon" href="{{ asset('images/snooker.png') }}" type="image/png">
    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<div class="min-h-screen bg-gray-100">
    <div class="w-full">

    @auth
        @php
            $user = auth()->user();
            $hasVND = $user->currencies()->where('currency', 'VND')->exists();
            $hasUSD = $user->currencies()->where('currency', 'USD')->exists();
        @endphp

        @if($hasVND && !$hasUSD)
            @include('layouts.navigation') {{-- Only VND --}}
        @elseif($hasUSD && !$hasVND)
            @include('layouts.navigation_usd') {{-- Only USD --}}
        @elseif($hasVND && $hasUSD)
            {{-- Default to one, or let user choose via session --}}
            @include('layouts.nonavigation') {{-- Default to VND --}}
        @endif
    @endauth


    </div>
    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    <!-- Page Content -->
    <main>
        {{ $slot }}
    </main>
</div>
@livewireScripts

</body>
</html>
