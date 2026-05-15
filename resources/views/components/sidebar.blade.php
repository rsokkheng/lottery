@php
    $user    = auth()->user();
    $hasVND  = $user->currencies()->where('currency', 'VND')->exists();
    $hasUSD  = $user->currencies()->where('currency', 'USD')->exists();
    $hasKHR  = $user->currencies()->where('currency', 'KHR')->exists();
    $isAdmin       = $user->hasRole('admin');
    $isMaster      = $user->hasRole('master');
    $isSenior      = $user->hasRole('senior');
    $isManager     = $user->hasRole('manager');
    $isShareMaster = $user->hasRole('share_master');
    $isSupervisor  = $isAdmin || $isMaster || $isSenior || $isManager || $isShareMaster;
@endphp
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        {{-- Dashboard --}}
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>

        {{-- Supervisor: Account Management --}}
        @if($isSupervisor)
        <li class="nav-item">
            <a href="{{ route('admin.user.index') }}"
               class="nav-link {{ Route::is('admin.user.index') || Route::is('admin.user.under-manager') ? 'active' : '' }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Account Management
                    <span class="badge badge-info right">{{ $userCount }}</span>
                </p>
            </a>
        </li>
        @endif

        {{-- Admin only --}}
        @if($isAdmin)
        <li class="nav-item">
            <a href="{{ route('admin.menu.index') }}" class="nav-link {{ Route::is('admin.menu.index') ? 'active' : '' }}">
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

        {{-- Balance Report: only for VND/USD users (not KHR-only) --}}
        @if($isSupervisor && ($hasVND || $hasUSD))
        <li class="nav-item">
            <a href="{{ route('admin.balance-report.index') }}"
               class="nav-link {{ Route::is('admin.balance-report.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-money-bill"></i>
                <p>Balance Report</p>
            </a>
        </li>
        @endif

        {{-- ── VND section ── --}}
        @if($hasVND)
        <li class="nav-item has-treeview
            {{ request()->is('admin/result/index-mien*') || request()->is('admin/credit/VND*') || Route::is('admin.account-report.index') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link
                {{ request()->is('admin/result/index-mien*') || request()->is('admin/credit/VND*') || Route::is('admin.account-report.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-money-bill-wave"></i>
                <p>VND <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @if($isSupervisor)
                <li class="nav-item">
                    <a href="{{ route('admin.result.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result.index-mien-*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Lottery Result</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.credit-fiat.index', 'VND') }}"
                       class="nav-link {{ request()->is('admin/credit/VND') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Credit Management</p>
                    </a>
                </li>
                @endif
                @if($isAdmin || $isMaster)
                <li class="nav-item">
                    <a href="{{ route('admin.account-report.index') }}"
                       class="nav-link {{ Route::is('admin.account-report.index') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Transaction Report</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        {{-- ── USD section ── --}}
        @if($hasUSD)
        <li class="nav-item has-treeview
            {{ request()->is('admin/credit/USD*') || Route::is('admin.account-report.transation-usd') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link
                {{ request()->is('admin/credit/USD*') || Route::is('admin.account-report.transation-usd') ? 'active' : '' }}">
                <i class="nav-icon fas fa-dollar-sign"></i>
                <p>USD <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @if($isSupervisor)
                <li class="nav-item">
                    <a href="{{ route('admin.credit-fiat.index', 'USD') }}"
                       class="nav-link {{ request()->is('admin/credit/USD') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Credit Management</p>
                    </a>
                </li>
                @endif
                @if($isAdmin || $isMaster)
                <li class="nav-item">
                    <a href="{{ route('admin.account-report.transation-usd') }}"
                       class="nav-link {{ Route::is('admin.account-report.transation-usd') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Transaction Report</p>
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        {{-- ── KHR section ── --}}
        @if($hasKHR)
        <li class="nav-item has-treeview {{ Route::is('admin.result-kh.*') || Route::is('admin.credit-kh.*') || Route::is('bet-kh.*') ? 'menu-open' : '' }}">
            <a href="#" class="nav-link {{ Route::is('admin.result-kh.*') || Route::is('admin.credit-kh.*') || Route::is('bet-kh.*') ? 'active' : '' }}">
                <i class="nav-icon fas fa-coins"></i>
                <p>KHR / VND <i class="fas fa-angle-left right"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @if($isSupervisor)
                <li class="nav-item">
                    <a href="{{ route('admin.result-kh.index-mien-nam') }}"
                       class="nav-link {{ Route::is('admin.result-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Lottery Result</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.credit-kh.index') }}"
                       class="nav-link {{ Route::is('admin.credit-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Credit Management</p>
                    </a>
                </li>
                @endif
                <li class="nav-item">
                    <a href="{{ route('bet-kh.receipt-list') }}"
                       class="nav-link {{ Route::is('bet-kh.*') ? 'active' : '' }}">
                        <i class="far fa-circle nav-icon"></i><p>Receipt / Bet List</p>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        {{-- Profile --}}
        <li class="nav-item">
            <a href="{{ route('admin.profile.edit') }}"
               class="nav-link {{ Route::is('admin.profile.edit') ? 'active' : '' }}">
                <i class="nav-icon fas fa-id-card"></i>
                <p>Profile</p>
            </a>
        </li>

    </ul>
</nav>
