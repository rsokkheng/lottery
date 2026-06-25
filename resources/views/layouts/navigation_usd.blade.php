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

    // Color theme for USD — deep green
    $navBg       = 'linear-gradient(135deg,#1b5e20 0%,#2e7d32 100%)';
    $navBgDarker = '#1a3d20';
    $hoverBg     = '#e8f5e9';
    $hoverText   = '#1b5e20';
@endphp

@include('layouts._nav_shared', compact(
    'navU','navRoles','navRole','isMember','isAdmin','isMaster','isAgent','isSupervisor','initials',
    'navBg','navBgDarker','hoverBg','hoverText'
) + [
    'currencyLabel' => 'Lotto Vietnam · USD Dollar',
    'switchRoute'   => route('bet-kh-usd.input'),
    'switchLabel'   => '🇰🇭 KH',
    'homeRoute'     => $isMember ? route('bet-usd.input') : route('bet-usd.receipt-list'),
    'routes' => [
        'bet'         => 'bet-usd.input',
        'receiptList' => 'bet-usd.receipt-list',
        'betList'     => 'bet-usd.bet-list',
        'betNumber'   => 'bet-usd.bet-number',
        'betWinning'  => 'bet-usd.bet-winning',
        'resultShow'  => 'bet-usd.result-show',
        'dailyMgr'    => 'bet-usd.reports.daily-manager',
        'monthly'     => 'bet-usd.reports.monthly-tracking',
        'dailyAll'    => 'bet-usd.reports.daily',
        'monthlyAll'  => 'bet-usd.reports.monthly-allmember',
        'summary'     => 'bet-usd.reports.summary',
        'reportWild'  => 'bet-usd.reports.*',
    ],
])
