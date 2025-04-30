<x-admin>
    @section('title', 'Account Balance')
    <div class="card">
       
        <div class="card-body">
            <table class="table table-striped" id="userTable">
                <thead>
                    <tr style="font-size: 14px;">
                        <th>#</th>
                        <th>Name</th>
                        <th>Account ID</th>
                        <th>Beginning</th>
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
                    @foreach ($user->userWallet as $wallet)
                        <tr>
                            <td>{{ $key+1 }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $wallet->beginning }}</td>
                            <td>{{ $wallet->net_win_loss }}</td>
                            <td>{{ $wallet->deposit }}</td>
                            <td>{{ $wallet->withdraw }}</td>
                            <td>{{ $wallet->balance }}</td>
                            <td>{{ $wallet->adjustment }}</td>
                            <td>{{ $wallet->outstanding }}</td>
                            <td>{{ $wallet->status }}</td>
                            
                            <td>
                                <a href="{{ route('admin.user.edit', encrypt($user->id)) }}" class="btn btn-sm btn-primary" style="display: inline-block; margin-right: 5px;">Deposit</a> 
                                <a href="{{ route('admin.user.edit', encrypt($user->id)) }}" class="btn btn-sm btn-danger" style="display: inline-block; margin-right: 5px;">Withdraw</a> 
                              
                            </td>

                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @section('js')
        <script>
            $(function() {
                $('#userTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": false,
                    "responsive": true,
                });
            });
        </script>
    @endsection
</x-admin>
