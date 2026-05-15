<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    @media (max-width: 768px) {
        table td, table th { font-size: 11px !important; white-space: nowrap; }
        .btn { font-size: 11px; padding: 2px 6px; }
    }
</style>

<x-admin>
    @section('title', 'Credit History')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <i class="fas fa-history me-1"></i>
                Credit History — {{ $member->name }}
                <small class="text-muted">({{ $member->username }})</small>
            </h3>
            <div>
                <button class="btn btn-sm btn-success me-1 openCreditModal"
                    data-bs-toggle="modal" data-bs-target="#creditModal"
                    data-type="deposit"
                    data-user-id="{{ encrypt($member->id) }}"
                    data-name="{{ $member->name }} ({{ $member->username }})"
                    data-balance="{{ $account?->credit_balance ?? 0 }}">
                    💰 Deposit
                </button>
                <button class="btn btn-sm btn-danger me-2 openCreditModal"
                    data-bs-toggle="modal" data-bs-target="#creditModal"
                    data-type="withdraw"
                    data-user-id="{{ encrypt($member->id) }}"
                    data-name="{{ $member->name }} ({{ $member->username }})"
                    data-balance="{{ $account?->credit_balance ?? 0 }}">
                    💸 Withdraw
                </button>
                <a href="{{ route('admin.credit-kh.index') }}" class="btn btn-sm btn-secondary">
                    ← Back
                </a>
            </div>
        </div>

        <div class="card-body pb-2">
            <div class="row g-3">
                <div class="col-sm-4">
                    <div class="info-box mb-0">
                        <span class="info-box-icon bg-primary"><i class="fas fa-coins"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Current Balance</span>
                            <span class="info-box-number {{ ($account?->credit_balance ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($account?->credit_balance ?? 0, 2) }} VND
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    {{ session('error') }}
                </div>
            @endif

            <table class="table table-striped" id="historyTable" style="font-size:13px">
                <thead>
                    <tr style="font-size:12px">
                        <th style="width:3%">#</th>
                        <th>Date / Time</th>
                        <th>Type</th>
                        <th class="text-end">Amount (VND)</th>
                        <th class="text-end">Balance Before</th>
                        <th class="text-end">Balance After</th>
                        <th>Note</th>
                        <th>By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $i => $tx)
                        @php
                            [$badgeClass, $typeLabel] = match($tx->type) {
                                'deposit'    => ['bg-success',   'Deposit'],
                                'win_credit' => ['bg-info',      'Win Credit'],
                                'withdraw'   => ['bg-danger',    'Withdraw'],
                                'bet_debit'  => ['bg-warning text-dark', 'Bet Debit'],
                                'adjustment' => ['bg-secondary', 'Adjustment'],
                                default      => ['bg-light text-dark', ucfirst($tx->type)],
                            };
                            $isCredit = in_array($tx->type, ['deposit', 'win_credit']);
                        @endphp
                        <tr>
                            <td>{{ $transactions->firstItem() + $i }}</td>
                            <td class="text-nowrap">{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span></td>
                            <td class="text-end fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                {{ $isCredit ? '+' : '-' }}{{ number_format($tx->amount, 2) }}
                            </td>
                            <td class="text-end text-muted">{{ number_format($tx->balance_before, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($tx->balance_after, 2) }}</td>
                            <td class="text-muted small">{{ $tx->note ?? '-' }}</td>
                            <td class="text-muted small">{{ $tx->createdBy?->name ?? 'System' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-2">{{ $transactions->links() }}</div>
        </div>
    </div>

    {{-- Credit Modal --}}
    <div class="modal fade" id="creditModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.credit-kh.deposit') }}">
                @csrf
                <input type="hidden" name="user_id" id="modal-user-id">
                <input type="hidden" name="type" id="modal-type">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="creditModalLabel">Credit Transaction</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Verify Password</label>
                            <input type="password" class="form-control" name="password" required placeholder="Enter your password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Member</label>
                            <input type="text" class="form-control" id="modal-member-name" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Credit Balance (VND)</label>
                            <input type="number" class="form-control" id="modal-balance" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount (VND)</label>
                            <input type="number" name="amount" class="form-control" step="0.01" min="0.01"
                                required placeholder="Enter amount">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Note</label>
                            <textarea name="note" class="form-control" rows="2"
                                placeholder="Optional reason / reference"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="btn-submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
            $('#historyTable').DataTable({
                paging: false, searching: true, ordering: true,
                responsive: true, autoWidth: false
            });

            $(document).on('click', '.openCreditModal', function () {
                const type    = $(this).data('type');
                const name    = $(this).data('name');
                const userId  = $(this).data('user-id');
                const balance = $(this).data('balance');

                $('#creditModalLabel').text(
                    type === 'deposit' ? 'Deposit — ' + name : 'Withdraw — ' + name
                );
                $('#modal-type').val(type);
                $('#modal-user-id').val(userId);
                $('#modal-member-name').val(name);
                $('#modal-balance').val(parseFloat(balance).toFixed(2));
                $('#btn-submit')
                    .removeClass('btn-success btn-danger')
                    .addClass(type === 'deposit' ? 'btn-success' : 'btn-danger')
                    .text(type === 'deposit' ? 'Deposit' : 'Withdraw');
                $('input[name=amount]').val('');
                $('input[name=password]').val('');
            });
        });
    </script>
    @endsection
</x-admin>
