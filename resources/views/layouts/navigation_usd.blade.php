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
</style>

<nav x-data="{ open: false, accountOpen: false, reportOpen: false }" class="bg-blue-600 border-b border-gray-100 py-4">
    <!-- Primary Navigation Menu -->
    <div class="flex justify-between px-4 sm:px-2 lg:px-8">
        <!-- Logo/Title -->
        <div class="md:flex md:space-x-10">
            <div class="shrink-0 flex items-center m-4 md:m-0">
                <a href="{{ route('bet.input') }}">
                    <img src="{{ asset('images/logo2888_back.png') }}"  style="max-width: 60px;">
                </a>
            </div>

            <!--Menu size computer-->
            <div class="hidden lg:flex lg:space-x-4">
                @if(Auth::user()->roles->pluck('name')->intersect(['admin', 'manager'])->isEmpty())
                    <x-nav-link
                            class="{{ Route::is('bet-usd.input') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                            href="{{ route('bet-usd.input') }}">{{ __('lang.menu.bet') }}</x-nav-link>
                @endif
                <x-nav-link
                        class="{{ Route::is('bet-usd.receipt-list') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.receipt-list') }}">{{ __('lang.menu.receipt-list') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.bet-list') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.bet-list') }}">{{ __('lang.menu.bet-list') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.bet-number') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.bet-number') }}">{{ __('lang.menu.bet-number') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.bet-winning') ? 'active-menu' : 'not-active-menu' }} hover:text-white" href="{{ route('bet-usd.bet-winning') }}">{{ __('lang.menu.win-report') }}</x-nav-link>
                 @if(Auth::user()->roles->pluck('name')->intersect(['admin'])->isEmpty())
                <x-nav-link
                        class="{{ Route::is('bet-usd.reports.daily') ? 'active-menu' : 'not-active-menu' }} hover:text-white" href="{{ route('bet-usd.reports.daily') }}">{{ __('lang.menu.daily-report') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.reports.monthly-allmember') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.reports.monthly-allmember') }}">{{ __('lang.menu.monthly-report') }}</x-nav-link>
                @elseif(Auth::user()->roles->pluck('name')->intersect(['manager'])->isEmpty())  
                <x-nav-link
                        class="{{ Route::is('bet-usd.reports.daily-manager') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.reports.daily-manager') }}">{{ __('lang.menu.daily-report') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.reports.monthly-tracking') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.reports.monthly-tracking') }}">{{ __('lang.menu.monthly-report') }}</x-nav-link>
                @endif
                <x-nav-link
                        class="{{ Route::is('bet-usd.reports.summary') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.reports.summary') }}">{{ __('lang.menu.summary-report') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.result-show') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.result-show') }}">{{ __('lang.menu.result') }}</x-nav-link>
            </div>

            <form action="{{ route('lang.switch', app()->getLocale()) }}" method="GET" class="d-flex flex-column me-4">
                <select 
                    onchange="location = this.value;"  
                    class="form-select form-select-sm border-0 shadow-none bg-transparent text-white"
                    style="appearance: none; -webkit-appearance: none; -moz-appearance: none; background-color: transparent;">
                    <option value="{{ route('lang.switch', 'en') }}" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                    <option value="{{ route('lang.switch', 'vi') }}" {{ app()->getLocale() == 'vi' ? 'selected' : '' }}>🇻🇳 Tiếng Việt</option>
                </select>
            </form>


            {{--   Menu size tablet   --}}
            <div class="hidden lg:hidden md:flex md:items-center md:space-x-2">
                @if(Auth::user()->roles->pluck('name')->intersect(['admin', 'manager'])->isEmpty())
                    <x-nav-link
                            class="{{ Route::is('bet-usd.input') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                            href="{{ route('bet-usd.input') }}">{{ __('lang.menu.bet') }}</x-nav-link>
                @endif
                <x-nav-link
                        class="{{ Route::is('bet-usd.receipt-list') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.receipt-list') }}">{{ __('lang.menu.receipt-list') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.bet-list') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.bet-list') }}">{{ __('lang.menu.bet-list') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.bet-number') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.bet-number') }}">{{ __('lang.menu.bet-number') }}</x-nav-link>
                <x-nav-link
                        class="{{ Route::is('bet-usd.result-show') ? 'active-menu' : 'not-active-menu' }} hover:text-white"
                        href="{{ route('bet-usd.result-show') }}">{{ __('lang.menu.result') }}</x-nav-link>

                <ul class="flex space-x-4">
                    <li class="relative">
                        <x-nav-link
                                class="not-active-menu hover:text-white space-x-2"
                                @click="reportOpen = !reportOpen">
                            <p>{{ __('Report') }} </p>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </x-nav-link>

                        <ul x-show="reportOpen"
                            class="absolute left-0 top-10 bg-white text-white shadow-lg rounded-md mt-1 p-2 whitespace-nowrap"
                            @click.away="reportOpen = false">
                            <li>
                                <x-nav-link
                                        class="{{ Route::is('bet-usd.bet-winning') ? 'text-black' : 'text-black' }} "
                                        href="{{ route('bet-usd.bet-winning') }}">{{ __('lang.menu.win-report') }}</x-nav-link>
                            </li>
                            @if(Auth::user()->roles->pluck('name')->intersect(['admin'])->isEmpty())
                            <li>
                                <x-nav-link
                                        class="black"
                                        href="{{ route('bet-usd.reports.daily') }}">{{ __('lang.menu.daily-report') }}</x-nav-link>
                            </li>
                            <li>
                                <x-nav-link
                                        class="black"
                                        href="{{ route('bet-usd.reports.monthly-allmember') }}">{{ __('lang.menu.monthly-report') }}</x-nav-link>
                            </li>
                            @elseif(Auth::user()->roles->pluck('name')->intersect(['manager'])->isEmpty())
                            <li>
                                <x-nav-link
                                        class="black"
                                        href="{{ route('bet-usd.reports.daily-manager') }}">{{ __('lang.menu.daily-report') }}</x-nav-link>
                            </li>
                            <li>
                                <x-nav-link
                                        class="black"
                                        href="{{ route('bet-usd.reports.monthly-tracking') }}">{{ __('lang.menu.monthly-report') }}</x-nav-link>
                            </li>
                            @endif
                            <li>
                                <x-nav-link
                                        class="black"
                                        href="{{ route('bet-usd.reports.summary') }}">{{ __('lang.menu.summary-report') }}</x-nav-link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Account Dropdown (Tablet/Computer: Always Visible) -->
        <div class="hidden md:flex md:items-center md:ms-6 relative">
            <button @click="accountOpen = !accountOpen"
                    class="text-white font-bold text-lg flex items-center space-x-2">
                <span class="md:text-[14px]">{{ Auth::user()->name ?? 'Guest' }}</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div x-show="accountOpen" @click.away="accountOpen = false"
                 class="absolute right-0 top-0 mt-12 w-48 bg-white rounded-lg shadow-lg overflow-hidden z-50 border border-gray-200">
                <a href="{{ route('admin.profile.edit') }}"
                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100 sm:text-[12px]">{{ __('lang.menu.change-password') }}</a>
                
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100 sm:text-[12px]">{{ __('lang.menu.manage-account') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100 sm:text-[12px]">{{ __('lang.menu.log-out') }}</button>
                </form>
            </div>
        </div>

        <!-- Hamburger (Mobile Only) -->
        <div class="flex items-center md:hidden">
            <button @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-blue-600 focus:outline-none transition duration-150 ease-in-out">
                <svg class="h-8 w-8" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path x-show="!open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                    <path x-show="open" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Mobile Menu (Collapsed by Default) -->
    <div x-show="open" class="md:hidden flex flex-col space-y-2 px-4 py-2 bg-blue-500">
        @if(Auth::user()->roles->pluck('name')->intersect(['admin', 'manager'])->isEmpty())
            <x-nav-link class="{{ Route::is('bet-usd.input') ? 'active-menu' : 'not-active-menu' }}"
                        href="{{ route('bet-usd.input') }}">{{ __('lang.menu.bet') }}</x-nav-link>
        @endif
        <x-nav-link class="{{ Route::is('bet-usd.receipt-list') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.receipt-list') }}">{{ __('lang.menu.receipt-list') }}</x-nav-link>
        <x-nav-link class="{{ Route::is('bet-usd.bet-list') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.bet-list') }}">{{ __('Bet List') }}</x-nav-link>
        <x-nav-link class="{{ Route::is('bet-usd.bet-number') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.bet-number') }}">{{ __('lang.menu.bet-number') }}</x-nav-link>

        <x-nav-link  class="{{ Route::is('bet-usd.bet-winning') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.bet-winning') }}">{{ __('lang.menu.win-report') }}</x-nav-link>

        @if(Auth::user()->roles->pluck('name')->intersect(['admin'])->isEmpty())
        <x-nav-link  class="{{ Route::is('reports.daily') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.reports.daily') }}">{{ __('lang.menu.daily-report') }}</x-nav-link>
        <x-nav-link  class="{{ Route::is('bet-usd.reports.monthly-allmember') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.reports.monthly-allmember') }}">{{ __('lang.menu.monthly-report') }}</x-nav-link>
        @elseif(Auth::user()->roles->pluck('name')->intersect(['manager'])->isEmpty())
        <x-nav-link  class="{{ Route::is('bet-usd.reports.daily-manager') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.reports.daily-manager') }}">{{ __('lang.menu.daily-report') }}</x-nav-link>
        <x-nav-link  class="{{ Route::is('bet-usd.reports.monthly-tracking') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.reports.monthly-tracking') }}">{{ __('lang.menu.monthly-report') }}</x-nav-link>
        @endif
        <x-nav-link class="{{ Route::is('bet-usd.reports.summary') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.reports.summary') }}">{{ __('lang.menu.summary-report') }}</x-nav-link>
        <x-nav-link class="{{ Route::is('bet-usd.result-show') ? 'active-menu' : 'not-active-menu' }}"
                    href="{{ route('bet-usd.result-show') }}">{{ __('lang.menu.result') }}</x-nav-link>

        <!-- Mobile Account Menu -->
        <div class="relative">
            <x-nav-link class="flex justify-start">
                <button @click="accountOpen = !accountOpen"
                        class="text-white font-bold text-[14px] flex items-center space-x-2">
                    <span>{{ Auth::user()->name ?? 'Guest' }}</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </x-nav-link>
            <div x-show="accountOpen" @click.away="accountOpen = false"
                 class="absolute left-0 mt-2 w-48 bg-white rounded-lg shadow-lg overflow-hidden z-50 border border-gray-200">
                <a href="{{ route('admin.profile.edit') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">{{ __('lang.menu.change-password') }}</a>
                <a href="{{ route('admin.dashboard') }}"
                   class="block px-4 py-2 text-gray-700 hover:bg-gray-100">{{ __('lang.menu.manage-account') }}</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">{{ __('lang.menu.log-out') }}</button>
                </form>
            </div>
        </div>
    </div>
</nav>