@php
    $user    = auth()->user();
    $hasVND  = $user->currencies()->where('currency', 'VND')->exists();
    $hasUSD  = $user->currencies()->where('currency', 'USD')->exists();
    $hasKHR  = $user->currencies()->where('currency', 'KHR')->exists();
    $isAdmin   = $user->hasRole('admin');
    $isMaster  = $user->hasRole('master');
    $isSenior  = $user->hasRole('senior');
    $isManager = $user->hasRole('manager');
    $isSupervisor = $isAdmin || $isMaster || $isSenior || $isManager;
    $today   = \Carbon\Carbon::today()->format('Y-m-d');

    $supervisorRoleNames = ['admin', 'master', 'senior', 'manager', 'share_master'];

    if ($isSupervisor) {
        $memberQuery = \App\Models\User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', $supervisorRoleNames));
        if (!$isAdmin) {
            $memberQuery->where(function ($q) use ($user) {
                $q->where('manager_id', $user->id)->orWhere('master_id', $user->id);
            });
        }
        $totalMembers  = $memberQuery->count();
        $activeMembers = (clone $memberQuery)->where('is_active', 1)->count();
    }
@endphp

{{-- ===================== SUPERVISOR VIEW ===================== --}}
@if($isSupervisor)

{{-- Top stat row --}}
<div class="row">
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $totalMembers }}</h3>
                <p>Total Members</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ route('admin.user.index') }}" class="small-box-footer">
                Manage <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $activeMembers }}</h3>
                <p>Active Members</p>
            </div>
            <div class="icon"><i class="fas fa-user-check"></i></div>
            <a href="{{ route('admin.user.index') }}" class="small-box-footer">
                View <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $totalMembers - $activeMembers }}</h3>
                <p>Suspended Members</p>
            </div>
            <div class="icon"><i class="fas fa-user-slash"></i></div>
            <a href="{{ route('admin.user.index') }}" class="small-box-footer">
                View <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ \Carbon\Carbon::today()->format('d M Y') }}</h3>
                <p>Today</p>
            </div>
            <div class="icon"><i class="fas fa-calendar-day"></i></div>
            <a href="{{ route('admin.balance-report.index') }}" class="small-box-footer">
                Balance Report <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
</div>

{{-- Currency Section Cards --}}
<div class="row">

    {{-- VND --}}
    @if($hasVND)
    <div class="col-lg-4 col-md-6">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-money-bill-wave me-1 text-info"></i>
                    <strong>VND</strong> — Vietnamese Dong
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-receipt me-2 text-muted"></i>Receipt List</span>
                        <a href="{{ url('lotto_vn/receipt-list') }}" class="btn btn-sm btn-outline-info">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-list me-2 text-muted"></i>Bet List</span>
                        <a href="{{ url('lotto_vn/bet-list') }}" class="btn btn-sm btn-outline-info">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-file-invoice me-2 text-muted"></i>Transaction Report</span>
                        <a href="{{ route('admin.account-report.index') }}" class="btn btn-sm btn-outline-info">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-trophy me-2 text-muted"></i>Lottery Result</span>
                        <a href="{{ route('admin.result.index-mien-nam') }}" class="btn btn-sm btn-outline-info">Open</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- USD --}}
    @if($hasUSD)
    <div class="col-lg-4 col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-dollar-sign me-1 text-success"></i>
                    <strong>USD</strong> — US Dollar
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-receipt me-2 text-muted"></i>Receipt List</span>
                        <a href="{{ url('lotto_usd/receipt-list') }}" class="btn btn-sm btn-outline-success">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-list me-2 text-muted"></i>Bet List</span>
                        <a href="{{ url('lotto_usd/bet-list') }}" class="btn btn-sm btn-outline-success">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-file-invoice-dollar me-2 text-muted"></i>Transaction Report</span>
                        <a href="{{ route('admin.account-report.transation-usd') }}" class="btn btn-sm btn-outline-success">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-trophy me-2 text-muted"></i>Lottery Result</span>
                        <a href="{{ route('admin.result.index-mien-nam') }}" class="btn btn-sm btn-outline-success">Open</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- KHR --}}
    @if($hasKHR)
    <div class="col-lg-4 col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-coins me-1 text-warning"></i>
                    <strong>KHR / VND</strong> — Cambodia Lottery
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-receipt me-2 text-muted"></i>Receipt List</span>
                        <a href="{{ route('bet-kh.receipt-list') }}" class="btn btn-sm btn-outline-warning">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-list me-2 text-muted"></i>Bet List</span>
                        <a href="{{ route('bet-kh.bet-list') }}" class="btn btn-sm btn-outline-warning">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-trophy me-2 text-muted"></i>Lottery Result</span>
                        <a href="{{ route('admin.result-kh.index-mien-nam') }}" class="btn btn-sm btn-outline-warning">Open</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-wallet me-2 text-muted"></i>Credit Management</span>
                        <a href="{{ route('admin.credit-kh.index') }}" class="btn btn-sm btn-warning text-white">Open</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif

</div>

{{-- ===================== MEMBER VIEW ===================== --}}
@else {{-- not supervisor --}}

@php
    $creditKH   = \App\Models\AccountKH::where('user_id', $user->id)->value('credit_balance') ?? 0;
    $todayBetsKH = 0;
    if ($hasKHR) {
        $todayBetsKH = \Illuminate\Support\Facades\DB::table('bet_receipt_kh')
            ->where('user_id', $user->id)
            ->whereDate('date', $today)
            ->sum('net_amount');
    }
@endphp

<div class="row">

    {{-- Credit Balance (KHR) --}}
    @if($hasKHR)
    <div class="col-lg-3 col-6">
        <div class="small-box {{ $creditKH > 0 ? 'bg-success' : 'bg-danger' }}">
            <div class="inner">
                <h3 style="font-size:1.4rem">{{ number_format($creditKH, 2) }}</h3>
                <p>Credit Balance (VND)</p>
            </div>
            <div class="icon"><i class="fas fa-wallet"></i></div>
            <a href="{{ route('bet-kh.receipt-list') }}" class="small-box-footer">
                View Receipts <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3 style="font-size:1.4rem">{{ number_format($todayBetsKH, 2) }}</h3>
                <p>Today's Bet Amount (VND)</p>
            </div>
            <div class="icon"><i class="fas fa-ticket-alt"></i></div>
            <a href="{{ url('lotto_kh/bet') }}" class="small-box-footer">
                Place Bet <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endif

    {{-- VND Bet --}}
    @if($hasVND)
    <div class="col-lg-3 col-6">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>VND</h3>
                <p>Vietnamese Lottery</p>
            </div>
            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
            <a href="{{ url('lotto_vn/bet') }}" class="small-box-footer">
                Bet Now <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endif

    {{-- USD Bet --}}
    @if($hasUSD)
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>USD</h3>
                <p>USD Lottery</p>
            </div>
            <div class="icon"><i class="fas fa-dollar-sign"></i></div>
            <a href="{{ url('lotto_usd/bet') }}" class="small-box-footer">
                Bet Now <i class="fas fa-arrow-circle-right"></i>
            </a>
        </div>
    </div>
    @endif

</div>

{{-- Member quick links --}}
<div class="row">
    @if($hasKHR)
    <div class="col-lg-4 col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-coins me-1 text-warning"></i> KHR / VND Lottery</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-dice me-2 text-muted"></i>Place Bet</span>
                        <a href="{{ url('lotto_kh/bet') }}" class="btn btn-sm btn-warning text-white">Bet</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-receipt me-2 text-muted"></i>My Receipts</span>
                        <a href="{{ route('bet-kh.receipt-list') }}" class="btn btn-sm btn-outline-warning">View</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-trophy me-2 text-muted"></i>Win Report</span>
                        <a href="{{ route('bet-kh.bet-winning') }}" class="btn btn-sm btn-outline-warning">View</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if($hasVND)
    <div class="col-lg-4 col-md-6">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-money-bill-wave me-1 text-info"></i> VND Lottery</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-dice me-2 text-muted"></i>Place Bet</span>
                        <a href="{{ url('lotto_vn/bet') }}" class="btn btn-sm btn-info text-white">Bet</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-receipt me-2 text-muted"></i>My Receipts</span>
                        <a href="{{ url('lotto_vn/receipt-list') }}" class="btn btn-sm btn-outline-info">View</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif

    @if($hasUSD)
    <div class="col-lg-4 col-md-6">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-dollar-sign me-1 text-success"></i> USD Lottery</h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-dice me-2 text-muted"></i>Place Bet</span>
                        <a href="{{ url('lotto_usd/bet') }}" class="btn btn-sm btn-success">Bet</a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-receipt me-2 text-muted"></i>My Receipts</span>
                        <a href="{{ url('lotto_usd/receipt-list') }}" class="btn btn-sm btn-outline-success">View</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    @endif
</div>

@endif
