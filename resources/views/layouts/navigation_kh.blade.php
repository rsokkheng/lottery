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

    // Determine KH sub-currency from session (set by middleware when entering /lotto_kh_* routes)
    $khCurrency = session('currency', $navU->currency ?? 'VND');
    $khPrefix = $khCurrency === 'USD' ? 'bet-kh-usd' : 'bet-kh-vnd';

    // Color theme for Bet Khmer — red for VND, green for USD
    if ($khCurrency === 'USD') {
        $navBg       = 'linear-gradient(135deg,#14532d 0%,#16a34a 100%)';
        $navBgDarker = '#14532d';
        $hoverBg     = '#dcfce7';
        $hoverText   = '#15803d';
    } else {
        $navBg       = 'linear-gradient(135deg,#7f1d1d 0%,#dc2626 100%)';
        $navBgDarker = '#7f1d1d';
        $hoverBg     = '#fee2e2';
        $hoverText   = '#b91c1c';
    }

    $vnSwitchRoute = $khCurrency === 'USD' ? route('bet-usd.input') : route('bet.input');
@endphp

@include('layouts._nav_shared', compact(
    'navU','navRoles','navRole','isMember','isAdmin','isMaster','isAgent','isSupervisor','initials',
    'navBg','navBgDarker','hoverBg','hoverText'
) + [
    'currencyLabel' => 'Lotto Cambodia · ' . ($khCurrency === 'VND' ? 'Vietnamese Dong' : 'USD Dollar'),
    'switchRoute'   => $vnSwitchRoute,
    'switchLabel'   => '🇻🇳 VN',
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
