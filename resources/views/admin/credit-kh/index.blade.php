<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    @media (max-width: 768px) {
        table td, table th { font-size: 11px !important; white-space: nowrap; }
        .btn { font-size: 11px; padding: 2px 6px; }
    }
</style>

<x-admin>
    @section('title', 'Credit Management (VND)')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-coins me-1"></i> Credit Management (VND)</h3>
        </div>
        <div class="card-body pb-2">

            {{-- Date filter --}}
            <form method="GET" action="{{ route('admin.credit-kh.index') }}" class="d-flex align-items-center gap-2 mb-3" style="max-width:340px">
                <label class="form-label mb-0 text-nowrap fw-semibold">Win/Loss Date:</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-primary text-nowrap">Filter</button>
            </form>

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

            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="creditTable" style="font-size:13px">
                    <thead>
                        <tr style="font-size:12px">
                            <th style="width:3%">#</th>
                            <th>Account ID</th>
                            <th>Name</th>
                            @if (in_array('admin', $roles))
                                <th>Manager</th>
                            @endif
                            <th class="text-end">Credit Balance (VND)</th>
                            <th class="text-end">Outstanding</th>
                            <th class="text-end">Turnover</th>
                            <th class="text-end">Net Amount</th>
                            <th class="text-end">Win (Compensate)</th>
                            <th class="text-end">Win / Loss</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $i => $member)
                            @php
                                $balance     = (float)($member->accountKH?->credit_balance ?? 0);
                                $s           = $stats->get($member->id);
                                $turnover    = $s ? (float)$s->turnover   : 0;
                                $net         = $s ? (float)$s->net_amount : 0;
                                $compensate  = $s ? (float)$s->compensate : 0;
                                $winLoss     = $compensate - $net;
                                $outs        = $outstanding->get($member->id);
                                $outstandAmt = $outs ? (float)$outs->total_outstanding : 0;
                            @endphp
                            <tr style="font-size:13px">
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $member->username }}</td>
                                <td>{{ $member->name }}</td>
                                @if (in_array('admin', $roles))
                                    <td>{{ $member->manager?->name ?? '-' }}</td>
                                @endif
                                <td class="text-end fw-bold {{ $balance <= 0 ? 'text-danger' : '' }}">
                                    {{ number_format($balance, 2) }}
                                </td>
                                <td class="text-end {{ $outstandAmt > 0 ? 'text-warning fw-bold' : 'text-muted' }}">
                                    {{ $outstandAmt > 0 ? number_format($outstandAmt, 2) : '-' }}
                                </td>
                                <td class="text-end text-muted">{{ $turnover > 0 ? number_format($turnover, 2) : '-' }}</td>
                                <td class="text-end text-muted">{{ $net > 0 ? number_format($net, 2) : '-' }}</td>
                                <td class="text-end {{ $compensate > 0 ? 'text-success fw-bold' : 'text-muted' }}">
                                    {{ $compensate > 0 ? number_format($compensate, 2) : '-' }}
                                </td>
                                <td class="text-end fw-bold {{ $winLoss > 0 ? 'text-success' : ($winLoss < 0 ? 'text-danger' : 'text-muted') }}">
                                    {{ $s ? number_format($winLoss, 2) : '-' }}
                                </td>
                                <td class="text-center">
                                    @if ($balance > 0)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">No Credit</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown">⚙️</button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a href="#" class="dropdown-item text-success openCreditModal"
                                                    data-bs-toggle="modal" data-bs-target="#creditModal"
                                                    data-type="deposit"
                                                    data-user-id="{{ encrypt($member->id) }}"
                                                    data-name="{{ $member->name }} ({{ $member->username }})"
                                                    data-balance="{{ $balance }}">
                                                    💰 Deposit
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" class="dropdown-item text-danger openCreditModal"
                                                    data-bs-toggle="modal" data-bs-target="#creditModal"
                                                    data-type="withdraw"
                                                    data-user-id="{{ encrypt($member->id) }}"
                                                    data-name="{{ $member->name }} ({{ $member->username }})"
                                                    data-balance="{{ $balance }}">
                                                    💸 Withdraw
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a href="{{ route('admin.credit-kh.history', $member->id) }}"
                                                    class="dropdown-item">
                                                    📋 History
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
            $('#creditTable').DataTable({
                paging: true, searching: true, ordering: true,
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
