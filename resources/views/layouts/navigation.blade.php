<style>
    .active-menu {
        color: #337ab7 !important;
        font-weight: bold !important;
        background-color: #fff !important;
        padding: clamp(0.2vw, 0.3rem, 0.5vw) clamp(0.4vw, 0.5rem, 0.8vw) !important;
        border-radius: clamp(2px, 0.3vw, 4px);
        white-space: nowrap;
        font-size: clamp(0.5rem, 0.8vw, 0.75rem);
    }

    .not-active-menu {
        color: white;
        font-weight: bold;
        white-space: nowrap;
        font-size: clamp(0.5rem, 0.8vw, 0.75rem);
    }

    /* Auto-sizing navigation */
    .nav-container {
        display: flex;
        align-items: center;
        gap: clamp(0.5rem, 1.5vw, 2rem);
        flex-wrap: wrap;
    }

    .nav-link {
        padding: clamp(0.2vw, 0.3rem, 0.5vw) clamp(0.4vw, 0.5rem, 0.8vw);
        transition: all 0.2s ease-in-out;
        text-decoration: none;
        display: inline-block;
        font-size: clamp(0.5rem, 0.8vw, 0.75rem);
    }

    .nav-link:hover {
        transform: translateY(-1px);
        color: white !important;
    }

    /* Language selector auto-sizing */
    .lang-selector {
        background: transparent !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: white !important;
        font-size: clamp(0.45rem, 0.7vw, 0.65rem);
        padding: clamp(0.15vw, 0.2rem, 0.4vw) clamp(0.3vw, 0.4rem, 0.6vw);
        border-radius: clamp(2px, 0.2vw, 4px);
    }

    .lang-selector option {
        background-color: #3b82f6 !important;
        color: white !important;
    }

    /* Auto-sizing dropdown improvements */
    .dropdown-menu {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95);
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: clamp(4px, 0.5vw, 12px);
        width: clamp(160px, 15vw, 240px);
        border-radius: clamp(4px, 0.5vw, 12px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        z-index: 50;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .dropdown-item {
        transition: background-color 0.15s ease-in-out;
        display: block;
        padding: clamp(0.3vw, 0.5rem, 0.7vw) clamp(0.6vw, 0.75rem, 1vw);
        color: #374151;
        text-decoration: none;
        font-size: clamp(0.45rem, 0.7vw, 0.65rem);
    }

    .dropdown-item:hover {
        background-color: rgba(59, 130, 246, 0.1);
    }

    /* Auto-sizing logo */
    .logo-container img {
        max-width: clamp(35px, 4vw, 80px);
        height: auto;
        transition: all 0.2s ease-in-out;
    }

    /* Auto-sizing account dropdown button */
    .account-btn {
        color: white;
        font-weight: bold;
        display: flex;
        align-items: center;
        gap: clamp(3px, 0.4vw, 8px);
        background: none;
        border: none;
        cursor: pointer;
        padding: clamp(0.3vw, 0.4rem, 0.6vw) clamp(0.5vw, 0.6rem, 0.8vw);
        border-radius: clamp(2px, 0.2vw, 4px);
        transition: background-color 0.2s ease;
        font-size: clamp(0.5rem, 0.8vw, 0.75rem);
    }

    .account-btn:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .account-dropdown-container {
        position: relative;
        display: inline-block;
    }

    /* Auto-sizing reports dropdown */
    .reports-dropdown {
        position: relative;
        display: inline-block;
    }

    .reports-btn {
        padding: clamp(0.3vw, 0.5rem, 0.8vw) clamp(0.5vw, 0.8rem, 1.2vw);
        color: white;
        font-weight: bold;
        background: none;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: clamp(2px, 0.3vw, 6px);
        transition: all 0.2s ease-in-out;
        white-space: nowrap;
        font-size: clamp(0.7rem, 1.2vw, 1.1rem);
    }

    .reports-btn:hover {
        transform: translateY(-1px);
        color: white !important;
    }

    .reports-dropdown-menu {
        position: absolute;
        left: 0;
        top: 100%;
        margin-top: clamp(2px, 0.3vw, 6px);
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: clamp(4px, 0.5vw, 12px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        z-index: 50;
        border: 1px solid rgba(0, 0, 0, 0.1);
        min-width: clamp(160px, 15vw, 240px);
        backdrop-filter: blur(10px);
    }

    /* Auto-sizing navigation bar padding */
    .nav {
        padding: clamp(0.4rem, 1vw, 1.5rem) clamp(0.8rem, 2vw, 3rem);
    }

    /* Auto-sizing SVG icons */
    svg {
        width: clamp(10px, 0.8vw, 16px);
        height: clamp(10px, 0.8vw, 16px);
    }
</style>

<nav class="bg-blue-600 border-b border-gray-100 nav">
    <!-- Primary Navigation Menu -->
    <div class="flex justify-between items-center">
        <!-- Logo + Navigation Container -->
        <div class="nav-container">
            <!-- Logo/Title -->
            <div class="shrink-0 flex items-center logo-container">
                <a href="{{ route('bet.input') }}">
                    <img src="{{ asset('images/logo2888_back.png') }}" alt="Logo">
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="flex items-center flex-wrap" style="gap: clamp(0.3rem, 1vw, 1.5rem);">
                @if(Auth::user()->roles->pluck('name')->intersect(['admin', 'manager'])->isEmpty())
                    <a href="{{ route('bet.input') }}" 
                       class="{{ Route::is('bet.input') ? 'active-menu' : 'not-active-menu' }} nav-link">
                        {{ __('lang.menu.bet') }}
                    </a>
                @endif
                
                <a href="{{ route('bet.receipt-list') }}" 
                   class="{{ Route::is('bet.receipt-list') ? 'active-menu' : 'not-active-menu' }} nav-link">
                    {{ __('lang.menu.receipt-list') }}
                </a>
                
                <a href="{{ route('bet.bet-list') }}" 
                   class="{{ Route::is('bet.bet-list') ? 'active-menu' : 'not-active-menu' }} nav-link">
                    {{ __('lang.menu.bet-list') }}
                </a>
                
                <a href="{{ route('bet.bet-number') }}" 
                   class="{{ Route::is('bet.bet-number') ? 'active-menu' : 'not-active-menu' }} nav-link">
                    {{ __('lang.menu.bet-number') }}
                </a>
                
                <a href="{{ route('bet.bet-winning') }}" 
                   class="{{ Route::is('bet.bet-winning') ? 'active-menu' : 'not-active-menu' }} nav-link">
                    {{ __('lang.menu.win-report') }}
                </a>
                @if(Auth::user()->roles->pluck('name')->contains('admin'))
                    {{-- Admin --}}
                   
                    <a href="{{ route('reports.daily-manager') }}" 
                       class="{{ Route::is('reports.daily-manager') ? 'active-menu' : 'not-active-menu' }} nav-link">
                        {{ __('lang.menu.daily-report') }}
                    </a>
                    <a href="{{ route('reports.monthly-tracking') }}" 
                       class="{{ Route::is('reports.monthly-tracking') ? 'active-menu' : 'not-active-menu' }} nav-link">
                        {{ __('lang.menu.monthly-report') }}
                    </a>
                @elseif(Auth::user()->roles->pluck('name')->contains('manager'))
                    {{-- Manager --}}
                     <a href="{{ route('reports.daily') }}" 
                       class="{{ Route::is('reports.daily') ? 'active-menu' : 'not-active-menu' }} nav-link">
                        {{ __('lang.menu.daily-report') }}
                    </a>
                    <a href="{{ route('reports.monthly-allmember') }}" 
                       class="{{ Route::is('reports.monthly-allmember') ? 'active-menu' : 'not-active-menu' }} nav-link">
                        {{ __('lang.menu.monthly-report') }}
                    </a>
  
                @elseif(Auth::user()->roles->pluck('name')->contains('member'))
                    {{-- Member --}}
                    <a href="{{ route('reports.daily') }}" 
                       class="{{ Route::is('reports.daily') ? 'active-menu' : 'not-active-menu' }} nav-link">
                        {{ __('lang.menu.daily-report') }}
                    </a>
                @endif

              
                <a href="{{ route('reports.summary') }}" 
                   class="{{ Route::is('reports.summary') ? 'active-menu' : 'not-active-menu' }} nav-link">
                    {{ __('lang.menu.summary-report') }}
                </a>
                
                <a href="{{ route('bet.result-show') }}" 
                   class="{{ Route::is('bet.result-show') ? 'active-menu' : 'not-active-menu' }} nav-link">
                    {{ __('lang.menu.result') }}
                </a>
            </div>
        </div>

        <!-- Language Selector & Account Dropdown -->
        <div class="flex items-center flex-wrap" style="gap: clamp(0.5rem, 2vw, 2rem);">
            <!-- Language Dropdown (Auto-scaling) -->
            <div>
                <form action="{{ route('lang.switch', app()->getLocale()) }}" method="GET">
                    <select onchange="location = this.value;" class="lang-selector">
                        <option value="{{ route('lang.switch', 'en') }}" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>
                            🇺🇸 EN
                        </option>
                        <option value="{{ route('lang.switch', 'vi') }}" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>
                            🇻🇳 VI
                        </option>
                    </select>
                </form>
            </div>
            
            <!-- Account Dropdown (Auto-scaling) -->
            <div class="account-dropdown-container">
                <button onclick="toggleAccountDropdown()" class="account-btn">
                    <span>{{ Auth::user()->name ?? 'Guest' }}</span>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                
                <div id="accountDropdown" class="dropdown-menu" style="display: none;">
                    <a href="{{ route('admin.profile.edit') }}" class="dropdown-item">
                        {{ __('lang.menu.change-password') }}
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="dropdown-item">
                        {{ __('lang.menu.manage-account') }}
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item" style="width: 100%; text-align: left; border: none; background: none; border-top: 1px solid #e5e7eb;">
                            {{ __('lang.menu.log-out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
// Dropdown toggle function for account menu
function toggleAccountDropdown() {
    const dropdown = document.getElementById('accountDropdown');
    const isVisible = dropdown.style.display === 'block';
    
    if (isVisible) {
        dropdown.style.display = 'none';
    } else {
        dropdown.style.display = 'block';
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const accountContainer = document.querySelector('.account-dropdown-container');
    
    if (!accountContainer.contains(event.target)) {
        document.getElementById('accountDropdown').style.display = 'none';
    }
});
</script>