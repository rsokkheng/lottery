<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Lottery2888') }}</title>
    <link rel="icon" href="{{ asset('images/snooker.png') }}" type="image/png">

    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', 'Source Sans Pro', sans-serif;
            background: #f0f2f5;
            color: #1f2937;
            min-height: 100vh;
            overflow-x: hidden; /* nav scrolls internally; page never scrolls horizontally */
            width: 100%;
        }
        .page-main {
            padding: .75rem .75rem 2rem;
            width: 100%;
        }
        /* ── Card shell around content ── */
        .page-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,.07);
            overflow: hidden;
        }
        /* ── Shared table styles ── */
        .bn-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: .82rem;
        }
        .bn-table thead th {
            background: #f8f9fc;
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .6px;
            color: #6c757d;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 9px 10px;
            white-space: nowrap;
            text-align: center;
        }
        .bn-table tbody td {
            padding: 7px 10px;
            border-bottom: 1px solid #f0f2f5;
            vertical-align: middle;
            white-space: nowrap;
        }
        .bn-table tbody tr:hover { background: #f8f9ff; }
        .bn-table tbody tr.win-row { background: #fff0f0; }
        .bn-table tbody tr.win-row:hover { background: #ffe5e5; }
        .bn-table tfoot td {
            font-weight: 700;
            background: #f8f9fc;
            border-top: 2px solid #e2e8f0;
            padding: 8px 10px;
        }
        /* ── Filter bar ── */
        .bn-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            padding: .75rem 1rem;
            background: #fff;
            border-bottom: 1px solid #f0f2f5;
            align-items: center;
        }
        .bn-filter-bar input,
        .bn-filter-bar select {
            border: 1px solid #d1d8e0;
            border-radius: 7px;
            padding: 5px 10px;
            font-size: .82rem;
            color: #374151;
            height: 34px;
            outline: none;
            transition: border-color .15s;
        }
        .bn-filter-bar input:focus,
        .bn-filter-bar select:focus { border-color: #80b3ff; box-shadow: 0 0 0 2px rgba(78,115,223,.15); }
        .bn-btn {
            height: 34px;
            padding: 0 14px;
            border-radius: 7px;
            font-size: .8rem;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .bn-btn-primary { background: #4e73df; color: #fff; }
        .bn-btn-primary:hover { background: #3a5bbf; transform: translateY(-1px); }
        .bn-btn-success { background: #28a745; color: #fff; }
        .bn-btn-success:hover { background: #1e7e34; }
        .bn-btn-info { background: #17a2b8; color: #fff; }
        .bn-btn-info:hover { background: #0f7d8e; }
        .bn-btn-warning { background: #ffc107; color: #212529; }
        .bn-btn-warning:hover { background: #e0a800; }
        .bn-btn-danger { background: #dc3545; color: #fff; }
        /* ── Badges ── */
        .bn-badge-vnd { background:#17a2b8; color:#fff; border-radius:5px; padding:2px 8px; font-size:.68rem; font-weight:700; }
        .bn-badge-usd { background:#28a745; color:#fff; border-radius:5px; padding:2px 8px; font-size:.68rem; font-weight:700; }
        .bn-badge-khr { background:#d4a017; color:#fff; border-radius:5px; padding:2px 8px; font-size:.68rem; font-weight:700; }
        /* ── Page title bar ── */
        .bn-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .7rem 1rem;
            border-bottom: 1px solid #f0f2f5;
        }
        .bn-page-title {
            font-size: 1rem;
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }
        /* ── Scrollable table wrapper ── */
        .bn-table-wrap {
            overflow-x: auto;
            padding: 0 0 .5rem;
        }
        /* ── Responsive: stack filter bar on small screens ── */
        @media (max-width: 600px) {
            .page-main { padding: .5rem .5rem 2rem; }
            .bn-filter-bar { gap: .35rem; }
            .bn-filter-bar input,
            .bn-filter-bar select { font-size: .78rem; height: 32px; }
        }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>

<body>
    @auth
        @php
            $user = auth()->user();
            $isSupervisor  = $user->hasAnyRole(['admin', 'master', 'agent']);
            $hasVietnamVND = $isSupervisor || ($user->bet_system === 'vietnam' && $user->currency === 'VND');
            $hasVietnamUSD = $isSupervisor || ($user->bet_system === 'vietnam' && $user->currency === 'USD');
            $hasKhmer      = $isSupervisor || $user->bet_system === 'khmer';
            // For admin/master: rely on session to pick the active navigation
            $sessionBetSystem = session('bet_system');
            $sessionCurrency  = session('currency');
        @endphp

        @if($sessionBetSystem === 'khmer' || ($hasKhmer && !$hasVietnamVND && !$hasVietnamUSD))
            @include('layouts.navigation_kh')
        @elseif($sessionCurrency === 'USD' || ($hasVietnamUSD && !$hasVietnamVND && !$hasKhmer))
            @include('layouts.navigation_usd')
        @elseif($sessionCurrency === 'VND' || ($hasVietnamVND && !$hasVietnamUSD && !$hasKhmer))
            @include('layouts.navigation')
        @else
            @include('layouts.nonavigation')
        @endif
    @endauth

    @isset($header)
        <header style="background:#fff; border-bottom:1px solid #e2e8f0; padding:.6rem 1rem;">
            <div style="font-size:.9rem; font-weight:700; color:#2d3748;">{{ $header }}</div>
        </header>
    @endisset

    <main class="page-main">
        {{ $slot }}
    </main>

    @livewireScripts
    @yield('js')
</body>
</html>
