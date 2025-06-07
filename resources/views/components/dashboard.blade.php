<div class="row">
    @php
        $user = auth()->user();
        $hasVND = $user->currencies()->where('currency', 'VND')->exists();
        $hasUSD = $user->currencies()->where('currency', 'USD')->exists();
    @endphp

    @role('admin|manager')
        {{-- Admin/Manager specific content --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ \App\Models\User::count() }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <a href="{{ route('admin.user.index') }}" class="small-box-footer">
                    View <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        @if($hasVND)
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Receipts</h3>
                    <p>VND Receipt List</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <a href="{{ url('lotto_vn/receipt-list') }}" class="small-box-footer">
                    View <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endif

        @if($hasUSD)
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Receipts</h3>
                    <p>USD Receipt List</p>
                </div>
                <div class="icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <a href="{{ url('lotto_usd/receipt-list') }}" class="small-box-footer">
                    View <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endif

    @else
        {{-- Non-admin/manager content --}}
        @if($hasVND)
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Bets</h3>
                    <p>VND Bets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list-alt"></i>
                </div>
                <a href="{{ url('lotto_vn/bet') }}" class="small-box-footer">
                    View <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endif

        @if($hasUSD)
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Bets</h3>
                    <p>USD Bets</p>
                </div>
                <div class="icon">
                    <i class="fas fa-list-alt"></i>
                </div>
                <a href="{{ url('lotto_usd/bet') }}" class="small-box-footer">
                    View <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        @endif
    @endrole
</div>
