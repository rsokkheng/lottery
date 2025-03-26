<style>
    .active-menu {
        color: #337ab7 !important;
        font-weight: bold !important;
        font-size: 15px !important;
        background-color: #fff !important;  /* Change this to your desired background color */
        padding: 6px 12px !important;  /* Adjust padding as needed */
        border-radius: 4px;  /* Optional: Add border radius for rounded corners */
    }
</style>


<nav x-data="{ open: false }" class="bg-white border-b border-gray-100" style="background-color:rgb(37 99 235)">
    <!-- Primary Navigation Menu -->
    <div class="max-w-12xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a style="color:yellow;font-weight: 800;font-size: 20px;">Lottery 2888</a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:ms-10 space-x-8">
                    <x-nav-link style="font-size: 15px;font-weight: bold; color:white" class="{{ Route::is('bet.input') ? 'active-menu' :''}}" href="{{ route('bet.input') }}">{{ __('Bet') }}</x-nav-link>
                    <x-nav-link style="font-size: 15px;font-weight: bold; color:white" class="{{ Route::is('bet.receipt-list') ? 'active-menu' :''}}" href="{{ route('bet.receipt-list') }}">{{ __('Receipt List') }}</x-nav-link>
                    <x-nav-link style="font-size: 15px;font-weight: bold; color:white"  class="{{ Route::is('bet.bet-list') ? 'active-menu' :''}}" href="{{ route('bet.bet-list') }}">{{ __('Bet List') }}</x-nav-link>
                    <x-nav-link style="font-size: 15px;font-weight: bold; color:white"  class="{{ Route::is('bet.bet-number') ? 'active-menu' :''}}" href="{{ route('bet.bet-number') }}">{{ __('Bet Number') }}</x-nav-link>
                    <x-nav-link style="font-size: 15px;font-weight: bold; color:white">{{ __('Win Report') }}</x-nav-link>
                    <x-nav-link style="font-size: 15px;font-weight: bold; color:white">{{ __('Daily Report') }}</x-nav-link>
                    <x-nav-link style="font-size: 15px;font-weight: bold; color:white" class="{{ Route::is('reports.summary') ? 'active-menu' :''}}" href="{{ route('reports.summary') }}">{{ __('Summary Report') }}</x-nav-link>
                    <x-nav-link style="font-size: 15px;font-weight: bold; color:white" class="{{ Route::is('bet.result-show') ? 'active-menu' :''}}" href="{{ route('bet.result-show') }}">{{ __('Results') }}</x-nav-link>
                </div>

            </div>

            <!-- Settings Dropdown -->
            <div x-data="{ open: false }" class="flex justify-center sm:ms-6">
                <button @click="open = !open" class="text-white font-bold text-lg flex items-center space-x-2">
                    <span>{{ Auth::user()->name ?? 'Guest' }}</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            
                <!-- Dropdown Menu -->
                <div x-show="open" @click.away="open = false" 
                    class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg overflow-hidden z-50 border border-gray-200">
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        {{ __('Change Password') }}
                    </a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        {{ __('Change Language') }}
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                        {{ __('Manage Account') }}
                    </a>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <input type="submit" name="submit" value="Log out" class="btn btn-primary btn-sm">
                    </form>
                    </a>
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

    <!-- Mobile Menu (collapsed on small screens) -->
    <div x-show="open" class="sm:hidden flex flex-col space-y-2 px-4 py-2 bg-blue-500 text-white">
        <x-nav-link style="font-size: 13px; font-weight: 800;" class="{{ Route::is('bet.input') ? 'active-menu' :''}}" href="{{ route('bet.input') }}">{{ __('Bet') }}</x-nav-link>
        <x-nav-link style="font-size: 13px; font-weight: 800;" class="{{ Route::is('bet.receipt-list') ? 'active-menu' :''}}" href="{{ route('bet.receipt-list') }}">{{ __('Receipt List') }}</x-nav-link>
        <x-nav-link style="font-size: 13px; font-weight: 800;"  class="{{ Route::is('bet.bet-list') ? 'active-menu' :''}}" href="{{ route('bet.bet-list') }}">{{ __('Bet List') }}</x-nav-link>
        <x-nav-link style="font-size: 13px; font-weight: 800;" class="{{ Route::is('bet.bet-number') ? 'active-menu' :''}}" href="{{ route('bet.bet-number') }}">{{ __('Bet Number') }}</x-nav-link>
        <x-nav-link style="font-size: 13px; font-weight: 800;" href="#">{{ __('Win Report') }}</x-nav-link>
        <x-nav-link style="font-size: 13px; font-weight: 800;" href="#">{{ __('Daily Report') }}</x-nav-link>
        <x-nav-link style="font-size: 13px; font-weight: 800;" href="#" class="{{ Route::is('reports.summary') ? 'active-menu' :''}}" href="{{ route('reports.summary') }}">{{ __('Summary Report') }}</x-nav-link>
        <x-nav-link style="font-size: 13px; font-weight: 800;" class="{{ Route::is('bet.result-show') ? 'active-menu' :''}}" href="{{ route('bet.result-show') }}">{{ __('Results') }}</x-nav-link>
    </div>
</nav>
