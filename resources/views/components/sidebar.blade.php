@php
        $user = auth()->user();
        $hasVND = $user->currencies()->where('currency', 'VND')->exists();
        $hasUSD = $user->currencies()->where('currency', 'USD')->exists();
    @endphp
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                <i class="nav-icon fas fa-tachometer-alt"></i>
                <p>Dashboard</p>
            </a>
        </li>
       
        @hasanyrole('admin|manager')
        <li class="nav-item">
            <a href="{{ route('admin.menu.index') }}" class="nav-link {{ Route::is('admin.menu.index') ? 'active' : '' }}">
                <i class="nav-icon fas fa-home"></i>
                <p>Menus</p>
            </a>
        </li>
            <li class="nav-item">
                <a href="{{ route('admin.user.index') }}"
                    class="nav-link {{ Route::is('admin.user.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user"></i>
                    <p>Account Management
                        <span class="badge badge-info right">{{ $userCount }}</span>
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.balance-report.index') }}"
                    class="nav-link {{ Route::is('admin.balance-report.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-money-bill"></i>
                    <p>Balance Report
                        <span class="badge badge-info right">{{ $userCount }}</span>
                    </p>
                </a>
            </li>
            @endrole
            @hasanyrole('admin')
            @if ($hasVND)
              <li class="nav-item">
                    <a href="{{ route('admin.account-report.index') }}"
                    class="nav-link {{ Route::is('admin.account-report.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>Transaction Report (VND)</p>
                    </a>
                </li> 
                <!-- <li class="nav-item">
                    <a href="{{ route('admin.report.index') }}"
                    class="nav-link {{ Route::is('admin.report.index') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice"></i>
                        <p>Daily Report (VND)</p>
                    </a>
                </li>
                -->
            @endif

            @if ($hasUSD)
                <li class="nav-item">
                    <a href="{{ route('admin.account-report.transation-usd') }}"
                    class="nav-link {{ Route::is('admin.account-report.transation-usd') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Transaction Report (USD)</p>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a href="{{ route('admin.report.daily-usd') }}"
                    class="nav-link {{ Route::is('admin.report.daily-usd') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Daily Report (USD)</p>
                    </a>
                </li>
                -->
            @endif

            @endrole
            @hasanyrole('admin|manager')
            <li class="nav-item">
                <a href="{{ route('admin.bet-lottery-package.index') }}"
                    class="nav-link {{ Route::is('admin.bet-lottery-package.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-list-alt"></i>
                    <p>Lottery Package
                       
                    </p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.result.index-mien-nam') }}"
                   class="nav-link {{ Route::is('admin.result.index-mien-nam') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-list-alt"></i>
                    <p>
                        {{__('lang.menu.lottery-result')}}
                       
                    </p>
                </a>
            </li>
        @endrole
        <li class="nav-item">
            <a href="{{ route('admin.profile.edit') }}"
                class="nav-link {{ Route::is('admin.profile.edit') ? 'active' : '' }}">
                <i class="nav-icon fas fa-id-card"></i>
                <p>Profile</p>
            </a>
        </li>

    </ul>
</nav>
