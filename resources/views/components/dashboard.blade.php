@php
    $user    = auth()->user();
    $isAdmin      = $user->hasRole('admin');
    $isMaster     = $user->hasRole('master');
    $isAgent      = $user->hasRole('agent');
    $isSupervisor = $isAdmin || $isMaster || $isAgent;
    $today         = \Carbon\Carbon::today()->format('Y-m-d');

    $hasVND      = $isAdmin || ($user->bet_system === 'vietnam' && $user->currency === 'VND');
    $hasUSD      = $isAdmin || ($user->bet_system === 'vietnam' && $user->currency === 'USD');
    $hasKhmerVND = $isAdmin || ($user->bet_system === 'khmer' && $user->currency === 'VND');
    $hasKhmerUSD = $isAdmin || ($user->bet_system === 'khmer' && $user->currency === 'USD');
    $hasKhmer    = $hasKhmerVND || $hasKhmerUSD;
    $hasKHR      = $hasKhmer;

    if ($isSupervisor) {
        $supervisorRoleNames = ['admin', 'master', 'agent'];
        $memberQuery   = \App\Models\User::whereDoesntHave('roles', fn($q) => $q->whereIn('name', $supervisorRoleNames));
        if ($isMaster) {
            $memberQuery->where('master_id', $user->id);
        } elseif ($isAgent) {
            $memberQuery->where('manager_id', $user->id);
        }
        $totalMembers  = $memberQuery->count();
        $activeMembers = (clone $memberQuery)->where('is_active', 1)->count();
    }
@endphp

<style>
/* ── Bet-type card grid ── */
.bet-type-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem; }

.bt-card { border-radius: .5rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.12); display: flex; flex-direction: column; }
.bt-card .bt-header { padding: .9rem 1.1rem .75rem; display: flex; align-items: center; gap: .75rem; }
.bt-card .bt-icon  { width: 2.6rem; height: 2.6rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0; }
.bt-card .bt-title { font-weight: 700; font-size: 1rem; line-height: 1.2; margin: 0; }
.bt-card .bt-sub   { font-size: .72rem; opacity: .75; margin: 0; }
.bt-card .bt-body  { padding: .6rem .85rem .9rem; flex: 1; }

/* action select dropdown */
.bt-select-wrap { margin-top: .5rem; display: flex; gap: .4rem; align-items: center; }
.bt-select { flex: 1; font-size: .8rem; font-weight: 600; padding: .35rem .5rem; border-radius: .35rem; border: 1.5px solid #dee2e6; background: #fff; cursor: pointer; height: 34px; }
.bt-select:focus { outline: none; box-shadow: 0 0 0 2px rgba(0,123,255,.2); border-color: #80bdff; }
.bt-go-btn { height: 34px; padding: 0 .75rem; border-radius: .35rem; border: none; font-size: .78rem; font-weight: 700; cursor: pointer; white-space: nowrap; }

/* divider label */
.bt-section-label { font-size: .65rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; opacity: .55; margin: .6rem 0 .2rem; }

/* balance row */
.balance-row { display: flex; align-items: center; justify-content: space-between; padding: .45rem .6rem; border-radius: .35rem; margin-bottom: .3rem; }
.balance-row:last-child { margin-bottom: 0; }

/* colour themes */
.bt-vnd  { border-top: 4px solid #17a2b8; }
.bt-vnd  .bt-header { background: rgba(23,162,184,.08); }
.bt-vnd  .bt-icon   { background: rgba(23,162,184,.15); color: #17a2b8; }
.bt-usd  { border-top: 4px solid #28a745; }
.bt-usd  .bt-header { background: rgba(40,167,69,.08); }
.bt-usd  .bt-icon   { background: rgba(40,167,69,.15); color: #28a745; }
.bt-khvnd{ border-top: 4px solid #ffc107; }
.bt-khvnd .bt-header { background: rgba(255,193,7,.08); }
.bt-khvnd .bt-icon  { background: rgba(255,193,7,.15); color: #d39e00; }
.bt-khusd{ border-top: 4px solid #fd7e14; }
.bt-khusd .bt-header { background: rgba(253,126,20,.08); }
.bt-khusd .bt-icon  { background: rgba(253,126,20,.15); color: #fd7e14; }

/* stat mini-boxes */
.stat-mini { border-radius: .5rem; padding: .85rem 1rem; display: flex; align-items: center; gap: .85rem; box-shadow: 0 2px 6px rgba(0,0,0,.1); }
.stat-mini .stat-val { font-size: 1.7rem; font-weight: 800; line-height: 1; }
.stat-mini .stat-lbl { font-size: .76rem; opacity: .8; margin-top: 3px; }
.stat-mini .stat-ico { font-size: 2rem; opacity: .25; margin-left: auto; }

/* ── Schedule + Reference panel ── */
.sched-row { display: flex; align-items: center; gap: .6rem; padding: .55rem .75rem; border-bottom: 1px solid rgba(0,0,0,.06); }
.sched-row:last-child { border-bottom: 0; }
.sched-time { font-size: .82rem; font-weight: 700; color: #495057; min-width: 4.5rem; white-space: nowrap; }
.sched-companies { flex: 1; display: flex; flex-wrap: wrap; gap: .3rem; }
.sched-badge { display: inline-block; padding: .18rem .55rem; border-radius: .3rem; font-size: .72rem; font-weight: 700; letter-spacing: .03em; border: 1px solid; }
.sched-countdown { font-size: .88rem; font-weight: 800; font-family: 'SFMono-Regular', Consolas, monospace; min-width: 6.5rem; text-align: right; white-space: nowrap; }
.sched-countdown.closed  { color: #6c757d; font-weight: 400; font-size: .78rem; }
.sched-countdown.live    { color: #dc3545; }
.sched-countdown.soon    { color: #fd7e14; }
.sched-countdown.normal  { color: #28a745; }

.ref-table { width: 100%; border-collapse: collapse; font-size: .8rem; }
.ref-table td { padding: .35rem .6rem; border-bottom: 1px solid rgba(0,0,0,.05); vertical-align: top; }
.ref-table tr:last-child td { border-bottom: 0; }
.ref-table td:first-child { white-space: nowrap; font-weight: 700; color: #495057; width: 9rem; }
.ref-chip { display: inline-block; padding: .1rem .45rem; border-radius: .25rem; font-size: .7rem; font-weight: 700; margin: .1rem .15rem .1rem 0; }
</style>

<script>
function btGo(sel) {
    var url = sel.value;
    if (url) { window.location.href = url; sel.value = ''; }
}
</script>

{{-- ══════════════════════════════════════════════════
     TOP STATS ROW  (supervisor only)
══════════════════════════════════════════════════ --}}
@if($isSupervisor)
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stat-mini bg-primary text-white">
            <div>
                <div class="stat-val">{{ $totalMembers }}</div>
                <div class="stat-lbl">Total Members</div>
            </div>
            <i class="fas fa-users stat-ico"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-mini bg-success text-white">
            <div>
                <div class="stat-val">{{ $activeMembers }}</div>
                <div class="stat-lbl">Active Members</div>
            </div>
            <i class="fas fa-user-check stat-ico"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-mini bg-warning text-white">
            <div>
                <div class="stat-val">{{ $totalMembers - $activeMembers }}</div>
                <div class="stat-lbl">Suspended</div>
            </div>
            <i class="fas fa-user-slash stat-ico"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stat-mini bg-info text-white">
            <div>
                <div class="stat-val" style="font-size:1.05rem">{{ \Carbon\Carbon::today()->format('d M Y') }}</div>
                <div class="stat-lbl">Today</div>
            </div>
            <i class="fas fa-calendar-day stat-ico"></i>
        </div>
    </div>
</div>
@endif

{{-- ══════════════════════════════════════════════════
     BET TYPE CARDS
══════════════════════════════════════════════════ --}}
<div class="bet-type-grid">

    {{-- ── Bet Vietnam · Vietnamese Dong ── --}}
    @if($hasVND)
    <div class="bt-card card bt-vnd">
        <div class="bt-header">
            <div class="bt-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div>
                <p class="bt-title">Bet Vietnam · Vietnamese Dong</p>
                <p class="bt-sub">Vietnam Lottery · VND</p>
            </div>
        </div>
        <div class="bt-body">
            @if($isSupervisor)
                <div class="bt-select-wrap">
                    <select class="bt-select" onchange="btGo(this)">
                        <option value="">— Select Action —</option>
                        <optgroup label="Management">
                            <option value="{{ url('lotto_vn/receipt-list') }}">Receipt List</option>
                            <option value="{{ url('lotto_vn/bet-list') }}">Bet List</option>
                            <option value="{{ url('lotto_vn/bet-number') }}">Bet Number</option>
                        </optgroup>
                        <optgroup label="Reports">
                            <option value="{{ route('reports.summary') }}">Summary</option>
                            <option value="{{ route('reports.daily') }}">Daily</option>
                            @if($isAdmin)
                            <option value="{{ route('reports.monthly-tracking') }}">Monthly</option>
                            @endif
                        </optgroup>
                        <optgroup label="Admin">
                            <option value="{{ route('admin.result.index-mien-nam') }}">Lottery Result</option>
                            <option value="{{ route('admin.credit-fiat.index', 'VND') }}">Credit</option>
                        </optgroup>
                    </select>
                </div>
            @else
                <div class="bt-section-label">My Account</div>
                @php
                    $vndCredit = (float)(\App\Models\AccountVND::where('user_id', $user->id)->value('credit_balance') ?? 0);
                @endphp
                <div class="balance-row" style="background:rgba(23,162,184,.08)">
                    <span class="fw-bold text-muted" style="font-size:.8rem"><i class="fas fa-wallet me-1"></i>Credit Balance</span>
                    <span class="fw-bold {{ $vndCredit > 0 ? 'text-info' : 'text-muted' }}">{{ number_format($vndCredit, 2) }}</span>
                </div>
                <div class="bt-select-wrap mt-2">
                    <select class="bt-select" onchange="btGo(this)">
                        <option value="">— Select Action —</option>
                        <option value="{{ url('lotto_vn/bet') }}">Place Bet</option>
                        <option value="{{ url('lotto_vn/receipt-list') }}">My Receipts</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Bet Vietnam · USD Dollar ── --}}
    @if($hasUSD)
    <div class="bt-card card bt-usd">
        <div class="bt-header">
            <div class="bt-icon"><i class="fas fa-dollar-sign"></i></div>
            <div>
                <p class="bt-title">Bet Vietnam · USD Dollar</p>
                <p class="bt-sub">Vietnam Lottery · USD</p>
            </div>
        </div>
        <div class="bt-body">
            @if($isSupervisor)
                <div class="bt-select-wrap">
                    <select class="bt-select" onchange="btGo(this)">
                        <option value="">— Select Action —</option>
                        <optgroup label="Management">
                            <option value="{{ url('lotto_usd/receipt-list') }}">Receipt List</option>
                            <option value="{{ url('lotto_usd/bet-list') }}">Bet List</option>
                            <option value="{{ url('lotto_usd/bet-number') }}">Bet Number</option>
                        </optgroup>
                        <optgroup label="Reports">
                            <option value="{{ route('bet-usd.reports.summary') }}">Summary</option>
                            <option value="{{ route('bet-usd.reports.daily') }}">Daily</option>
                            @if($isAdmin)
                            <option value="{{ route('bet-usd.reports.monthly-tracking') }}">Monthly</option>
                            @endif
                        </optgroup>
                        <optgroup label="Admin">
                            <option value="{{ route('admin.result.index-mien-nam') }}">Lottery Result</option>
                            <option value="{{ route('admin.credit-fiat.index', 'USD') }}">Credit</option>
                        </optgroup>
                    </select>
                </div>
            @else
                <div class="bt-section-label">My Account</div>
                @php
                    $usdCredit = (float)(\App\Models\AccountUSD::where('user_id', $user->id)->value('credit_balance') ?? 0);
                @endphp
                <div class="balance-row" style="background:rgba(40,167,69,.08)">
                    <span class="fw-bold text-muted" style="font-size:.8rem"><i class="fas fa-wallet me-1"></i>Credit Balance</span>
                    <span class="fw-bold {{ $usdCredit > 0 ? 'text-success' : 'text-muted' }}">{{ number_format($usdCredit, 2) }}</span>
                </div>
                <div class="bt-select-wrap mt-2">
                    <select class="bt-select" onchange="btGo(this)">
                        <option value="">— Select Action —</option>
                        <option value="{{ url('lotto_usd/bet') }}">Place Bet</option>
                        <option value="{{ url('lotto_usd/receipt-list') }}">My Receipts</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Bet Khmer · Vietnamese Dong ── --}}
    @if($hasKHR && $hasKhmerVND)
    <div class="bt-card card bt-khvnd">
        <div class="bt-header">
            <div class="bt-icon"><i class="fas fa-coins"></i></div>
            <div>
                <p class="bt-title">Bet Khmer · Vietnamese Dong</p>
                <p class="bt-sub">Khmer Lottery · VND</p>
            </div>
        </div>
        <div class="bt-body">
            @if($isSupervisor)
                <div class="bt-select-wrap">
                    <select class="bt-select" onchange="btGo(this)">
                        <option value="">— Select Action —</option>
                        <optgroup label="Management">
                            <option value="{{ route('bet-kh-vnd.receipt-list') }}">Receipt List</option>
                            <option value="{{ route('bet-kh-vnd.bet-list') }}">Bet List</option>
                            <option value="{{ route('bet-kh-vnd.bet-number') }}">Bet Number</option>
                            <option value="{{ route('bet-kh-vnd.bet-winning') }}">Win Report</option>
                        </optgroup>
                        <optgroup label="Reports">
                            <option value="{{ route('bet-kh-vnd.reports.summary') }}">Summary</option>
                            <option value="{{ route('bet-kh-vnd.reports.daily') }}">Daily</option>
                            @if($isAdmin)
                            <option value="{{ route('bet-kh-vnd.reports.monthly-tracking') }}">Monthly</option>
                            @endif
                        </optgroup>
                        <optgroup label="Admin">
                            <option value="{{ route('admin.result-kh.index-mien-nam') }}">KH Result</option>
                            <option value="{{ route('admin.credit-kh.index') }}">Credit</option>
                        </optgroup>
                    </select>
                </div>
            @else
                <div class="bt-section-label">My Account</div>
                @php
                    $khVndCredit = (float)(\App\Models\AccountKH::where('user_id', $user->id)->value('credit_balance') ?? 0);
                @endphp
                <div class="balance-row" style="background:rgba(255,193,7,.08)">
                    <span class="fw-bold text-muted" style="font-size:.8rem"><i class="fas fa-wallet me-1"></i>Credit Balance</span>
                    <span class="fw-bold {{ $khVndCredit > 0 ? 'text-warning' : 'text-muted' }}">{{ number_format($khVndCredit, 2) }}</span>
                </div>
                <div class="bt-select-wrap mt-2">
                    <select class="bt-select" onchange="btGo(this)">
                        <option value="">— Select Action —</option>
                        <option value="{{ url('lotto_kh_vnd/bet') }}">Place Bet</option>
                        <option value="{{ route('bet-kh-vnd.receipt-list') }}">My Receipts</option>
                        <option value="{{ route('bet-kh-vnd.bet-winning') }}">Win Report</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Bet Khmer · USD Dollar ── --}}
    @if($hasKHR && $hasKhmerUSD)
    <div class="bt-card card bt-khusd">
        <div class="bt-header">
            <div class="bt-icon"><i class="fas fa-coins"></i></div>
            <div>
                <p class="bt-title">Bet Khmer · USD Dollar</p>
                <p class="bt-sub">Khmer Lottery · USD</p>
            </div>
        </div>
        <div class="bt-body">
            @if($isSupervisor)
                <div class="bt-select-wrap">
                    <select class="bt-select" onchange="btGo(this)">
                        <option value="">— Select Action —</option>
                        <optgroup label="Management">
                            <option value="{{ route('bet-kh-usd.receipt-list') }}">Receipt List</option>
                            <option value="{{ route('bet-kh-usd.bet-list') }}">Bet List</option>
                            <option value="{{ route('bet-kh-usd.bet-number') }}">Bet Number</option>
                            <option value="{{ route('bet-kh-usd.bet-winning') }}">Win Report</option>
                        </optgroup>
                        <optgroup label="Reports">
                            <option value="{{ route('bet-kh-usd.reports.summary') }}">Summary</option>
                            <option value="{{ route('bet-kh-usd.reports.daily') }}">Daily</option>
                            @if($isAdmin)
                            <option value="{{ route('bet-kh-usd.reports.monthly-tracking') }}">Monthly</option>
                            @endif
                        </optgroup>
                        <optgroup label="Admin">
                            <option value="{{ route('admin.result-kh.index-mien-nam') }}">KH Result</option>
                            <option value="{{ route('admin.credit-kh.index') }}">Credit</option>
                        </optgroup>
                    </select>
                </div>
            @else
                <div class="bt-section-label">My Account</div>
                @php
                    $khUsdCredit = class_exists('App\Models\AccountKHUSD')
                        ? (float)(\App\Models\AccountKHUSD::where('user_id', $user->id)->value('credit_balance') ?? 0)
                        : 0.0;
                @endphp
                <div class="balance-row" style="background:rgba(253,126,20,.08)">
                    <span class="fw-bold text-muted" style="font-size:.8rem"><i class="fas fa-wallet me-1"></i>Credit Balance</span>
                    <span class="fw-bold {{ $khUsdCredit > 0 ? '' : 'text-muted' }}" style="{{ $khUsdCredit > 0 ? 'color:#fd7e14' : 'color:inherit' }}">{{ number_format($khUsdCredit, 2) }}</span>
                </div>
                <div class="bt-select-wrap mt-2">
                    <select class="bt-select" onchange="btGo(this)">
                        <option value="">— Select Action —</option>
                        <option value="{{ url('lotto_kh_usd/bet') }}">Place Bet</option>
                        <option value="{{ route('bet-kh-usd.receipt-list') }}">My Receipts</option>
                        <option value="{{ route('bet-kh-usd.bet-winning') }}">Win Report</option>
                    </select>
                </div>
            @endif
        </div>
    </div>
    @endif

</div>{{-- /.bet-type-grid --}}

{{-- ══════════════════════════════════════════════════
     ADMIN-ONLY QUICK ACTIONS (reports across all types)
══════════════════════════════════════════════════ --}}
{{-- ══════════════════════════════════════════════════
     DRAW SCHEDULE + BET QUICK REFERENCE
══════════════════════════════════════════════════ --}}
<div class="row g-3 mb-3">

    {{-- Schedule card --}}
    <div class="col-lg-6">
        <div class="card card-outline card-primary h-100">
            <div class="card-header d-flex align-items-center justify-content-between py-2">
                <h3 class="card-title mb-0">
                    <i class="fas fa-clock me-1 text-primary"></i>
                    Today's Draw Schedule
                    <span class="badge badge-secondary ml-1" style="font-size:.65rem;font-weight:600;background:#e9ecef;color:#495057">GMT+7</span>
                </h3>
                <span id="sched-live-clock" style="font-family:monospace;font-size:.8rem;color:#6c757d"></span>
            </div>
            <div class="card-body p-0">

                {{-- 16:05 row --}}
                <div class="sched-row">
                    <span class="sched-time">16:05:00</span>
                    <div class="sched-companies">
                        <span class="sched-badge" style="background:#fff3cd;color:#856404;border-color:#ffe69c">TP</span>
                        <span class="sched-badge" style="background:#d1ecf1;color:#0c5460;border-color:#bee5eb">LA</span>
                        <span class="sched-badge" style="background:#d4edda;color:#155724;border-color:#c3e6cb">BP</span>
                        <span class="sched-badge" style="background:#f8d7da;color:#721c24;border-color:#f5c6cb">HG</span>
                    </div>
                    <span class="sched-countdown" id="cd-1605"></span>
                </div>

                {{-- 17:05 row --}}
                <div class="sched-row">
                    <span class="sched-time">17:05:00</span>
                    <div class="sched-companies">
                        <span class="sched-badge" style="background:#cce5ff;color:#004085;border-color:#b8daff">DNA</span>
                        <span class="sched-badge" style="background:#e2d9f3;color:#432874;border-color:#d1bff0">QNG</span>
                        <span class="sched-badge" style="background:#fde2e2;color:#78181e;border-color:#f9c9ca">DNO</span>
                    </div>
                    <span class="sched-countdown" id="cd-1705"></span>
                </div>

                {{-- 18:05 row --}}
                <div class="sched-row">
                    <span class="sched-time">18:05:00</span>
                    <div class="sched-companies">
                        <span class="sched-badge" style="background:#d6d8d9;color:#1b1e21;border-color:#c6c8ca">HN</span>
                    </div>
                    <span class="sched-countdown" id="cd-1805"></span>
                </div>

            </div>
        </div>
    </div>

    {{-- Bet Quick Reference card --}}
    <div class="col-lg-6">
        <div class="card card-outline card-secondary h-100">
            <div class="card-header py-2">
                <h3 class="card-title mb-0">
                    <i class="fas fa-book me-1 text-secondary"></i> Bet Quick Reference
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="ref-table">
                    <tr>
                        <td>Number Wildcard</td>
                        <td>
                            <span class="ref-chip" style="background:#fff3cd;color:#856404">*</span> = any digit (0–9)
                            &nbsp;|&nbsp;
                            <span class="ref-chip" style="background:#d1ecf1;color:#0c5460">11-19</span> → 11, 12 … 19
                            &nbsp;|&nbsp;
                            <span class="ref-chip" style="background:#d4edda;color:#155724">small</span> = 00–49
                            &nbsp;|&nbsp;
                            <span class="ref-chip" style="background:#f8d7da;color:#721c24">big</span> = 50–99
                        </td>
                    </tr>
                    <tr>
                        <td>Bet Position</td>
                        <td>
                            <span class="ref-chip" style="background:#cce5ff;color:#004085">A</span>
                            <span class="ref-chip" style="background:#cce5ff;color:#004085">B</span>
                            <span class="ref-chip" style="background:#cce5ff;color:#004085">C</span>
                            <span class="ref-chip" style="background:#cce5ff;color:#004085">D</span>
                            <span class="ref-chip" style="background:#004085;color:#fff">ABCD</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Roll / Parlay</td>
                        <td>
                            <span class="ref-chip" style="background:#e2d9f3;color:#432874">Roll → R</span>
                            <span class="ref-chip" style="background:#e2d9f3;color:#432874">Roll 2 → R2</span>
                            <span class="ref-chip" style="background:#432874;color:#fff">Roll Parlay → PL</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Cross</td>
                        <td>
                            <span class="ref-chip" style="background:#f8d7da;color:#721c24">Cross → x</span>
                            <small class="text-muted"> (combine positions across companies)</small>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<script>
(function () {
    // Draw times in GMT+7: [hour, minute, second]
    var draws = [
        { id: 'cd-1605', h: 16, m: 5,  s: 0 },
        { id: 'cd-1705', h: 17, m: 5,  s: 0 },
        { id: 'cd-1805', h: 18, m: 5,  s: 0 },
    ];

    function gmt7Now() {
        var d = new Date();
        var utc = d.getTime() + d.getTimezoneOffset() * 60000;
        return new Date(utc + 7 * 3600000);
    }

    function pad2(n) { return String(n).padStart(2, '0'); }

    function liveClock() {
        var now = gmt7Now();
        var el  = document.getElementById('sched-live-clock');
        if (el) el.textContent = pad2(now.getHours()) + ':' + pad2(now.getMinutes()) + ':' + pad2(now.getSeconds());
    }

    function tick() {
        var now  = gmt7Now();
        var nowS = now.getHours() * 3600 + now.getMinutes() * 60 + now.getSeconds();

        draws.forEach(function (draw) {
            var el = document.getElementById(draw.id);
            if (!el) return;

            var drawS = draw.h * 3600 + draw.m * 60 + draw.s;
            var diff  = drawS - nowS;

            if (diff <= 0) {
                el.className = 'sched-countdown closed';
                el.textContent = 'Closed';
                return;
            }

            var hh = Math.floor(diff / 3600);
            var mm = Math.floor((diff % 3600) / 60);
            var ss = diff % 60;
            var txt = pad2(hh) + ':' + pad2(mm) + ':' + pad2(ss);

            el.textContent = txt;
            if (diff <= 300) {          // < 5 min
                el.className = 'sched-countdown live';
            } else if (diff <= 1800) {  // < 30 min
                el.className = 'sched-countdown soon';
            } else {
                el.className = 'sched-countdown normal';
            }
        });

        liveClock();
    }

    tick();
    setInterval(tick, 1000);
})();
</script>

@if($isAdmin)
<div class="card card-outline card-secondary mt-1">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-cogs me-1"></i> Admin Quick Actions</h3>
    </div>
    <div class="card-body">
        <div class="bt-select-wrap">
            <select class="bt-select" onchange="btGo(this)">
                <option value="">— Select Action —</option>
                <optgroup label="Administration">
                    <option value="{{ route('admin.user.index') }}">User Management</option>
                    <option value="{{ route('admin.bet-lottery-package.index') }}">Lottery Packages</option>
                    <option value="{{ route('admin.menu.index') }}">Menus</option>
                </optgroup>
                @if($hasVND || $hasUSD)
                <optgroup label="Daily Reports">
                    @if($hasVND)
                    <option value="{{ route('admin.report.index') }}">VND Daily Report</option>
                    @endif
                    @if($hasUSD)
                    <option value="{{ route('admin.report.daily-usd') }}">USD Daily Report</option>
                    @endif
                </optgroup>
                @endif
            </select>
        </div>
    </div>
</div>
@endif
