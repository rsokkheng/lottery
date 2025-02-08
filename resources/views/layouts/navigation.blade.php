<style>
    .active-menu {
        color: blue !important;
        font-weight: bold !important;
    }
</style>
<nav x-data="{ open: false }" class="bg-white border-b border-gray-100" style="background-color:rgb(37 99 235)">
    <!-- Primary Navigation Menu -->
    <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a style="color:yellow;font-weight: 800;font-size: 26px;">HAPPY SPORT 888</a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:ms-10 space-x-8">
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white {{ Route::is('bet.input') ? 'active-menu' :''}}" href="{{ route('bet.input') }}">{{ __('Bet') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Receipt List') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Bet List') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Bet Number') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Total Bet Number') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Win Report') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Daily Report') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Summary Report') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white {{ Route::is('bet.result-show') ? 'active-menu' :''}}" href="{{ route('bet.result-show') }}">{{ __('Results') }}</x-nav-link>
                </div>

            </div>

            <!-- Settings Dropdown -->
            <div class="sm:flex sm:items-center sm:ms-6">
                <div class="hidden space-x-8 sm:-my-px sm:ms-20 sm:flex">
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ Auth::user()->name ?? 'Guest' }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Change Password') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Change Language') }}</x-nav-link>
                    <x-nav-link style="font-size: 16px;font-weight: 800; color:white">{{ __('Logout') }}</x-nav-link>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
{{--            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">--}}
            <x-responsive-nav-link >
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">
                    {{__('Name user')}}}
{{--                    {{ Auth::user()->name }}--}}
                </div>
                <div class="font-medium text-sm text-gray-500">
                    {{__('Email User')}}
{{--                    {{ Auth::user()->email }}--}}

                </div>
            </div>

            <div class="mt-3 space-y-1">
{{--                <x-responsive-nav-link :href="route('profile.edit')">--}}
                <x-responsive-nav-link>
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
