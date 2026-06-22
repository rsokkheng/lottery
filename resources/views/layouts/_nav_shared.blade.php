<style>
.bn-nav {
    position: sticky;
    top: 0;
    z-index: 1000;
    box-shadow: 0 2px 10px rgba(0,0,0,.28);
    font-family: 'Segoe UI', 'Source Sans Pro', sans-serif;
    overflow-x: auto;
    overflow-y: visible;
    scrollbar-width: none;
}
.bn-nav::-webkit-scrollbar { display: none; }

.bn-inner {
    display: flex;
    align-items: center;
    padding: 0 1rem;
    height: 50px;
    min-width: max-content; /* never wrap — scroll instead */
    gap: 0;
}

/* ── Logo ── */
.bn-logo {
    display: flex;
    align-items: center;
    gap: 7px;
    text-decoration: none;
    flex-shrink: 0;
    margin-right: 10px;
}
.bn-logo img { height: 28px; width: auto; }
.bn-logo-text {
    color: #FFD700;
    font-weight: 800;
    font-size: .86rem;
    letter-spacing: .3px;
    white-space: nowrap;
}

/* ── Nav links ── */
.bn-links {
    display: flex;
    align-items: center;
    gap: 2px;
    flex: 1;
}
.bn-link {
    color: rgba(255,255,255,.85);
    text-decoration: none;
    font-size: .79rem;
    font-weight: 600;
    padding: 5px 10px;
    border-radius: 6px;
    white-space: nowrap;
    transition: background .15s, color .15s;
    line-height: 1;
}
.bn-link:hover { background: rgba(255,255,255,.16); color: #fff; }
.bn-link.bn-active {
    background: rgba(255,255,255,.22);
    color: #fff;
    box-shadow: inset 0 -2px 0 #FFD700;
}

/* ── Separator between sections ── */
.bn-sep {
    width: 1px;
    height: 18px;
    background: rgba(255,255,255,.22);
    flex-shrink: 0;
    margin: 0 4px;
}

/* ── Right side ── */
.bn-right {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
    margin-left: 10px;
}
.bn-badge {
    font-size: .64rem;
    font-weight: 800;
    padding: 3px 8px;
    border-radius: 5px;
    letter-spacing: .6px;
    background: rgba(255,255,255,.18);
    color: #fff;
    border: 1px solid rgba(255,255,255,.28);
    white-space: nowrap;
}
.bn-admin-link {
    font-size: .74rem;
    font-weight: 700;
    color: rgba(255,255,255,.88);
    text-decoration: none;
    padding: 4px 9px;
    border-radius: 6px;
    border: 1px solid rgba(255,255,255,.28);
    transition: background .15s;
    white-space: nowrap;
}
.bn-admin-link:hover { background: rgba(255,255,255,.16); color: #fff; }

/* ── Language selector ── */
.bn-lang {
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.22);
    border-radius: 6px;
    color: #fff;
    font-size: .76rem;
    font-weight: 700;
    padding: 4px 7px;
    cursor: pointer;
    outline: none;
    white-space: nowrap;
}
.bn-lang option { background: #1a2535; color: #fff; }
.bn-lang:focus { border-color: rgba(255,255,255,.5); }

/* ── User dropdown ── */
.bn-user { position: relative; }
.bn-user-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 8px;
    padding: 5px 10px;
    color: #fff;
    font-size: .78rem;
    font-weight: 700;
    cursor: pointer;
    transition: background .15s;
    white-space: nowrap;
}
.bn-user-btn:hover { background: rgba(255,255,255,.2); }
.bn-user-initials {
    width: 22px; height: 22px;
    border-radius: 50%;
    background: rgba(255,255,255,.25);
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; font-weight: 800;
    flex-shrink: 0;
}
.bn-user-menu {
    display: none;
    position: fixed; /* fixed so it's not clipped by overflow:hidden on nav */
    right: 10px;
    top: 56px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 8px 28px rgba(0,0,0,.2);
    min-width: 190px;
    z-index: 2000;
    overflow: hidden;
}
.bn-user.open .bn-user-menu { display: block; }
.bn-user-header {
    padding: 10px 14px 9px;
    border-bottom: 1px solid #f0f0f0;
}
.bn-user-header strong { font-size: .84rem; color: #111; display: block; }
.bn-user-header small { font-size: .7rem; color: #6b7280; text-transform: capitalize; }
.bn-user-item {
    display: block;
    padding: 8px 14px;
    font-size: .79rem;
    font-weight: 600;
    color: #374151;
    text-decoration: none;
    transition: background .1s;
    background: none;
    border: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
}
.bn-user-item:hover { background: #f5f5f5; }
.bn-user-item.danger { color: #dc2626; }
.bn-user-divider { height: 1px; background: #f0f0f0; margin: 2px 0; }
</style>

@php $r = $routes; @endphp

<nav class="bn-nav" style="background: {{ $navBg }}">
    <div class="bn-inner">

        {{-- Logo --}}
        <a href="{{ $homeRoute }}" class="bn-logo">
            <img src="{{ asset('images/logo2888_back.png') }}" alt="Logo">
            <span class="bn-logo-text">Lottery2888</span>
        </a>

        {{-- Nav links --}}
        <div class="bn-links">
            @if($isMember)
            <a href="{{ route($r['bet']) }}" class="bn-link {{ Route::is($r['bet']) ? 'bn-active' : '' }}">Bet</a>
            @endif

            <a href="{{ route($r['receiptList']) }}" class="bn-link {{ Route::is($r['receiptList']) ? 'bn-active' : '' }}">Receipt List</a>
            <a href="{{ route($r['betList']) }}" class="bn-link {{ Route::is($r['betList']) ? 'bn-active' : '' }}">Bet List</a>
            <a href="{{ route($r['betNumber']) }}" class="bn-link {{ Route::is($r['betNumber']) ? 'bn-active' : '' }}">Bet Number</a>
            <a href="{{ route($r['betWinning']) }}" class="bn-link {{ Route::is($r['betWinning']) ? 'bn-active' : '' }}">Win Report</a>
            <a href="{{ route($r['resultShow']) }}" class="bn-link {{ Route::is($r['resultShow']) ? 'bn-active' : '' }}">Results</a>

            <span class="bn-sep"></span>

            @if($isAdmin)
            <a href="{{ route($r['dailyMgr']) }}" class="bn-link {{ Route::is($r['dailyMgr']) ? 'bn-active' : '' }}">Daily Report</a>
            <a href="{{ route($r['monthly']) }}" class="bn-link {{ Route::is($r['monthly']) ? 'bn-active' : '' }}">Monthly Report</a>
            @elseif($isMaster)
            <a href="{{ route($r['dailyAll']) }}" class="bn-link {{ Route::is($r['dailyAll']) ? 'bn-active' : '' }}">Daily Report</a>
            <a href="{{ route($r['monthlyAll']) }}" class="bn-link {{ Route::is($r['monthlyAll']) ? 'bn-active' : '' }}">Monthly Report</a>
            @else
            <a href="{{ route($r['dailyAll']) }}" class="bn-link {{ Route::is($r['dailyAll']) ? 'bn-active' : '' }}">Daily Report</a>
            @endif
            <a href="{{ route($r['summary']) }}" class="bn-link {{ Route::is($r['summary']) ? 'bn-active' : '' }}">Summary Report</a>
        </div>

        {{-- Right: badge + lang + admin + user --}}
        <div class="bn-right">
            <span class="bn-badge">{{ $currencyLabel }}</span>

            {{-- Language selector --}}
            <select class="bn-lang" onchange="location = this.value;">
                <option value="{{ route('lang.switch', 'en') }}" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>🇺🇸 EN</option>
                <option value="{{ route('lang.switch', 'vi') }}" {{ app()->getLocale() === 'vi' ? 'selected' : '' }}>🇻🇳 VI</option>
            </select>

            @if($isSupervisor)
            <a href="{{ route('admin.dashboard') }}" class="bn-admin-link">&#9881; Admin</a>
            @endif

            <div class="bn-user" id="bnUserMenu">
                <button class="bn-user-btn" onclick="bnToggle('bnUserMenu')">
                    <span class="bn-user-initials">{{ $initials }}</span>
                    {{ $navU->name }}
                    <svg width="10" height="10" viewBox="0 0 20 20" fill="currentColor" style="opacity:.7;flex-shrink:0;">
                        <path d="M5 8l5 5 5-5z"/>
                    </svg>
                </button>
                <div class="bn-user-menu">
                    <div class="bn-user-header">
                        <strong>{{ $navU->name }}</strong>
                        <small>{{ $navRole }}</small>
                    </div>
                    <a href="{{ route('admin.profile.edit') }}" class="bn-user-item">Profile / Password</a>
                    <a href="{{ route('admin.dashboard') }}" class="bn-user-item">Admin Dashboard</a>
                    <div class="bn-user-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="bn-user-item danger">Log Out</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</nav>

<script>
function bnToggle(id) {
    document.getElementById(id).classList.toggle('open');
}
document.addEventListener('click', function (e) {
    if (!e.target.closest('#bnUserMenu')) {
        document.getElementById('bnUserMenu')?.classList.remove('open');
    }
});
</script>
