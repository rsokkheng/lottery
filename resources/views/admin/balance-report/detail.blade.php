
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
        <table class="table table-striped">
            <thead>
                <tr class="bg-gray-100">
                    <th>Report Date</th>
                    <th>Created By</th>
                    <th>Day</th>
                    <th>Net Win</th>
                    <th>Net Lose</th>
                    <th>Net W/L</th>
                    <th>Deposit</th>
                    <th>Withdraw</th>
                    <th>Adjustment</th>
                    <th>Balance</th>
                    <th>Text</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sum_net_win = 0;
                    $sum_net_lose = 0;
                    $sum_diff = 0;
                    $sum_deposit = 0;
                    $sum_withdraw = 0;
                    $sum_adjustment = 0;
                    $sum_balance = 0;
                @endphp

                @foreach ($data as $key => $user)
                    @php
                        $diff = $user->total_net_win - $user->total_net_lose;

                        $sum_net_win += $user->total_net_win;
                        $sum_net_lose += $user->total_net_lose;
                        $sum_diff += $diff;
                        $sum_deposit += $user->total_deposit;
                        $sum_withdraw += $user->total_withdraw;
                        $sum_adjustment += $user->total_adjustment;
                        $sum_balance += $user->total_balance;
                    @endphp

                    <tr style="font-size: 13px;">
                        <td>{{ $user->report_date }}</td>
                        <td>{{ $user->created_by }}</td>
                        <td>{{ \Carbon\Carbon::parse($user->report_date)->format('l') }}</td>
                        <td>{{ number_format($user->total_net_win, 2, '.', '') }}</td>
                        <td style="color: red;">-{{ number_format($user->total_net_lose, 2, '.', '') }}</td>
                        <td>
                            @if ($diff < 0)
                                <h6 style="color: red;">{{ number_format($diff, 2, '.', '') }}</h6>
                            @else
                                {{ number_format($diff, 2, '.', '') }}
                            @endif
                        </td>
                        <td>{{ number_format($user->total_deposit, 2, '.', '') }}</td>
                        <td>
                            @if ($user->total_withdraw > 0)
                                <h6 style="color: red;">-{{ number_format($user->total_withdraw, 2, '.', '') }}</h6>
                            @else
                                {{ number_format($user->total_withdraw, 2, '.', '') }}
                            @endif
                        </td>
                        <td>{{ number_format($user->total_adjustment, 2, '.', '') }}</td>
                        <td>
                            @if ($user->total_balance < 0)
                                <h6 style="color: red;">{{ number_format($user->total_balance, 2, '.', '') }}</h6>
                            @else
                                <h6>{{ number_format($user->total_balance, 2, '.', '') }}</h6>
                            @endif
                        </td>
                        <td>{{ $user->text }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr style="font-size: 13px; font-weight: bold; background-color: #f3f4f6;">
                    <td colspan="3" class="text-center">Total</td>
                    <td>{{ number_format($sum_net_win, 2, '.', '') }}</td>
                    <td style="color: red;">-{{ number_format($sum_net_lose, 2, '.', '') }}</td>
                    <td>
                        @if ($sum_diff < 0)
                            <span style="color: red;">{{ number_format($sum_diff, 2, '.', '') }}</span>
                        @else
                            {{ number_format($sum_diff, 2, '.', '') }}
                        @endif
                    </td>
                    <td>{{ number_format($sum_deposit, 2, '.', '') }}</td>
                    <td>
                        @if ($sum_withdraw > 0)
                            <span style="color: red;">-{{ number_format($sum_withdraw, 2, '.', '') }}</span>
                        @else
                            {{ number_format($sum_withdraw, 2, '.', '') }}
                        @endif
                    </td>
                    <td>{{ number_format($sum_adjustment, 2, '.', '') }}</td>
                    <td>
                        @if ($sum_balance < 0)
                            <span style="color: red;">{{ number_format($sum_balance, 2, '.', '') }}</span>
                        @else
                            {{ number_format($sum_balance, 2, '.', '') }}
                        @endif
                    </td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>

        </div>
    </div>
</x-admin>
