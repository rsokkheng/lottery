@php
    $navUser         = Auth::user();
    $navRole         = $navUser->roles->first()?->name ?? 'user';
    $navIsAdmin    = $navUser->hasRole('admin');
    // Currency-based: any currency user has access to both Vietnam and Khmer
    $navHasVietnam = $navIsAdmin || in_array($navUser->currency, ['VND', 'USD']);
    $navHasKhmer   = $navIsAdmin || in_array($navUser->currency, ['VND', 'USD']);
    $navHasKHR     = $navHasKhmer;
    $navInitials     = strtoupper(substr($navUser->name, 0, 1));
@endphp

<nav class="main-header navbar navbar-expand navbar-white navbar-light">

    {{-- Left: sidebar toggle --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('admin.dashboard') }}" class="nav-link text-muted" style="font-size:.82rem; font-weight:600;">
                <i class="fas fa-home me-1"></i>Dashboard
            </a>
        </li>
    </ul>

    {{-- Right: currency badges + dark mode + user menu --}}
    <ul class="navbar-nav ml-auto align-items-center">

        {{-- Currency badges --}}
        <li class="nav-item d-none d-md-flex align-items-center" style="gap:5px; margin-right:6px;">
            @if($navHasVietnam)
                <span class="badge" style="background:#17a2b8; color:#fff; font-size:.68rem; padding:4px 8px; border-radius:6px; letter-spacing:.4px;">Lotto Vietnam</span>
            @endif
            @if($navHasKhmer)
                <span class="badge" style="background:#d4a017; color:#fff; font-size:.68rem; padding:4px 8px; border-radius:6px; letter-spacing:.4px;">Lotto Cambodia</span>
            @endif
        </li>

        {{-- Dark mode toggle --}}
        <li class="nav-item">
            <button id="darkModeToggle" class="nav-link btn btn-link border-0"
                    title="Toggle dark / light mode"
                    style="font-size:1rem; color:#6c757d; padding:6px 10px;">
                <i id="darkModeIcon" class="fas fa-moon"></i>
            </button>
        </li>

        {{-- User dropdown --}}
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#"
               id="userDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
               style="gap:8px; padding:4px 10px;">
                @if($navUser->avatar)
                    <img src="{{ $navUser->avatar }}" alt="avatar"
                         style="width:30px; height:30px; border-radius:50%; object-fit:cover; border:2px solid #e2e8f0;">
                @else
                    <span style="width:30px; height:30px; border-radius:50%; background:#4e73df; color:#fff;
                                 display:inline-flex; align-items:center; justify-content:center;
                                 font-size:.78rem; font-weight:700; flex-shrink:0;">
                        {{ $navInitials }}
                    </span>
                @endif
                <span class="d-none d-md-block" style="font-size:.83rem; font-weight:600; color:#2d3748; line-height:1.1;">
                    {{ $navUser->name }}
                    <small class="d-block" style="font-size:.68rem; color:#8899aa; font-weight:500; text-transform:capitalize;">{{ $navRole }}</small>
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <div class="px-3 py-2 border-bottom" style="font-size:.78rem; color:#8899aa;">
                    Signed in as <strong class="text-dark">{{ $navUser->username ?? $navUser->name }}</strong>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                    <i class="fas fa-tachometer-alt me-2 text-muted" style="width:16px;"></i> Dashboard
                </a>
                <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-id-card me-2 text-muted" style="width:16px;"></i> My Profile
                </a>
                <div class="dropdown-divider"></div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger" style="border:none; background:none; width:100%; text-align:left;">
                        <i class="fas fa-sign-out-alt me-2" style="width:16px;"></i> Log Out
                    </button>
                </form>
            </div>
        </li>

    </ul>
</nav>

<script>
(function () {
    const body    = document.body;
    const toggle  = document.getElementById('darkModeToggle');
    const icon    = document.getElementById('darkModeIcon');
    const DARK_CLS = 'dark-mode';

    function applyMode(dark) {
        if (dark) {
            body.classList.add(DARK_CLS);
            icon.className = 'fas fa-sun';
        } else {
            body.classList.remove(DARK_CLS);
            icon.className = 'fas fa-moon';
        }
    }

    // Restore saved preference
    applyMode(localStorage.getItem('adminDarkMode') === '1');

    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        const isDark = !body.classList.contains(DARK_CLS);
        applyMode(isDark);
        localStorage.setItem('adminDarkMode', isDark ? '1' : '0');
    });
})();
</script>
