<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    /* ── Page chrome ── */
    .cr-page { padding: 0 1px; }

    /* ── Stat cards ── */
    .cr-stat { border-radius: 14px; padding: 18px 20px; display:flex; align-items:center; gap:16px; box-shadow:0 2px 12px rgba(0,0,0,.07); }
    .cr-stat-icon { width:52px; height:52px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:1.45rem; flex-shrink:0; }
    .cr-stat-label { font-size:.73rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#6c757d; margin-bottom:2px; }
    .cr-stat-value { font-size:1.35rem; font-weight:700; line-height:1.1; }

    /* ── Filter bar ── */
    .cr-filter { background:#f8f9fc; border:1px solid #e8ecf0; border-radius:12px; padding:14px 18px; margin-bottom:20px; }

    /* ── Alerts ── */
    .cr-alert { border-radius:10px; border:none; padding:12px 16px; font-size:.875rem; }

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
    #creditTable tfoot td { background:#f8f9fc; font-weight:700; }

    /* ── Badges ── */
    .badge-vnd  { background:#0dcaf0; color:#fff; }
    .badge-usd  { background:#198754; color:#fff; }
    .badge-role { font-size:.7rem; padding:3px 8px; border-radius:6px; font-weight:600; }

    /* ── Inline action buttons ── */
    .btn-deposit  { background:#e8f5e9; color:#2e7d32; border:1px solid #c8e6c9; font-size:.75rem; font-weight:600; border-radius:7px; padding:3px 10px; }
    .btn-deposit:hover  { background:#2e7d32; color:#fff; }
    .btn-withdraw { background:#fce4ec; color:#c62828; border:1px solid #f8bbd0; font-size:.75rem; font-weight:600; border-radius:7px; padding:3px 10px; }
    .btn-withdraw:hover { background:#c62828; color:#fff; }
    .btn-history  { background:#e3f2fd; color:#1565c0; border:1px solid #bbdefb; font-size:.75rem; font-weight:600; border-radius:7px; padding:3px 10px; }
    .btn-history:hover  { background:#1565c0; color:#fff; }

    /* ── Balance cell ── */
    .bal-ok   { color:#1a7a3c; font-weight:700; }
    .bal-zero { color:#dc3545; font-weight:700; }
    .bal-pill { display:inline-flex; align-items:center; gap:5px; }
    .cur-badge { font-size:.65rem; padding:2px 6px; border-radius:5px; font-weight:700; }

    /* ── Win/Loss ── */
    .wl-pos { color:#198754; font-weight:700; }
    .wl-neg { color:#dc3545; font-weight:700; }

    /* ── Modal ── */
    .modal-header-deposit  { background:linear-gradient(135deg,#1b5e20,#2e7d32); color:#fff; border-radius:12px 12px 0 0; }
    .modal-header-withdraw { background:linear-gradient(135deg,#b71c1c,#c62828); color:#fff; border-radius:12px 12px 0 0; }
    .modal-content { border-radius:14px; border:none; box-shadow:0 10px 40px rgba(0,0,0,.18); }
    .modal-header .btn-close { filter:invert(1); }
    .modal-field-label { font-size:.78rem; font-weight:600; color:#495057; margin-bottom:4px; }
    .modal-field-ro { background:#f8f9fc; border:1px solid #e0e3ea; border-radius:8px; padding:8px 12px; font-size:.9rem; font-weight:600; }
    .modal-summary { background:#f0f5ff; border:1px solid #d0def5; border-radius:10px; padding:12px 16px; margin-bottom:16px; }
    .modal-summary .row > div { font-size:.8rem; }
    .modal-summary .val { font-size:1.1rem; font-weight:700; }

    /* ── DataTables override ── */
    .dataTables_wrapper .dataTables_filter input { border-radius:8px; border:1px solid #d1d8e0; padding:5px 10px; font-size:.82rem; }
    .dataTables_wrapper .dataTables_length select { border-radius:8px; border:1px solid #d1d8e0; padding:4px 8px; font-size:.82rem; }

    @media (max-width:768px) {
        #creditTable td, #creditTable th { font-size:.75rem; white-space:nowrap; }
        .cr-stat-value { font-size:1rem; }
        .btn-deposit, .btn-withdraw, .btn-history { padding:2px 7px; }
    }
</style>

<x-admin>
    @section('title', 'Credit Management · Khmer')

    @php
        $totalMembers = $members->count();
        $totalVND = $members->sum(fn($m) => strtolower($m->currency) !== 'usd' ? ($m->accountKH?->credit_balance ?? 0) : 0);
        $totalUSD = $members->sum(fn($m) => strtolower($m->currency) === 'usd' ? ($m->accountKHUSD?->credit_balance ?? 0) : 0);
        $activeCount = $members->filter(fn($m) => strtolower($m->currency) === 'usd'
            ? ($m->accountKHUSD?->credit_balance ?? 0) > 0
            : ($m->accountKH?->credit_balance ?? 0) > 0
        )->count();
    @endphp

    <div class="cr-page">

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
                    <div class="cr-stat-icon" style="background:#e3f2fd">
                        <i class="fas fa-coins" style="color:#0d6efd"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Total VND Credit</div>
                        <div class="cr-stat-value" style="color:#0d6efd;font-size:1rem">{{ number_format($totalVND, 2) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="cr-stat bg-white">
                    <div class="cr-stat-icon" style="background:#e8f5e9">
                        <i class="fas fa-dollar-sign" style="color:#198754"></i>
                    </div>
                    <div>
                        <div class="cr-stat-label">Total USD Credit</div>
                        <div class="cr-stat-value" style="color:#198754;font-size:1rem">{{ number_format($totalUSD, 2) }}</div>
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

        {{-- ── Filter & Table Card ── --}}
        <div class="bg-white rounded-3 shadow-sm" style="border:1px solid #e8ecf0; overflow:hidden;">

            {{-- Card header --}}
            <div class="d-flex align-items-center justify-content-between px-4 py-3" style="border-bottom:1px solid #e8ecf0;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;background:linear-gradient(135deg,#1e3a5f,#2563a8);border-radius:9px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-coins text-white" style="font-size:.9rem"></i>
                    </div>
                    <div>
                        <div style="font-size:.95rem;font-weight:700;color:#1e293b;">Credit Management</div>
                        <div style="font-size:.72rem;color:#6c757d;">Khmer System · Deposit &amp; Withdraw</div>
                    </div>
                </div>
                {{-- Date filter --}}
                <form method="GET" action="{{ route('admin.credit-kh.index') }}" class="d-flex align-items-center gap-2">
                    <label class="mb-0 fw-semibold text-nowrap" style="font-size:.8rem;color:#495057;">Win/Loss Date:</label>
                    <input type="date" name="date" value="{{ $date }}"
                           class="form-control form-control-sm" style="border-radius:8px;font-size:.82rem;max-width:150px;">
                    <button type="submit" class="btn btn-sm btn-primary" style="border-radius:8px;font-size:.82rem;white-space:nowrap;">
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
                                <th class="text-end">Credit Balance</th>
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
                                    $isUSD    = strtolower($member->currency) === 'usd';
                                    $balance  = $isUSD
                                        ? (float)($member->accountKHUSD?->credit_balance ?? 0)
                                        : (float)($member->accountKH?->credit_balance ?? 0);
                                    $s        = $stats->get($member->id);
                                    $turnover = $s ? (float)$s->turnover   : 0;
                                    $net      = $s ? (float)$s->net_amount : 0;
                                    $compensate = $s ? (float)$s->compensate : 0;
                                    $winLoss  = $compensate - $net;
                                    $cur      = strtoupper($member->currency);
                                    $curClass = $isUSD ? 'badge-usd' : 'badge-vnd';
                                @endphp
                                <tr>
                                    <td class="text-muted" style="font-size:.75rem;">{{ $i + 1 }}</td>
                                    <td>
                                        <span class="fw-600" style="font-family:monospace;font-size:.85rem;">{{ $member->username }}</span>
                                    </td>
                                    <td>{{ $member->name }}</td>
                                    @if (in_array('admin', $roles))
                                        <td class="text-muted" style="font-size:.82rem;">{{ $member->manager?->name ?? '—' }}</td>
                                    @endif
                                    <td class="text-end">
                                        <div class="bal-pill justify-content-end">
                                            <span class="{{ $balance > 0 ? 'bal-ok' : 'bal-zero' }}">{{ number_format($balance, 2) }}</span>
                                            <span class="cur-badge {{ $curClass }}">{{ $cur }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end text-muted" style="font-size:.82rem;">{{ $turnover > 0 ? number_format($turnover, 2) : '—' }}</td>
                                    <td class="text-end text-muted" style="font-size:.82rem;">{{ $net > 0 ? number_format($net, 2) : '—' }}</td>
                                    <td class="text-end {{ $compensate > 0 ? 'text-success fw-bold' : 'text-muted' }}" style="font-size:.82rem;">
                                        {{ $compensate > 0 ? '+'.number_format($compensate, 2) : '—' }}
                                    </td>
                                    <td class="text-end {{ $winLoss > 0 ? 'wl-pos' : ($winLoss < 0 ? 'wl-neg' : 'text-muted') }}" style="font-size:.82rem;">
                                        {{ $s ? ($winLoss >= 0 ? '+' : '').number_format($winLoss, 2) : '—' }}
                                    </td>
                                    <td class="text-center">
                                        @if ($balance > 0)
                                            <span class="badge rounded-pill bg-success" style="font-size:.7rem;">● Active</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger" style="font-size:.7rem;">● No Credit</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-deposit openCreditModal"
                                                data-bs-toggle="modal" data-bs-target="#creditModal"
                                                data-type="deposit"
                                                data-user-id="{{ encrypt($member->id) }}"
                                                data-name="{{ $member->name }} ({{ $member->username }})"
                                                data-balance="{{ $balance }}"
                                                data-currency="{{ strtolower($member->currency) }}">
                                                <i class="fas fa-plus-circle me-1"></i>Deposit
                                            </button>
                                            <button class="btn btn-withdraw openCreditModal"
                                                data-bs-toggle="modal" data-bs-target="#creditModal"
                                                data-type="withdraw"
                                                data-user-id="{{ encrypt($member->id) }}"
                                                data-name="{{ $member->name }} ({{ $member->username }})"
                                                data-balance="{{ $balance }}"
                                                data-currency="{{ strtolower($member->currency) }}">
                                                <i class="fas fa-minus-circle me-1"></i>Withdraw
                                            </button>
                                            <a href="{{ route('admin.credit-kh.history', $member->id) }}" class="btn btn-history">
                                                <i class="fas fa-history me-1"></i>History
                                            </a>
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
            <form method="POST" action="{{ route('admin.credit-kh.deposit') }}" id="creditForm">
                @csrf
                <input type="hidden" name="user_id" id="modal-user-id">
                <input type="hidden" name="type" id="modal-type">
                <input type="hidden" name="currency" id="modal-currency">
                <div class="modal-content">
                    <div class="modal-header modal-header-deposit" id="modal-header">
                        <div>
                            <h5 class="modal-title mb-0" id="creditModalLabel">Credit Transaction</h5>
                            <small class="opacity-75" id="modal-subtitle">Enter amount and confirm with password</small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">

                        {{-- Member summary bar --}}
                        <div class="modal-summary mb-4">
                            <div class="row g-2">
                                <div class="col-7">
                                    <div class="text-muted mb-1" style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Member</div>
                                    <div class="val fw-bold" id="modal-member-display">—</div>
                                </div>
                                <div class="col-5 text-end">
                                    <div class="text-muted mb-1" style="font-size:.72rem;font-weight:600;text-transform:uppercase;">Current Balance</div>
                                    <div class="val fw-bold" id="modal-balance-display" style="color:#1a73e8;">—</div>
                                </div>
                            </div>
                        </div>

                        {{-- Amount --}}
                        <div class="mb-3">
                            <label class="modal-field-label" id="modal-amount-label">Amount</label>
                            <div class="input-group">
                                <span class="input-group-text" style="border-radius:8px 0 0 8px;background:#f8f9fc;font-weight:700;font-size:.85rem;" id="modal-currency-prefix">VND</span>
                                <input type="number" name="amount" id="modal-amount" class="form-control"
                                       style="border-radius:0 8px 8px 0;font-size:1rem;font-weight:600;"
                                       step="0.01" min="0.01" required placeholder="0.00"
                                       autocomplete="off">
                            </div>
                        </div>

                        {{-- Note --}}
                        <div class="mb-3">
                            <label class="modal-field-label">Note <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" name="note" class="form-control" style="border-radius:8px;font-size:.9rem;" placeholder="Reason or reference…">
                        </div>

                        {{-- Password --}}
                        <div class="mb-1">
                            <label class="modal-field-label">Your Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" style="border-radius:8px;font-size:.9rem;" required placeholder="Enter your password to confirm">
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
                const type     = $(this).data('type');
                const name     = $(this).data('name');
                const userId   = $(this).data('user-id');
                const balance  = parseFloat($(this).data('balance') || 0);
                const currency = ($(this).data('currency') || 'vnd').toUpperCase();
                const isDeposit = type === 'deposit';

                // Header colour
                const $hdr = $('#modal-header');
                $hdr.removeClass('modal-header-deposit modal-header-withdraw')
                    .addClass(isDeposit ? 'modal-header-deposit' : 'modal-header-withdraw');

                $('#creditModalLabel').text(isDeposit ? '💰 Deposit Credit' : '💸 Withdraw Credit');
                $('#modal-subtitle').text((isDeposit ? 'Add credit to' : 'Remove credit from') + ' ' + name);

                // Hidden fields
                $('#modal-type').val(type);
                $('#modal-user-id').val(userId);
                $('#modal-currency').val(currency.toLowerCase());

                // Summary bar
                $('#modal-member-display').text(name);
                $('#modal-balance-display').text(balance.toLocaleString('en', {minimumFractionDigits:2}) + ' ' + currency);

                // Amount input
                $('#modal-currency-prefix').text(currency);
                $('#modal-amount-label').text('Amount (' + currency + ')');
                $('#modal-amount').val('').attr('max', isDeposit ? '' : balance).focus();

                // Submit button
                $('#btn-submit')
                    .removeClass('btn-success btn-danger')
                    .addClass(isDeposit ? 'btn-success' : 'btn-danger');
                $('#btn-submit-text').text(isDeposit ? 'Confirm Deposit' : 'Confirm Withdraw');

                // Reset password
                $('input[name=password]').val('');
            });

            // Auto-focus amount after modal shown
            $('#creditModal').on('shown.bs.modal', function () {
                $('#modal-amount').trigger('focus');
            });
        });
    </script>
    @endsection
</x-admin>
