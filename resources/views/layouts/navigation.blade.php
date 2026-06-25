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

    // Color theme for VND — teal-blue
    $navBg      = 'linear-gradient(135deg,#1565c0 0%,#1976d2 100%)';
    $navBgDarker = '#0d47a1';
    $hoverBg    = '#e8f0fe';
    $hoverText  = '#1565c0';
@endphp

@include('layouts._nav_shared', compact(
    'navU','navRoles','navRole','isMember','isAdmin','isMaster','isAgent','isSupervisor','initials',
    'navBg','navBgDarker','hoverBg','hoverText'
) + [
    'currencyLabel' => 'Lotto Vietnam · Vietnamese Dong',
    'switchRoute'   => route('bet-kh-vnd.input'),
    'switchLabel'   => '🇰🇭 KH',
    'homeRoute'     => $isMember ? route('bet.input') : route('bet.receipt-list'),
    'routes' => [
        'bet'         => 'bet.input',
        'receiptList' => 'bet.receipt-list',
        'betList'     => 'bet.bet-list',
        'betNumber'   => 'bet.bet-number',
        'betWinning'  => 'bet.bet-winning',
        'resultShow'  => 'bet.result-show',
        'dailyMgr'    => 'reports.daily-manager',
        'monthly'     => 'reports.monthly-tracking',
        'dailyAll'    => 'reports.daily',
        'monthlyAll'  => 'reports.monthly-allmember',
        'summary'     => 'reports.summary',
        'reportWild'  => 'reports.*',
    ],
])
