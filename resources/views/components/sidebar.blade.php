@php
    $user          = auth()->user();
    $isSupervisor  = $user->hasAnyRole(['admin', 'master', 'agent']);
    $hasVND        = $isSupervisor || ($user->bet_system === 'vietnam' && $user->currency === 'VND');
    $hasUSD        = $isSupervisor || ($user->bet_system === 'vietnam' && $user->currency === 'USD');
    $hasKhmer      = $isSupervisor || $user->bet_system === 'khmer';
    $hasKhmerVND   = $isSupervisor || ($user->bet_system === 'khmer' && $user->currency === 'VND');
    $hasKhmerUSD   = $isSupervisor || ($user->bet_system === 'khmer' && $user->currency === 'USD');
    // Legacy alias so existing sidebar blade references still work
    $hasKHR        = $hasKhmer;
    $isAdmin      = $user->hasRole('admin');
    $isMaster     = $user->hasRole('master');
    $isAgent      = $user->hasRole('agent');
    $isSupervisor = $isAdmin || $isMaster || $isAgent;

    $vndOpen = request()->is('admin/credit/VND*') || request()->is('lotto_vn/*');
    $usdOpen = request()->is('admin/credit/USD*') || request()->is('lotto_usd/*');
    $khrOpen = Route::is('admin.result-kh.*') || Route::is('admin.credit-kh.*')
               || Route::is('bet-kh-vnd.*') || Route::is('bet-kh-usd.*')
               || request()->is('lotto_kh_vnd/*') || request()->is('lotto_kh_usd/*');
@endphp

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        {{-- ── General ── --}}
        <span class="sidebar-section-label">General</span>

        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}"
               class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        @if($isSupervisor)
        <li class="nav-item">
            <a href="{{ route('admin.user.index') }}"
               class="nav-link {{ Route::is('admin.user.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Account Management
                    @isset($userCount)
                        <span class="badge badge-info right">{{ $userCount }}</span>
                    @endisset
                </p>
            </a>
        </li>
        @endif

        @if($isAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.menu.index') }}"
               class="nav-link {{ Route::is('admin.menu.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-home"></i>
                <p>Menus</p>
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('admin.bet-lottery-package.index') }}"
               class="nav-link {{ Route::is('admin.bet-lottery-package.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-box-open"></i>
                <p>Lottery Packages</p>
            </a>
        </li>
        @endif


        {{-- ── Bet Vietnam ── --}}
        @if($hasVND || $hasUSD)
        <span class="sidebar-section-label">Bet Vietnam</span>

        <li class="nav-item has-treeview {{ $vndOpen || $usdOpen ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $vndOpen || $usdOpen ? 'active' : '' }}">
                <i class="nav-icon fas fa-flag"></i>
                <p>Bet Vietnam <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">

                {{-- Bet Vietnam Result (shared for VND & USD) --}}
                @if($isSupervisor)
                <li class="nav-item">
                    <a href="{{ route('admin.result.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result.index-mien-*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Vietnam Result</p>
                    </a>
                </li>
                @endif

                {{-- Vietnamese Dong sub-section --}}
                @if($hasVND)
                <li class="nav-item has-treeview nav-vnd {{ $vndOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $vndOpen ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Vietnamese Dong <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview nav-treeview-vnd">
                        @if($isSupervisor)
                        <li class="nav-item">
                            <a href="{{ route('admin.credit-fiat.index', 'VND') }}"
                               class="nav-link {{ request()->is('admin/credit/VND*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Credit Management</p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ url('lotto_vn/receipt-list') }}"
                               class="nav-link {{ request()->is('lotto_vn/receipt-list') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Receipt List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('lotto_vn/bet-list') }}"
                               class="nav-link {{ request()->is('lotto_vn/bet-list') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Bet List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- USD Dollar sub-section --}}
                @if($hasUSD)
                <li class="nav-item has-treeview nav-usd {{ $usdOpen ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ $usdOpen ? 'active' : '' }}">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>USD Dollar <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview nav-treeview-usd">
                        @if($isSupervisor)
                        <li class="nav-item">
                            <a href="{{ route('admin.credit-fiat.index', 'USD') }}"
                               class="nav-link {{ request()->is('admin/credit/USD*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Credit Management</p>
                            </a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ url('lotto_usd/receipt-list') }}"
                               class="nav-link {{ request()->is('lotto_usd/receipt-list') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Receipt List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('lotto_usd/bet-list') }}"
                               class="nav-link {{ request()->is('lotto_usd/bet-list') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Bet List</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

            </ul>
        </li>
        @endif

        {{-- ── Bet Khmer ── --}}
        @if($hasKHR)
        <span class="sidebar-section-label">Bet Khmer</span>

        <li class="nav-item has-treeview {{ $khrOpen ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $khrOpen ? 'active' : '' }}">
                <i class="nav-icon fas fa-coins"></i>
                <p>Bet Khmer <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">

                @if($isSupervisor)
                {{-- Admin links shared across both KH currencies --}}
                <li class="nav-item">
                    <a href="{{ route('admin.result-kh.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Khmer Result</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.credit-kh.index') }}"
                       class="nav-link {{ Route::is('admin.credit-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Credit Management</p>
                    </a>
                </li>
                @endif

                {{-- Vietnamese Dong sub-section --}}
                @if($hasKhmerVND)
                <li class="nav-item has-treeview nav-khr {{ request()->is('lotto_kh_vnd/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('lotto_kh_vnd/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Vietnamese Dong <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('bet-kh-vnd.receipt-list') }}"
                               class="nav-link {{ Route::is('bet-kh-vnd.receipt-list') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Receipt List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('bet-kh-vnd.bet-list') }}"
                               class="nav-link {{ Route::is('bet-kh-vnd.bet-list') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Bet List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('bet-kh-vnd.bet-winning') }}"
                               class="nav-link {{ Route::is('bet-kh-vnd.bet-winning') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Win Report</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                {{-- USD Dollar sub-section --}}
                @if($hasKhmerUSD)
                <li class="nav-item has-treeview nav-khr {{ request()->is('lotto_kh_usd/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('lotto_kh_usd/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-dollar-sign"></i>
                        <p>USD Dollar <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('bet-kh-usd.receipt-list') }}"
                               class="nav-link {{ Route::is('bet-kh-usd.receipt-list') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Receipt List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('bet-kh-usd.bet-list') }}"
                               class="nav-link {{ Route::is('bet-kh-usd.bet-list') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Bet List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('bet-kh-usd.bet-winning') }}"
                               class="nav-link {{ Route::is('bet-kh-usd.bet-winning') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Win Report</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

            </ul>
        </li>
        @endif

        {{-- ── Account ── --}}
        <span class="sidebar-section-label">Account</span>

        <li class="nav-item">
            <a href="{{ route('admin.profile.edit') }}"
               class="nav-link {{ Route::is('admin.profile.edit') ? 'active' : '' }}">
                <i class="nav-icon fas fa-id-card"></i>
                <p>My Profile</p>
            </a>
        </li>

    </ul>
</nav>
