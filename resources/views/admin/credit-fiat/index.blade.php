@php
    $currencyColor = $currency === 'VND' ? '#0dcaf0' : '#198754';
    $currencyBg    = $currency === 'VND' ? '#e3f8fd' : '#e8f5e9';
    $currencyIcon  = $currency === 'VND' ? 'fas fa-money-bill-wave' : 'fas fa-dollar-sign';
    $depositRoute  = route('admin.credit-fiat.deposit', $currency);
    $historyRoute  = fn($id) => route('admin.credit-fiat.history', [$currency, $id]);
@endphp
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* ── Stat cards ── */
    .cr-stat { border-radius:14px; padding:18px 20px; display:flex; align-items:center; gap:16px; box-shadow:0 2px 12px rgba(0,0,0,.07); }
    .cr-stat-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.45rem; flex-shrink:0; }
    .cr-stat-label { font-size:.73rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; margin-bottom:2px; }
    .cr-stat-value { font-size:1.35rem; font-weight:700; line-height:1.1; }

    /* ── Table ── */
    .cr-table-wrap { border-radius:12px; overflow:hidden; border:1px solid #e8ecf0; }
    #creditTable { margin-bottom:0 !important; font-size:.845rem; }
    #creditTable thead th {
        background: linear-gradient(135deg, #1e3a5f 0%, #2563a8 100%);
        color:#fff; font-weight:600; font-size:.75rem; text-transform:uppercase;
        letter-spacing:.5px; border:none; padding:11px 12px; white-space:nowrap;
    }
    #creditTable tbody td { padding:10px 12px; vertical-align:middle; border-color:#f0f2f5; }
    #creditTable tbody tr:hover { background:#f0f5ff; }

    /* ── Alerts ── */
    .cr-alert { border-radius:10px; border:none; padding:12px 16px; font-size:.875rem; }

    /* ── Actions dropdown ── */
    .act-toggle { font-size:.78rem; font-weight:700; padding:4px 12px; border-radius:8px;
                  background:#f1f5f9; color:#374151; border:1px solid #cbd5e1; transition:all .15s; }
    .act-toggle:hover, .act-toggle:focus { background:#334155; color:#fff; border-color:#334155; }
    .act-toggle::after { margin-left:4px; }
    .act-menu { border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.13); border:1px solid #e2e8f0;
                padding:4px 0; min-width:150px; }
    .act-menu .dropdown-item { font-size:.82rem; font-weight:600; padding:7px 14px; display:flex; align-items:center; gap:8px; cursor:pointer; }
    .act-menu .dropdown-item:hover { background:#f1f5f9; }
    .act-menu button.dropdown-item { background:none; border:none; width:100%; text-align:left; }

    /* ── Balance ── */
    .bal-ok  { color:#1a7a3c; font-weight:700; }
    .bal-zero{ color:#dc3545; font-weight:700; }
    .wl-pos  { color:#198754; font-weight:700; }
    .wl-neg  { color:#dc3545; font-weight:700; }

    /* ── Modal ── */
    .modal-header-deposit  { background:linear-gradient(135deg,#1b5e20,#2e7d32); color:#fff; border-radius:12px 12px 0 0; }
    .modal-header-withdraw { background:linear-gradient(135deg,#b71c1c,#c62828); color:#fff; border-radius:12px 12px 0 0; }
    .modal-content { border-radius:14px; border:none; box-shadow:0 10px 40px rgba(0,0,0,.18); }
    .modal-header .btn-close { filter:invert(1); }
    .modal-summary { background:#f0f5ff; border:1px solid #d0def5; border-radius:10px; padding:12px 16px; margin-bottom:16px; }
    .modal-field-label { font-size:.78rem; font-weight:600; color:#495057; margin-bottom:4px; }

    /* ── DataTables ── */
    .dataTables_wrapper .dataTables_filter input { border-radius:8px; border:1px solid #d1d8e0; padding:5px 10px; font-size:.82rem; }
    .dataTables_wrapper .dataTables_length select { border-radius:8px; border:1px solid #d1d8e0; padding:4px 8px; font-size:.82rem; }

    @media (max-width:768px) {
        #creditTable td, #creditTable th { font-size:.75rem; white-space:nowrap; }
        .act-toggle { padding:3px 9px; font-size:.74rem; }
    }
</style>

<x-admin>
    @section('title', 'Credit Management · ' . $currency)

    @php
        $totalMembers = $members->count();
        $totalCredit  = $balances->sum('credit_balance');
        $activeCount  = $balances->filter(fn($b) => $b->credit_balance > 0)->count();
        $totalWinLoss = $members->sum(function($m) use ($stats, $wins) {
            $s = $stats->get($m->id);
            $w = $wins->get($m->id);
            return ($w ? (float)$w->compensate : 0) - ($s ? (float)$s->net_amount : 0);
        });
    @endphp

    <div>

        {{-- ── Summary Cards ── --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="background:#e8f0fe">
                        <i class="fas fa-users" style="color:#1a73e8"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Total Members</div>
                        <div class="cr-stat-value" style="color:#1a73e8">{{ $totalMembers }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="background:#e8f5e9">
                        <i class="fas fa-check-circle" style="color:#2e7d32"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Active Accounts</div>
                        <div class="cr-stat-value" style="color:#2e7d32">{{ $activeCount }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="{{ 'background:' . $currencyBg }}">
                        <i class="{{ $currencyIcon }}" style="color:{{ $currencyColor }}"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Total Credit ({{ $currency }})</div>
                        <div class="cr-stat-value" style="color:{{ $currencyColor }};font-size:1rem">{{ number_format($totalCredit, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="background:{{ $totalWinLoss >= 0 ? '#e8f5e9' : '#fce4ec' }}">
                        <i class="fas fa-chart-line" style="color:{{ $totalWinLoss >= 0 ? '#2e7d32' : '#c62828' }}"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Win / Loss ({{ $date }})</div>
                        <div class="cr-stat-value" style="color:{{ $totalWinLoss >= 0 ? '#2e7d32' : '#c62828' }};font-size:1rem">
                            {{ ($totalWinLoss >= 0 ? '+' : '') . number_format($totalWinLoss, 2) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Alerts ── --}}
        @if (session('success'))
            <div class="alert cr-alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-check-circle fa-lg"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert cr-alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-exclamation-circle fa-lg"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ── Table Card ── --}}
        <div class="bg-white rounded-3 shadow-sm" style="border:1px solid #e8ecf0; overflow:hidden;">

            {{-- Card header --}}
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #e8ecf0; flex-wrap:wrap; gap:10px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#1e3a5f,#2563a8);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="{{ $currencyIcon }} text-white" style="font-size:.9rem"></i>
                    </div>
                    <div>
                        <div style="font-size:.95rem;font-weight:700;color:#1e293b;">Credit Management
                            <span class="badge ms-1" style="font-size:.75rem;background:{{ $currencyColor }};">{{ $currency }}</span>
                        </div>
                        <div style="font-size:.72rem;color:#6c757d;">Vietnam System · Deposit &amp; Withdraw</div>
                    </div>
                </div>
                <form method="GET" action="{{ route('admin.credit-fiat.index', $currency) }}" class="d-flex align-items-center gap-2">
                    <label class="mb-0 fw-semibold text-nowrap" style="font-size:.8rem;color:#495057;">Win/Loss Date:</label>
                    <input type="date" name="date" value="{{ $date }}"
                           class="form-control form-control-sm" style="border-radius:8px;font-size:.82rem;max-width:150px;">
                    <button type="submit" class="btn btn-sm btn-primary text-nowrap" style="border-radius:8px;font-size:.82rem;">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                </form>
            </div>

            {{-- Table --}}
            <div class="p-3">
                <div class="cr-table-wrap">
                    <table class="table table-hover" id="creditTable">
                        <thead>
                            <tr>
                                <th style="width:3%">#</th>
                                <th>Account</th>
                                <th>Name</th>
                                @if (in_array('admin', $roles))
                                    <th>Manager</th>
                                @endif
                                <th class="text-end">Credit Balance ({{ $currency }})</th>
                                <th class="text-end">Turnover</th>
                                <th class="text-end">Net</th>
                                <th class="text-end">Win</th>
                                <th class="text-end">Win / Loss</th>
                                <th class="text-center">Status</th>
                                <th class="text-center" style="min-width:170px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($members as $i => $member)
                                @php
                                    $balance    = (float)($balances->get($member->id)?->credit_balance ?? 0);
                                    $s          = $stats->get($member->id);
                                    $w          = $wins->get($member->id);
                                    $turnover   = $s ? (float)$s->turnover   : 0;
                                    $net        = $s ? (float)$s->net_amount : 0;
                                    $compensate = $w ? (float)$w->compensate : 0;
                                    $winLoss    = $compensate - $net;
                                @endphp
                                <tr>
                                    <td class="text-muted" style="font-size:.75rem;">{{ $i + 1 }}</td>
                                    <td>
                                        <span style="font-family:monospace;font-size:.85rem;font-weight:600;">{{ $member->username }}</span>
                                    </td>
                                    <td>{{ $member->name }}</td>
                                    @if (in_array('admin', $roles))
                                        <td class="text-muted" style="font-size:.82rem;">{{ $member->manager?->name ?? '—' }}</td>
                                    @endif
                                    <td class="text-end">
                                        <span class="{{ $balance > 0 ? 'bal-ok' : 'bal-zero' }}">{{ number_format($balance, 2) }}</span>
                                    </td>
                                    <td class="text-end text-muted" style="font-size:.82rem;">{{ $turnover > 0 ? number_format($turnover, 2) : '—' }}</td>
                                    <td class="text-end text-muted" style="font-size:.82rem;">{{ $net > 0 ? number_format($net, 2) : '—' }}</td>
                                    <td class="text-end {{ $compensate > 0 ? 'text-success fw-bold' : 'text-muted' }}" style="font-size:.82rem;">
                                        {{ $compensate > 0 ? '+'.number_format($compensate, 2) : '—' }}
                                    </td>
                                    <td class="text-end {{ $winLoss > 0 ? 'wl-pos' : ($winLoss < 0 ? 'wl-neg' : 'text-muted') }}" style="font-size:.82rem;">
                                        {{ $s || $w ? ($winLoss >= 0 ? '+' : '').number_format($winLoss, 2) : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($balance > 0)
                                            <span class="badge rounded-pill bg-success" style="font-size:.7rem;">● Active</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger" style="font-size:.7rem;">● No Credit</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn act-toggle dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Actions
                                            </button>
                                            <ul class="dropdown-menu act-menu">
                                                <li>
                                                    <button class="dropdown-item text-success openCreditModal"
                                                        data-bs-toggle="modal" data-bs-target="#creditModal"
                                                        data-type="deposit"
                                                        data-user-id="{{ encrypt($member->id) }}"
                                                        data-name="{{ $member->name }} ({{ $member->username }})"
                                                        data-balance="{{ $balance }}">
                                                        <i class="fas fa-plus-circle"></i>Deposit
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item text-danger openCreditModal"
                                                        data-bs-toggle="modal" data-bs-target="#creditModal"
                                                        data-type="withdraw"
                                                        data-user-id="{{ encrypt($member->id) }}"
                                                        data-name="{{ $member->name }} ({{ $member->username }})"
                                                        data-balance="{{ $balance }}">
                                                        <i class="fas fa-minus-circle"></i>Withdraw
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider my-1"></li>
                                                <li>
                                                    <a class="dropdown-item text-primary" href="{{ $historyRoute($member->id) }}">
                                                        <i class="fas fa-history"></i>History
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Credit Modal ── --}}
    <div class="modal fade" id="creditModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form method="POST" action="{{ $depositRoute }}">
                @csrf
                <input type="hidden" name="user_id" id="modal-user-id">
                <input type="hidden" name="type" id="modal-type">
                <div class="modal-content">
                    <div class="modal-header modal-header-deposit" id="modal-header">
                        <div>
                            <h5 class="modal-title mb-0" id="creditModalLabel">Credit Transaction</h5>
                            <small class="opacity-75" id="modal-subtitle">Enter amount and confirm with password</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        {{-- Member summary --}}
                        <div class="modal-summary mb-4">
                            <div class="row g-2">
                                <div class="col-7">
                                    <div class="text-muted mb-1" style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Member</div>
                                    <div class="fw-bold" id="modal-member-display">—</div>
                                </div>
                                <div class="col-5 text-end">
                                    <div class="text-muted mb-1" style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Current Balance</div>
                                    <div class="fw-bold" id="modal-balance-display" style="color:#1a73e8;font-size:1.05rem;">—</div>
                                </div>
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div class="mb-3">
                            <label class="modal-field-label">Amount ({{ $currency }})</label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius:8px 0 0 8px;background:#f8f9fc;font-weight:700;font-size:.85rem;">{{ $currency }}</span>
                                <input type="number" name="amount" id="modal-amount" class="form-control"
                                       style="border-radius:0 8px 8px 0;font-size:1rem;font-weight:600;"
                                       step="0.01" min="0.01" required placeholder="0.00" autocomplete="off">
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="mb-3">
                            <label class="modal-field-label">Note <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="note" class="form-control" style="border-radius:8px;" placeholder="Reason or reference…">
                        </div>

                        {{-- Password --}}
                        <div class="mb-1">
                            <label class="modal-field-label">Your Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" style="border-radius:8px;" required placeholder="Enter your password to confirm">
                        </div>

                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light w-50" style="border-radius:9px;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="btn-submit" class="btn btn-success w-50" style="border-radius:9px;font-weight:600;">
                            <i class="fas fa-check me-1"></i><span id="btn-submit-text">Confirm</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(function () {
            $('#creditTable').DataTable({
                paging: true, searching: true, ordering: true,
                responsive: true, autoWidth: false,
                language: { search: '', searchPlaceholder: 'Search member…' },
                columnDefs: [{ orderable: false, targets: -1 }]
            });

            $(document).on('click', '.openCreditModal', function () {
                const type      = $(this).data('type');
                const name      = $(this).data('name');
                const userId    = $(this).data('user-id');
                const balance   = parseFloat($(this).data('balance') || 0);
                const isDeposit = type === 'deposit';
                const currency  = '{{ $currency }}';

                $('#modal-header')
                    .removeClass('modal-header-deposit modal-header-withdraw')
                    .addClass(isDeposit ? 'modal-header-deposit' : 'modal-header-withdraw');

                $('#creditModalLabel').text(isDeposit ? '💰 Deposit Credit' : '💸 Withdraw Credit');
                $('#modal-subtitle').text((isDeposit ? 'Add credit to' : 'Remove credit from') + ' ' + name);

                $('#modal-type').val(type);
                $('#modal-user-id').val(userId);
                $('#modal-member-display').text(name);
                $('#modal-balance-display').text(balance.toLocaleString('en', {minimumFractionDigits:2}) + ' ' + currency);
                $('#modal-amount').val('').attr('max', isDeposit ? '' : balance);

                $('#btn-submit')
                    .removeClass('btn-success btn-danger')
                    .addClass(isDeposit ? 'btn-success' : 'btn-danger');
                $('#btn-submit-text').text(isDeposit ? 'Confirm Deposit' : 'Confirm Withdraw');

                $('input[name=password]').val('');
            });

            $('#creditModal').on('shown.bs.modal', function () {
                $('#modal-amount').trigger('focus');
            });
        });
    </script>
    @endsection
</x-admin>
