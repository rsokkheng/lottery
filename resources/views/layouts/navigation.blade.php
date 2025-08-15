
<style>
    .active-menu {
        color: #337ab7 !important;
        font-weight: bold !important;
        background-color: #fff !important;
        padding: 6px 10px !important;
        border-radius: 4px;
        white-space: nowrap;
    }

    .not-active-menu {
        color: white;
        font-weight: bold;
        white-space: nowrap;
    }

    /* Enhanced responsive styles */
    @media (max-width: 767px) {
        .mobile-nav-link {
            padding: 8px 12px;
            display: block;
            border-radius: 4px;
            margin: 2px 0;
        }
        
        .mobile-dropdown {
            background-color: rgba(59, 130, 246, 0.8);
            border-radius: 6px;
            margin: 4px 0;
        }
        
        .mobile-account-dropdown {
            position: static !important;
            width: 100% !important;
            margin-top: 8px !important;
            box-shadow: none !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
        }
    }

    /* iPad Portrait (768px - 1024px) */
    @media (min-width: 768px) and (max-width: 1024px) {
        .tablet-nav {
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            flex-wrap: nowrap;
        }
        
        .tablet-nav::-webkit-scrollbar {
            display: none;
        }
        
        .tablet-nav-container {
            min-width: max-content;
            padding: 0 4px;
            display: flex;
            white-space: nowrap;
        }
        
        .tablet-nav-container .nav-link {
            flex-shrink: 0;
            font-size: 12px;
            padding: 4px 6px !important;
        }
        
        .tablet-dropdown {
            min-width: 200px;
            position: absolute;
            z-index: 50;
        }
    }

    /* iPad Landscape and larger tablets (1025px - 1366px) */
    @media (min-width: 1025px) and (max-width: 1366px) {
        .desktop-nav-link {
            font-size: 14px;
            padding: 4px 8px !important;
        }
    }

    @media (min-width: 1367px) {
        .desktop-nav-link {
            transition: all 0.2s ease-in-out;
        }
        
        .desktop-nav-link:hover {
            transform: translateY(-1px);
        }
    }

    /* Language selector responsive styles */
    .lang-selector {
        background: transparent !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        color: white !important;
        font-size: 14px;
        padding: 4px 8px;
        border-radius: 4px;
    }

    .lang-selector option {
        background-color: #3b82f6 !important;
        color: white !important;
    }

    @media (max-width: 767px) {
        .lang-selector {
            font-size: 12px;
            padding: 2px 4px;
        }
    }

    /* iPad specific language selector */
    @media (min-width: 768px) and (max-width: 1024px) {
        .lang-selector {
            font-size: 13px;
            padding: 3px 6px;
        }
    }

    /* Dropdown improvements */
    .dropdown-menu {
        backdrop-filter: blur(10px);
        background-color: rgba(255, 255, 255, 0.95);
    }

    .dropdown-item {
        transition: background-color 0.15s ease-in-out;
    }

    .dropdown-item:hover {
        background-color: rgba(59, 130, 246, 0.1);
    }

    /* Logo responsive sizing */
    .logo-container img {
        transition: all 0.2s ease-in-out;
    }

    @media (max-width: 767px) {
        .logo-container img {
            max-width: 45px !important;
        }
    }

    @media (min-width: 768px) and (max-width: 1024px) {
        .logo-container img {
            max-width: 55px !important;
        }
    }

    @media (min-width: 1025px) {
        .logo-container img {
            max-width: 60px !important;
        }
    }
</style>

<nav x-data="{ open: false, accountOpen: false, reportOpen: false }" class="bg-blue-600 border-b border-gray-100 py-2 sm:py-3 lg:py-4">
    <!-- Primary Navigation Menu -->
    <div class="flex justify-between items-center px-2 sm:px-4 lg:px-8">
        <!-- Logo + Desktop Navigation Container -->
        <div class="flex items-center space-x-2 sm:space-x-4 lg:space-x-6">
            <!-- Logo/Title -->
            <div class="shrink-0 flex items-center logo-container">
                <a href="{{ route('bet.input') }}">
                    <img src="{{ asset('images/logo2888_back.png') }}" class="max-w-[45px] sm:max-w-[50px] lg:max-w-[60px]" alt="Logo">
                </a>
            </div>

            <!-- Desktop Navigation (1367px+) -->
            <div class="hidden xl:flex xl:items-center xl:space-x-2 2xl:space-x-4">
                @if(Auth::user()->roles->pluck('name')->intersect(['admin', 'manager'])->isEmpty())
                    <x-nav-link
                            class="{{ Route::is('bet.input') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                            href="{{ route('bet.input') }}">{{ __('lang.menu.bet') }}</x-nav-link>
                @endif
                <x-nav-link
                        class="{{ Route::is('bet.receipt-list') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                        href="{{ route('bet.receipt-list') }}">{{ __('lang.menu.receipt-list') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet.bet-list') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                        href="{{ route('bet.bet-list') }}">{{ __('lang.menu.bet-list') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet.bet-number') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                        href="{{ route('bet.bet-number') }}">{{ __('lang.menu.bet-number') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet.bet-winning') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1" 
                        href="{{ route('bet.bet-winning') }}">{{ __('lang.menu.win-report') }}</x-nav-link>
                @if(Auth::user()->roles->pluck('name')->intersect(['admin'])->isEmpty())
                <x-nav-link
                        class="{{ Route::is('reports.daily') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1" 
                        href="{{ route('reports.daily') }}">{{ __('lang.menu.daily-report') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('reports.monthly-allmember') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                        href="{{ route('reports.monthly-allmember') }}">{{ __('lang.menu.monthly-report') }}</x-nav-link>
                @elseif(Auth::user()->roles->pluck('name')->intersect(['manager'])->isEmpty())
                <x-nav-link
                        class="{{ Route::is('reports.daily-manager') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                        href="{{ route('reports.daily-manager') }}">{{ __('lang.menu.daily-report') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('reports.monthly-tracking') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                        href="{{ route('reports.monthly-tracking') }}">{{ __('lang.menu.monthly-report') }}</x-nav-link>
                @endif
                <x-nav-link
                        class="{{ Route::is('reports.summary') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                        href="{{ route('reports.summary') }}">{{ __('lang.menu.summary-report') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet.result-show') ? 'active-menu' : 'not-active-menu' }} hover:text-white desktop-nav-link px-2 py-1"
                        href="{{ route('bet.result-show') }}">{{ __('lang.menu.result') }}</x-nav-link>
            </div>

            <!-- Tablet Navigation (768px - 1366px) -->
            <div class="hidden md:flex xl:hidden tablet-nav">
                <div class="flex items-center space-x-1 tablet-nav-container">
                @if(Auth::user()->roles->pluck('name')->intersect(['admin', 'manager'])->isEmpty())
                    <x-nav-link
                            class="{{ Route::is('bet.input') ? 'active-menu' : 'not-active-menu' }} hover:text-white nav-link px-2 py-1"
                            href="{{ route('bet.input') }}">{{ __('lang.menu.bet') }}</x-nav-link>
                @endif
                <x-nav-link
                        class="{{ Route::is('bet.receipt-list') ? 'active-menu' : 'not-active-menu' }} hover:text-white nav-link px-2 py-1"
                        href="{{ route('bet.receipt-list') }}">{{ __('lang.menu.receipt-list') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet.bet-list') ? 'active-menu' : 'not-active-menu' }} hover:text-white nav-link px-2 py-1"
                        href="{{ route('bet.bet-list') }}">{{ __('lang.menu.bet-list') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet.bet-number') ? 'active-menu' : 'not-active-menu' }} hover:text-white nav-link px-2 py-1"
                        href="{{ route('bet.bet-number') }}">{{ __('lang.menu.bet-number') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet.result-show') ? 'active-menu' : 'not-active-menu' }} hover:text-white nav-link px-2 py-1"
                        href="{{ route('bet.result-show') }}">{{ __('lang.menu.result') }}</x-nav-link>

                <!-- Reports Dropdown for Tablet -->
                <div class="relative">
                    <button @click="reportOpen = !reportOpen"
                            class="not-active-menu hover:text-white nav-link px-2 py-1 flex items-center space-x-1">
                        <span>{{ __('Report') }}</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="reportOpen" @click.away="reportOpen = false"
                         class="absolute left-0 top-full mt-1 bg-white rounded-md shadow-lg py-1 tablet-dropdown dropdown-menu z-50">
                        <a href="{{ route('bet.bet-winning') }}"
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dropdown-item">{{ __('lang.menu.win-report') }}</a>
                        @if(Auth::user()->roles->pluck('name')->intersect(['admin'])->isEmpty())
                        <a href="{{ route('reports.daily') }}"
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dropdown-item">{{ __('lang.menu.daily-report') }}</a>
                        <a href="{{ route('reports.monthly-allmember') }}"
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dropdown-item">{{ __('lang.menu.monthly-report') }}</a>
                        @elseif(Auth::user()->roles->pluck('name')->intersect(['manager'])->isEmpty())
                        <a href="{{ route('reports.daily-manager') }}"
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dropdown-item">{{ __('lang.menu.daily-report') }}</a>
                        <a href="{{ route('reports.monthly-tracking') }}"
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dropdown-item">{{ __('lang.menu.monthly-report') }}</a>
                        @endif
                        <a href="{{ route('reports.summary') }}"
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 dropdown-item">{{ __('lang.menu.summary-report') }}</a>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Account Dropdown, Language Selector & Mobile Menu Button (Right Side) -->
        <div class="flex items-center space-x-2">
            <!-- Language Selector (Desktop/Tablet) -->
            <div class="hidden md:block">
                <form action="{{ route('lang.switch', app()->getLocale()) }}" method="GET">
                    <select 
                        onchange="location = this.value;"  
                        class="lang-selector">
                        <option value="{{ route('lang.switch', 'en') }}" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>🇺🇸 EN</option>
                        <option value="{{ route('lang.switch', 'vi') }}" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>🇻🇳 VI</option>
                    </select>
                </form>
            </div>
            
            <!-- Account Dropdown (Tablet/Desktop) -->
            <div class="hidden md:flex md:items-center relative">
                <button @click="accountOpen = !accountOpen"
                        class="text-white font-bold text-sm lg:text-base flex items-center space-x-1">
                    <span class="truncate max-w-24 lg:max-w-none">{{ Auth::user()->name ?? 'Guest' }}</span>
                    <svg class="w-4 h-4 lg:w-5 lg:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="accountOpen" @click.away="accountOpen = false"
                     class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg overflow-hidden z-50 border border-gray-200 dropdown-menu">
                    <a href="{{ route('admin.profile.edit') }}"
                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm dropdown-item">{{ __('lang.menu.change-password') }}</a>
                    <a href="{{ route('admin.dashboard') }}"
                       class="block px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm dropdown-item">{{ __('lang.menu.manage-account') }}</a>
                    
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm dropdown-item border-t border-gray-200">{{ __('lang.menu.log-out') }}</button>
                    </form>
                </div>
            </div>

            <!-- Hamburger Menu (Mobile Only) -->
            <div class="flex items-center md:hidden">
                <button @click="open = !open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-blue-700 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu (Collapsed by Default) -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform -translate-y-2"
         x-transition:enter-end="opacity-100 transform translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform translate-y-0"
         x-transition:leave-end="opacity-0 transform -translate-y-2"
         class="md:hidden bg-blue-700 px-4 py-3 space-y-2">
        
        <!-- Mobile Language Selector (nearby account) -->
        <div class="mb-3">
            <label class="block text-white text-sm mb-1">{{ __('Language') }}</label>
            <form action="{{ route('lang.switch', app()->getLocale()) }}" method="GET">
                <select onchange="location = this.value;" class="lang-selector w-full">
                    <option value="{{ route('lang.switch', 'en') }}" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                    <option value="{{ route('lang.switch', 'vi') }}" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>🇻🇳 Tiếng Việt</option>
                </select>
            </form>
        </div>

        @if(Auth::user()->roles->pluck('name')->intersect(['admin', 'manager'])->isEmpty())
            <a href="{{ route('bet.input') }}" 
               class="{{ Route::is('bet.input') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link">{{ __('lang.menu.bet') }}</a>
        @endif
        <a href="{{ route('bet.receipt-list') }}" 
           class="{{ Route::is('bet.receipt-list') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link">{{ __('lang.menu.receipt-list') }}</a>
        <a href="{{ route('bet.bet-list') }}" 
           class="{{ Route::is('bet.bet-list') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link">{{ __('lang.menu.bet-list') }}</a>
        <a href="{{ route('bet.bet-number') }}" 
           class="{{ Route::is('bet.bet-number') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link">{{ __('lang.menu.bet-number') }}</a>
        <a href="{{ route('bet.result-show') }}" 
           class="{{ Route::is('bet.result-show') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link">{{ __('lang.menu.result') }}</a>

        <!-- Mobile Reports Section -->
        <div class="mobile-dropdown">
            <button @click="reportOpen = !reportOpen"
                    class="w-full text-left not-active-menu mobile-nav-link flex justify-between items-center">
                <span>{{ __('Reports') }}</span>
                <svg class="w-4 h-4 transform transition-transform" :class="reportOpen ? 'rotate-180' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="reportOpen" x-transition class="pl-4 space-y-1 mt-2">
                <a href="{{ route('bet.bet-winning') }}" 
                   class="{{ Route::is('bet.bet-winning') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link text-sm">{{ __('lang.menu.win-report') }}</a>
                @if(Auth::user()->roles->pluck('name')->intersect(['admin'])->isEmpty())
                <a href="{{ route('reports.daily') }}" 
                   class="{{ Route::is('reports.daily') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link text-sm">{{ __('lang.menu.daily-report') }}</a>
                <a href="{{ route('reports.monthly-allmember') }}" 
                   class="{{ Route::is('reports.monthly-allmember') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link text-sm">{{ __('lang.menu.monthly-report') }}</a>
                @elseif(Auth::user()->roles->pluck('name')->intersect(['manager'])->isEmpty())
                <a href="{{ route('reports.daily-manager') }}" 
                   class="{{ Route::is('reports.daily-manager') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link text-sm">{{ __('lang.menu.daily-report') }}</a>
                <a href="{{ route('reports.monthly-tracking') }}" 
                   class="{{ Route::is('reports.monthly-tracking') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link text-sm">{{ __('lang.menu.monthly-report') }}</a>
                @endif
                <a href="{{ route('reports.summary') }}" 
                   class="{{ Route::is('reports.summary') ? 'active-menu' : 'not-active-menu' }} mobile-nav-link text-sm">{{ __('lang.menu.summary-report') }}</a>
            </div>
        </div>

        <!-- Mobile Account Menu -->
        <div class="mobile-dropdown">
            <button @click="accountOpen = !accountOpen"
                    class="w-full text-left not-active-menu mobile-nav-link flex justify-between items-center">
                <span>{{ Auth::user()->name ?? 'Guest' }}</span>
                <svg class="w-4 h-4 transform transition-transform" :class="accountOpen ? 'rotate-180' : ''" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="accountOpen" x-transition class="mobile-account-dropdown bg-white rounded mt-2">
                <a href="{{ route('admin.profile.edit') }}" 
                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">{{ __('lang.menu.change-password') }}</a>
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm">{{ __('lang.menu.manage-account') }}</a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 text-sm border-t border-gray-200">{{ __('lang.menu.log-out') }}</button>
                </form>
            </div>
        </div>
    </div>
</nav>
