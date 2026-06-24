@php
    $user          = auth()->user();
    $isAdmin       = $user->hasRole('admin');
    $isMaster      = $user->hasRole('master');
    $isAgent       = $user->hasRole('agent');
    $isSupervisor  = $isAdmin || $isMaster || $isAgent;

    // Back-office roles
    $isOperator    = $user->hasRole('operator');
    $isFinance     = $user->hasRole('finance');
    $isSupport     = $user->hasRole('support');
    $isAuditor     = $user->hasRole('auditor');
    $isBackOffice  = $isOperator || $isFinance || $isSupport || $isAuditor;

    $canEnterResult = $user->can('data entry bet result');

    // Betting access flags — only admin sees all systems; master/agent see only their own
    $hasVND      = $isAdmin || ($user->bet_system === 'vietnam' && $user->currency === 'VND');
    $hasUSD      = $isAdmin || ($user->bet_system === 'vietnam' && $user->currency === 'USD');
    $hasKhmerVND = $isAdmin || ($user->bet_system === 'khmer' && $user->currency === 'VND');
    $hasKhmerUSD = $isAdmin || ($user->bet_system === 'khmer' && $user->currency === 'USD');
    $hasKhmer    = $hasKhmerVND || $hasKhmerUSD;
    $hasKHR      = $hasKhmer;

    $vndOpen = request()->is('admin/credit/VND*') || request()->is('lotto_vn/*');
    $usdOpen = request()->is('admin/credit/USD*') || request()->is('lotto_usd/*');
    $khrOpen = Route::is('admin.credit-kh.*')
               || Route::is('bet-kh-vnd.*') || Route::is('bet-kh-usd.*')
               || request()->is('lotto_kh_vnd/*') || request()->is('lotto_kh_usd/*');
@endphp

<style>
.nav-sidebar .nav-link.active,
.nav-sidebar .nav-treeview .nav-link.active {
    background-color: #007bff !important;
    color: #fff !important;
}
.nav-sidebar .nav-link.active .nav-icon,
.nav-sidebar .nav-treeview .nav-link.active .nav-icon,
.nav-sidebar .nav-link.active p,
.nav-sidebar .nav-treeview .nav-link.active p {
    color: #fff !important;
}
</style>

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

        @if($isSupervisor || $isSupport)
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


        {{-- ── Bet Result (Vietnam + Khmer) ── --}}
        @if($canEnterResult)
        <span class="sidebar-section-label">Bet Result</span>
        <li class="nav-item has-treeview {{ Route::is('admin.result.*') || Route::is('admin.result-kh.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('admin.result.*') || Route::is('admin.result-kh.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-clipboard-list"></i>
                <p>Bet Result <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.result.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result.index-mien-*') || Route::is('admin.result.create-mien-*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Vietnam Result</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.result-kh.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Khmer Result</p>
                    </a>
                </li>
            </ul>
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

                {{-- Vietnamese Dong sub-section --}}
                @if($hasKhmerVND)
                <li class="nav-item has-treeview nav-khr {{ request()->is('lotto_kh_vnd/*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->is('lotto_kh_vnd/*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Vietnamese Dong <i class="fas fa-angle-left right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        @if($isSupervisor)
                        <li class="nav-item">
                            <a href="{{ route('admin.credit-kh.index') }}"
                               class="nav-link {{ Route::is('admin.credit-kh.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Credit Management</p>
                            </a>
                        </li>
                        @endif
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
                        @if($isSupervisor)
                        <li class="nav-item">
                            <a href="{{ route('admin.credit-kh.index') }}"
                               class="nav-link {{ Route::is('admin.credit-kh.*') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i><p>Credit Management</p>
                            </a>
                        </li>
                        @endif
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

        {{-- ── Operator: Lottery Operations ── --}}
        @if($isOperator)
        <span class="sidebar-section-label">Lottery Operations</span>

        <li class="nav-item has-treeview {{ Route::is('admin.result.*') || Route::is('admin.result-kh.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('admin.result.*') || Route::is('admin.result-kh.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-trophy"></i>
                <p>Lottery Results <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.result.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result.index-mien-*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Vietnam Result</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.result-kh.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Khmer Result</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- ── Finance: Credit & Transactions ── --}}
        @if($isFinance)
        <span class="sidebar-section-label">Finance</span>

        <li class="nav-item has-treeview {{ Route::is('admin.credit-fiat.*') || Route::is('admin.credit-kh.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('admin.credit-fiat.*') || Route::is('admin.credit-kh.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Credit Management <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.credit-fiat.index', 'VND') }}"
                       class="nav-link {{ request()->is('admin/credit/VND*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Credit VND</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.credit-fiat.index', 'USD') }}"
                       class="nav-link {{ request()->is('admin/credit/USD*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Credit USD</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.credit-kh.index') }}"
                       class="nav-link {{ Route::is('admin.credit-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Credit Khmer</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- ── Support: Account & Bet Lookup ── --}}
        @if($isSupport)
        <span class="sidebar-section-label">Support Tools</span>

        <li class="nav-item">
            <a href="{{ route('admin.user.index') }}"
               class="nav-link {{ Route::is('admin.user.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-search"></i>
                <p>Account Lookup</p>
            </a>
        </li>
        @endif

        {{-- ── Auditor: Reports ── --}}
        @if($isAuditor)
        <span class="sidebar-section-label">Reports</span>

        <li class="nav-item has-treeview {{ Route::is('admin.report.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('admin.report.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-chart-bar"></i>
                <p>Bet Reports <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item">
                    <a href="{{ route('admin.report.index') }}"
                       class="nav-link {{ Route::is('admin.report.index') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Vietnam Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.report.daily-usd') }}"
                       class="nav-link {{ Route::is('admin.report.daily-usd') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Vietnam USD Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.result.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result.index-mien-*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Vietnam Result History</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.result-kh.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Bet Khmer Result History</p>
                    </a>
                </li>
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
