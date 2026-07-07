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

    // Betting access flags — currency-based: VND gives Vietnam + Khmer VND; USD gives Vietnam + Khmer USD
    $hasVND      = $isAdmin || $user->currency === 'VND';
    $hasUSD      = $isAdmin || $user->currency === 'USD';
    $hasKhmerVND = $isAdmin || $user->currency === 'VND';
    $hasKhmerUSD = $isAdmin || $user->currency === 'USD';
    $hasKhmer    = $hasKhmerVND || $hasKhmerUSD;
    $hasKHR      = $hasKhmer;

    $vndOpen = request()->is('lotto_vn/*');
    $usdOpen = request()->is('lotto_usd/*');
    $khrOpen = Route::is('bet-kh-vnd.*') || Route::is('bet-kh-usd.*')
               || request()->is('lotto_kh_vnd/*') || request()->is('lotto_kh_usd/*');
@endphp

<style>
/* Sidebar active state — clean dark-blue */
.nav-sidebar .nav-link.active,
.nav-sidebar .nav-treeview .nav-link.active {
    background: linear-gradient(90deg,#1e3a5f,#2563a8) !important;
    color: #fff !important;
    border-radius: 8px !important;
    box-shadow: 0 2px 8px rgba(37,99,168,.35);
}
.nav-sidebar .nav-link.active .nav-icon,
.nav-sidebar .nav-treeview .nav-link.active .nav-icon,
.nav-sidebar .nav-link.active p,
.nav-sidebar .nav-treeview .nav-link.active p {
    color: #fff !important;
}
/* Sidebar hover */
.nav-sidebar .nav-link:not(.active):hover {
    background: rgba(37,99,168,.08) !important;
    border-radius: 8px !important;
}
/* Section labels */
.sidebar-section-label {
    display: block;
    font-size: .62rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: rgba(255,255,255,.35);
    padding: 12px 12px 4px;
    margin-top: 2px;
}
/* Smoother sidebar links */
.nav-sidebar .nav-link {
    border-radius: 8px !important;
    margin: 1px 8px !important;
    transition: background .15s, box-shadow .15s !important;
    font-size: .82rem !important;
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

        {{-- ── Credit Management (supervisors: filtered by currency) ── --}}
        @if($isSupervisor)
        @if($hasVND)
        <li class="nav-item">
            <a href="{{ route('admin.credit-fiat.index', 'VND') }}"
               class="nav-link {{ request()->is('admin/credit/VND*') || (Route::is('admin.credit-kh.*') && request()->query('currency','vnd') === 'vnd') ? 'active' : '' }}">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Credit VND</p>
            </a>
        </li>
        @endif
        @if($hasUSD)
        <li class="nav-item">
            <a href="{{ route('admin.credit-fiat.index', 'USD') }}"
               class="nav-link {{ request()->is('admin/credit/USD*') || (Route::is('admin.credit-kh.*') && request()->query('currency') === 'usd') ? 'active' : '' }}">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Credit USD</p>
            </a>
        </li>
        @endif
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
                        <i class="far fa-circle nav-icon"></i><p>Lotto Vietnam Result</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.result-kh.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Lotto Cambodia Result</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- ── Bet Vietnam ── --}}
        @if($hasVND || $hasUSD)
        <span class="sidebar-section-label">Lotto Vietnam</span>

        <li class="nav-item has-treeview {{ $vndOpen || $usdOpen ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $vndOpen || $usdOpen ? 'active' : '' }}">
                <i class="nav-icon fas fa-flag"></i>
                <p>Lotto Vietnam <i class="fas fa-angle-left right"></i></p>
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
        <span class="sidebar-section-label">Lotto Cambodia</span>

        <li class="nav-item has-treeview {{ $khrOpen ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ $khrOpen ? 'active' : '' }}">
                <i class="nav-icon fas fa-coins"></i>
                <p>Lotto Cambodia <i class="fas fa-angle-left right"></i></p>
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
                        <i class="far fa-circle nav-icon"></i><p>Lotto Vietnam Result</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.result-kh.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Lotto Cambodia Result</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- ── Finance: Credit & Transactions ── --}}
        @if($isFinance)
        @php
            $finCur     = $user->currency;
            $finShowVND = !$finCur || $finCur === 'VND';
            $finShowUSD = !$finCur || $finCur === 'USD';
        @endphp
        <span class="sidebar-section-label">Finance</span>

        @if($finShowVND)
        <li class="nav-item">
            <a href="{{ route('admin.credit-fiat.index', 'VND') }}"
               class="nav-link {{ request()->is('admin/credit/VND*') || (Route::is('admin.credit-kh.*') && request()->query('currency','vnd') === 'vnd') ? 'active' : '' }}">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Credit VND</p>
            </a>
        </li>
        @endif
        @if($finShowUSD)
        <li class="nav-item">
            <a href="{{ route('admin.credit-fiat.index', 'USD') }}"
               class="nav-link {{ request()->is('admin/credit/USD*') || (Route::is('admin.credit-kh.*') && request()->query('currency') === 'usd') ? 'active' : '' }}">
                <i class="nav-icon fas fa-wallet"></i>
                <p>Credit USD</p>
            </a>
        </li>
        @endif
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
                        <i class="far fa-circle nav-icon"></i><p>Lotto Vietnam Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.report.daily-usd') }}"
                       class="nav-link {{ Route::is('admin.report.daily-usd') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Lotto Vietnam USD Report</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.result.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result.index-mien-*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Lotto Vietnam Result History</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.result-kh.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Lotto Cambodia Result History</p>
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
