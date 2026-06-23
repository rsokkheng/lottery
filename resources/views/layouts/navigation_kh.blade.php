@php
    $navU     = Auth::user();
    $navRoles = $navU->roles->pluck('name');
    $navRole  = $navU->roles->first()?->name ?? 'user';
    $isMember   = $navRoles->contains('member');
    $isAdmin    = $navRoles->contains('admin');
    $isMaster     = $navRoles->contains('master');
    $isAgent      = $navRoles->contains('agent');
    $isSupervisor = $navRoles->intersect(['admin', 'master', 'agent'])->isNotEmpty();
    $initials   = strtoupper(substr($navU->name, 0, 1));

    // Determine Khmer sub-currency (VND or USD) from session or user record
    $khCurrency = session('currency') && session('bet_system') === 'khmer'
        ? session('currency')
        : ($navU->bet_system === 'khmer' ? $navU->currency : 'VND');
    $khPrefix = $khCurrency === 'USD' ? 'bet-kh-usd' : 'bet-kh-vnd';

    // Color theme for Bet Khmer — deep amber / gold
    $navBg       = 'linear-gradient(135deg,#7d5a00 0%,#a17100 100%)';
    $navBgDarker = '#6b4c00';
    $hoverBg     = '#fff8e1';
    $hoverText   = '#7d5a00';
@endphp

@include('layouts._nav_shared', compact(
    'navU','navRoles','navRole','isMember','isAdmin','isMaster','isAgent','isSupervisor','initials',
    'navBg','navBgDarker','hoverBg','hoverText'
) + [
    'currencyLabel' => 'Bet Khmer · ' . ($khCurrency === 'VND' ? 'Vietnamese Dong' : 'USD Dollar'),
    'homeRoute'     => $isMember ? route($khPrefix . '.input') : route($khPrefix . '.receipt-list'),
    'routes' => [
        'bet'         => $khPrefix . '.input',
        'receiptList' => $khPrefix . '.receipt-list',
        'betList'     => $khPrefix . '.bet-list',
        'betNumber'   => $khPrefix . '.bet-number',
        'betWinning'  => $khPrefix . '.bet-winning',
        'resultShow'  => $khPrefix . '.result-show',
        'dailyMgr'    => $khPrefix . '.reports.daily-manager',
        'monthly'     => $khPrefix . '.reports.monthly-tracking',
        'dailyAll'    => $khPrefix . '.reports.daily',
        'monthlyAll'  => $khPrefix . '.reports.monthly-allmember',
        'summary'     => $khPrefix . '.reports.summary',
        'reportWild'  => $khPrefix . '.reports.*',
    ],
])
