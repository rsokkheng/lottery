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

        /* ────────────────────────────────────────────────────
           Shared bet-page styles  (receipt-list, bet-list,
           bet-number, result, winning, reports)
        ──────────────────────────────────────────────────── */
        .bp-wrap { background:#fff; border-radius:14px; box-shadow:0 2px 14px rgba(0,0,0,.07); overflow:hidden; }

        /* Filter bar */
        .bp-filter { display:flex; flex-wrap:wrap; gap:.5rem; align-items:center;
                     padding:12px 16px; background:#f8f9fc; border-bottom:1px solid #e8ecf0; }
        .bp-filter input, .bp-filter select {
            border:1px solid #d1d8e0; border-radius:8px; padding:6px 10px 6px 32px;
            font-size:.83rem; color:#374151; height:36px; outline:none;
            background:#fff; transition:border-color .15s; }
        .bp-filter input:focus, .bp-filter select:focus { border-color:#3b82f6; box-shadow:0 0 0 2px rgba(59,130,246,.15); }
        .bp-filter select { padding-left:10px; }
        .bp-filter-icon { position:relative; display:flex; align-items:center; }
        .bp-filter-icon svg { position:absolute; left:9px; width:15px; height:15px; color:#9ca3af; pointer-events:none; }
        .bp-filter-icon input { padding-left:32px; }
        .bp-search-btn { display:inline-flex; align-items:center; gap:5px; background:#2563eb; color:#fff;
                         border:none; border-radius:8px; padding:0 16px; height:36px; font-size:.83rem;
                         font-weight:600; cursor:pointer; white-space:nowrap; transition:background .15s; }
        .bp-search-btn:hover { background:#1d4ed8; }

        /* Table */
        .bp-table-wrap { overflow-x:auto; }
        .bp-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.82rem; }
        .bp-table thead th {
            background:#4b5563;
            color:#f9fafb; font-size:.72rem; font-weight:700; text-transform:uppercase;
            letter-spacing:.5px; padding:10px 10px; white-space:nowrap;
            border-right:1px solid rgba(255,255,255,.12); border-bottom:none; }
        .bp-table thead th:first-child { border-radius:0; }
        .bp-table tbody td { padding:8px 10px; border-bottom:1px solid #f0f2f5;
                              border-right:1px solid #f0f2f5; vertical-align:middle; white-space:nowrap; }
        .bp-table tbody tr:last-child td { border-bottom:none; }
        .bp-table tbody tr:hover td { background:#f0f5ff; }
        .bp-table tbody tr.bp-win td { background:#fff0f0; color:#dc2626; }
        .bp-table tbody tr.bp-win:hover td { background:#ffe5e5; }
        .bp-table tfoot td, .bp-table tfoot th {
            background:#f8f9fc; font-weight:700; padding:9px 10px;
            border-top:2px solid #e2e8f0; font-size:.82rem; }
        .bp-table .bp-num { text-align:right; }
        .bp-table .bp-center { text-align:center; }

        /* System badge */
        .bp-badge { display:inline-flex; align-items:center; gap:6px; padding:4px 12px;
                    border-radius:20px; font-size:.75rem; font-weight:700; margin-bottom:10px; }
        .bp-badge-vn  { background:linear-gradient(135deg,#1e3a8a,#2563eb); color:#fff; }
        .bp-badge-usd { background:linear-gradient(135deg,#064e3b,#059669); color:#fff; }
        .bp-badge-kh  { background:linear-gradient(135deg,#7d5a00,#a17100); color:#fff; }

        /* Win/lose colour helpers */
        .bp-neg     { color:#dc2626; font-weight:700; }
        .bp-neg-t   { color:#dc2626; }
        .bp-row-win { background:#fef2f2; }

        /* Empty state */
        .bp-empty { text-align:center; padding:40px; color:#9ca3af; }
        .bp-empty svg { width:44px; height:44px; margin:0 auto 10px; opacity:.4; }
        .bp-empty p { font-size:.88rem; }

        /* Page header row */
        .bp-page-header { display:flex; align-items:center; justify-content:space-between;
                          padding:14px 16px; border-bottom:1px solid #e8ecf0; flex-wrap:wrap; gap:8px; }
        .bp-page-title { font-size:.95rem; font-weight:700; color:#1e293b; }
        .bp-page-sub { font-size:.72rem; color:#6c757d; }

        @media (max-width:640px) {
            .bp-filter { gap:.35rem; }
            .bp-filter input, .bp-filter select { font-size:.78rem; height:32px; }
            .bp-table thead th, .bp-table tbody td { font-size:.72rem; padding:6px 7px; }
        }
    </style>

    @livewireStyles
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('css')
</head>

<body>
    @auth
        @php
            $user             = auth()->user();
            $isAdmin          = $user->hasRole('admin');
            $sessionBetSystem = session('bet_system', $user->bet_system ?? 'vietnam');
            $sessionCurrency  = session('currency',   $user->currency);
            // Access is currency-based: VND gives Vietnam+Khmer VND, USD gives Vietnam+Khmer USD
            $hasVietnamVND = $isAdmin || $user->currency === 'VND';
            $hasVietnamUSD = $isAdmin || $user->currency === 'USD';
            $hasKhmer      = $isAdmin || in_array($user->currency, ['VND', 'USD']);
        @endphp

        @if($sessionBetSystem === 'khmer' || ($hasKhmer && !$hasVietnamVND && !$hasVietnamUSD))
            @include('layouts.navigation_kh')
        @elseif($sessionBetSystem !== 'khmer' && ($sessionCurrency === 'USD' || ($hasVietnamUSD && !$hasVietnamVND && !$hasKhmer)))
            @include('layouts.navigation_usd')
        @elseif($sessionBetSystem !== 'khmer' && ($sessionCurrency === 'VND' || ($hasVietnamVND && !$hasVietnamUSD && !$hasKhmer)))
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
