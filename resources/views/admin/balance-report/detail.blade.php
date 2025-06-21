
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
    @section('title', 'Account Details')
    <div class="card">
    <div class="mb-3" style="margin-top: 10px;" >
        @php $active = request('filter'); @endphp
        <b></b>
        <a href="{{ route('admin.balance-report.detail', ['user_id' => $user_id]) }}" 
        class="btn btn-sm {{ $active == '' ? 'btn-primary' : 'btn-outline-primary' }}" >
            All
        </a>
        <a href="{{ route('admin.balance-report.detail', ['user_id' => $user_id, 'filter' => 'today']) }}" 
        class="btn btn-sm {{ $active == 'today' ? 'btn-primary' : 'btn-outline-primary' }}">
            Today
        </a>
        <a href="{{ route('admin.balance-report.detail', ['user_id' => $user_id, 'filter' => 'yesterday']) }}" 
        class="btn btn-sm {{ $active == 'yesterday' ? 'btn-primary' : 'btn-outline-primary' }}">
            Yesterday
        </a>
        <a href="{{ route('admin.balance-report.detail', ['user_id' => $user_id, 'filter' => 'this_week']) }}" 
        class="btn btn-sm {{ $active == 'this_week' ? 'btn-primary' : 'btn-outline-primary' }}">
            This Week
        </a>
        <a href="{{ route('admin.balance-report.detail', ['user_id' => $user_id, 'filter' => 'last_week']) }}" 
        class="btn btn-sm {{ $active == 'last_week' ? 'btn-primary' : 'btn-outline-primary' }}">
            Last Week
        </a>
        <a href="{{ route('admin.balance-report.detail', ['user_id' => $user_id, 'filter' => 'this_month']) }}" 
        class="btn btn-sm {{ $active == 'this_month' ? 'btn-primary' : 'btn-outline-primary' }}">
            This Month
        </a>
        <a href="{{ route('admin.balance-report.detail', ['user_id' => $user_id, 'filter' => 'last_month']) }}" 
        class="btn btn-sm {{ $active == 'last_month' ? 'btn-primary' : 'btn-outline-primary' }}">
            Last Month
        </a>
    </div>
        <div class="card-body">
            <table class="table table-striped" >
                <thead>
                    <tr style="font-size: 12px;">
                        <th>Date</th>
                        <th>Weekday</th>
                        <th>Net W/L</th>
                        <th>Deposit</th>
                        <th>Withdraw</th>
                        <th>Adjustment</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                  
                    @foreach ($data as $key => $user)
                        @php
                            $diff = $user->total_net_win - $user->total_net_lose;
                        @endphp
                      
                            <tr style="font-size: 13px;">
       
                                <td>{{ $user->report_date }}</td>
                                <td>{{ \Carbon\Carbon::parse($user->report_date)->format('l') }}</td>
                                <td>
                                 @if ($diff < 0)
                                    <h6 style="color: red;">{{ number_format( $diff, 3, '.', '') }}</h6>
                                 @else
                                 {{ number_format( $diff, 3, '.', '') }}
                                 @endif
                                </td>
                                <td>{{ $user->total_deposit }}</td>
                                <td>
                                 @if ($user->total_withdraw > 0)
                                    <h6 style="color: red;">- {{ $user->total_withdraw }}</h6>
                                 @else
                                    {{ $user->total_withdraw }}
                                 @endif
                               </td>
                                <td>{{ $user->total_adjustment }}</td>
                                <td>
                                @if ( $user->total_balance < 0)
                                    <h6 style="color: red;">{{ $user->total_balance }}</h6>
                                 @else
                                 <h6 >{{ $user->total_balance }}</h6>
                                 @endif
                               </td>
                              
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-admin>
