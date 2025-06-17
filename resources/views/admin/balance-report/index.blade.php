<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- DataTables CSS (optional) -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
    @media (max-width: 768px) {
        table td, table th {
            font-size: 11px !important;
            white-space: nowrap;
        }

        .btn {
            font-size: 11px;
            padding: 2px 6px;
        }
    }
</style>
<x-admin>
    @section('title', 'Account Balance')
    <div class="card">
       
        <div class="card-body">
            <table class="table table-striped" id="userTable">
                <thead>
                    <tr style="font-size: 12px;">
                        <th>#</th>
                        <th>Name</th>
                        <th>Net W/L</th>
                        <th>Deposit</th>
                        <th>Withdraw</th>
                        <th>Adjustment</th>
                        <th>Balance</th>
                        <th>Outstanding</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                  
                    @foreach ($data as $key => $user)
                        @php
                            $diff = $user->net_win - $user->net_lose;
                        @endphp
                      
                            <tr style="font-size: 13px;">
                                <td>{{ $key+1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>
                                 @if ($diff < 0)
                                    <h6 style="color: red;">{{ number_format( $diff, 3, '.', '') }}</h6>
                                 @else
                                 {{ number_format( $diff, 3, '.', '') }}
                                 @endif
                                </td>
                                <td>{{ $user->deposit }}</td>
                                <td>
                                 @if ($user->withdraw > 0)
                                    <h6 style="color: red;">- {{ $user->withdraw }}</h6>
                                 @else
                                    {{ $user->withdraw }}
                                 @endif
                               </td>
                                <td>{{ $user->adjustment }}</td>
                                <td>
                                @if ( $user->balance < 0)
                                    <h6 style="color: red;">{{ $user->balance }}</h6>
                                 @else
                                 <h6 >{{ $user->balance }}</h6>
                                 @endif
                               </td>
                                <td style="color: blue;">{{ $user->outstanding }}</td>
                                <td>
                                @if ($user->record_status_id == 1)
                                    <h6 style="color: blue;">Active</h6>
                                 @else
                                 <h6 style="color: red;">Suspend</h6>
                                 @endif
                                 </td>
                                 <td>
                                    <button 
                                        class="btn btn-sm btn-primary openModal" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#transactionModal"
                                        data-type="deposit"
                                        data-name="{{ $user->name }}"
                                        data-balance-amount="{{ $user->balance }}"
                                        data-id="{{ encrypt($user->user_id) }}"
                                        data-withdraw-max="{{ $user->withdraw_max }}"
                                        data-balance-account-id="{{ encrypt($user->balance_account_id) }}"
                                    >Deposit</button>

                                    <button 
                                        class="btn btn-sm btn-danger openModal" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#transactionModal"
                                        data-type="withdraw"
                                        data-name="{{ $user->name }}"
                                        data-balance-amount="{{ $user->balance }}"
                                        data-id="{{ encrypt($user->user_id) }}"
                                        data-withdraw-max="{{ $user->withdraw_max }}"
                                        data-balance-account-id="{{ encrypt($user->balance_account_id) }}"
                                    >Withdraw</button>
                                </td>

                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
  <div class="modal-dialog">
  <form method="POST" action="{{ route('admin.balance-report.transaction') }}">
      @csrf
      <input type="hidden" name="user_id" id="modal-user-id">
      <input type="hidden" name="balance_account_id" id="modal-balance-account-id">
      <input type="hidden" name="transaction_type" id="modal-transaction-type">
      <input type="hidden" name="name_user" id="modal-name-user">
     
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="transactionModalLabel">Transaction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        
        <div class="modal-body">
          <div class="mb-3">
            <label for="modal-password" class="form-label">Verify Password</label>
            <input type="password" class="form-control" name="password" id="modal-password" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Member Name</label>
            <input type="text" class="form-control" id="modal-member-name" readonly>
          </div>


          <div class="mb-3">
            <label for="modal-amount" class="form-label">Current Balance</label>
            <input type="number" class="form-control"  id="modal-current-amount" readonly>
          </div>


          <div class="mb-3">
            <label for="modal-amount" class="form-label">Amount <span id="modal-withdraw-deposit" ></span></label>
            <input type="number" class="form-control" name="amount" id="amount" required>
            <small id="amount-error" class="text-danger d-none">Amount exceeds allowed maximum.</small>
          </div>

          <div class="mb-3">
            <label for="modal-remark" class="form-label">Remark</label>
            <textarea class="form-control" name="remark" id="modal-remark" rows="2"></textarea>
          </div>
        </div>
        
        <div class="modal-footer">
          <button type="submit" id="btn-save" class="btn btn-success">Submit</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>

    @section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let maxAmount = 0;
        $(function () {
    // Initialize DataTable
    $('#userTable').DataTable({
        paging: true,
        searching: true,
        ordering: false,
        responsive: true,
    });

    // Handle modal open and populate form
    $('.openModal').on('click', function () {
        const type = $(this).data('type');
        const name = $(this).data('name');
        const userId = $(this).data('id');
        const balanceAccount = $(this).data('balance-account-id');
        const balanceAmount = $(this).data('balance-amount');
        const withdrawMax = $(this).data('withdraw-max');
        const depositMax = $(this).data('deposit-max');

        // Set modal title
        $('#transactionModalLabel').text(
            type === 'deposit'
                ? 'Deposit Account: ' + name
                : 'Withdraw Account: ' + name
        );

        // Populate hidden fields
        $('#modal-transaction-type').val(type);
        $('#modal-user-id').val(userId);
        $('#modal-balance-account-id').val(balanceAccount);
        $('#modal-member-name').val(name);
        $('#modal-name-user').val(name);
        $('#modal-current-amount').val(balanceAmount);

        // Set max values on the amount input
        $('#amount')
            .data('deposit-max', depositMax)
            .data('withdraw-max', withdrawMax)
            .val(''); // clear previous input

        // Update max info label
        const labelText = type === 'deposit' ? `Max: ${depositMax}` : `Max: ${withdrawMax}`;
        $('#modal-withdraw-deposit').text(labelText);

        // Hide error message on open
        $('#amount-error').addClass('d-none');
    });

    // Validate amount input against max
    $('#amount').on('input', function () {
        const enteredAmount = parseFloat($(this).val()) || 0;
        const type = $('#modal-transaction-type').val();
        const maxAmount = type === 'deposit'
            ? $(this).data('deposit-max')
            : $(this).data('withdraw-max');

        if (enteredAmount > maxAmount) {
            $('#amount-error').removeClass('d-none');
            $('#btn-save').hide();
        } else {
            $('#amount-error').addClass('d-none');
            $('#btn-save').show();
        }
    });
});

</script>
    @endsection
</x-admin>
