<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />

    <div class="bp-wrap">

        <div class="bp-filter">
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                    style="width:160px;" placeholder="{{ __('message.date') }}">
            </div>
            <select id="company" style="width:160px;">
                @foreach ($company as $val)
                    <option value="{{ $val['id'] }}" {{ $company_id == $val['id'] ? 'selected' : '' }}>{{ $val['label'] }}</option>
                @endforeach
            </select>
            <input type="text" id="receipt-no" value="{{ $receiptNo }}" style="width:140px;" placeholder="{{ __('message.receipt_no') }}">
            <input type="text" id="number" value="{{ $number }}" style="width:120px;" placeholder="{{ __('message.number') }}">
            <button class="bp-search-btn" onclick="searchReceipt('{{ route('bet.bet-list') }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m16.5 16.5 5 5"/></svg>
                {{ __('message.search') }}
            </button>
            <button class="bp-search-btn" style="background:#6b7280;" onclick="clearSearch('{{ route('bet.bet-list') }}')">
                {{ __('message.clear') }}
            </button>
        </div>

        <div class="bp-table-wrap" style="padding:0 0 8px;">
            <table class="bp-table">
                <thead>
                    <tr>
                        <th>{{ __('message.no') }}</th>
                        <th>{{ __('message.bet_no') }}</th>
                        <th>{{ __('message.receipt_no') }}</th>
                        <th>{{ __('message.account') }}</th>
                        <th>{{ __('message.date_time') }}</th>
                        <th>{{ __('message.number') }}</th>
                        <th>{{ __('message.digit') }}</th>
                        <th>{{ __('message.game') }}</th>
                        <th>{{ __('message.company') }}</th>
                        <th class="bp-num">{{ __('message.amount') }}</th>
                        <th class="bp-num">{{ __('message.odds') }}</th>
                        <th class="bp-num">{{ __('message.net') }}</th>
                        <th class="bp-num">{{ __('message.turnover') }}</th>
                        <th class="bp-num">{{ __('message.commission') }}</th>
                        <th class="bp-num">{{ __('message.net_amount') }}</th>
                        <th class="bp-num">{{ __('message.win_lose') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data) && count($data))
                        @php
                            $totalTurnover = 0;
                            $totalCommission = 0;
                            $totalNetAmount = 0;
                            $No = 1;
                            $totalWinLose = 0;
                        @endphp
                        @foreach ($data as $row)
                            @foreach ($row->betNumber as $bet)
                                @php
                                    $betNumber = $bet;
                                    $betNumberAmount = 0;
                                    $betNumberGame = '';
                                    if ($betNumber->a_amount > 0)          { $betNumberAmount += $betNumber->a_amount;           $betNumberGame .= 'A'; }
                                    if ($betNumber->b_amount > 0)          { $betNumberAmount += $betNumber->b_amount;           $betNumberGame .= 'B'; }
                                    if ($betNumber->ab_amount > 0)         { $betNumberAmount += $betNumber->ab_amount;          $betNumberGame .= 'A+B'; }
                                    if ($betNumber->roll_amount > 0)       { $betNumberAmount += $betNumber->roll_amount;        $betNumberGame .= 'Roll'; }
                                    if ($betNumber->roll7_amount > 0)      { $betNumberAmount += $betNumber->roll7_amount;       $betNumberGame .= 'Roll7'; }
                                    if ($betNumber->roll_parlay_amount > 0){ $betNumberAmount += $betNumber->roll_parlay_amount; $betNumberGame .= 'Roll Parlay'; }
                                    $commission  = $bet->total_amount - ($bet->total_amount * $row['bePackageConfig']?->rate) / 100;
                                    $netAmount   = ($bet->total_amount * $row['bePackageConfig']?->rate) / 100;
                                    $prizeAmount = $betNumber?->betNumberWin?->betWinning->win_amount ?? 0;
                                    $totalCommission += $commission;
                                    $totalNetAmount  += $netAmount;
                                    $totalTurnover   += $bet->total_amount;
                                    $winLose          = $prizeAmount - $netAmount;
                                    $totalWinLose    += $winLose;
                                @endphp
                                <tr class="{{ $bet->betNumberWin != null ? 'bp-win' : '' }}">
                                    <td class="bp-center">{{ $No++ }}</td>
                                    <td class="bp-center">{{ $row['id'] ?? '' }}</td>
                                    <td class="bp-center">{{ $row->beReceipt->receipt_no }}</td>
                                    <td>{{ $row['user']?->name ?? '' }}</td>
                                    <td>{{ $row['created_at'] ?? '' }}</td>
                                    <td class="bp-center">{{ $bet->generated_number }}</td>
                                    <td class="bp-center">{{ $row['digit_format'] ?? '' }}</td>
                                    <td class="bp-center">{{ $betNumberGame ?? '' }}</td>
                                    <td class="bp-center">{{ $row->betLotterySchedule->province_en }}</td>
                                    <td class="bp-num">{{ number_format($betNumberAmount ?? 0, 2) }}</td>
                                    <td class="bp-num">{{ $row['bePackageConfig']?->price ?? '' }}</td>
                                    <td class="bp-num">{{ number_format($row['bePackageConfig']?->rate ?? 0, 2) }}</td>
                                    <td class="bp-num">{{ $betNumber->total_amount }}</td>
                                    <td class="bp-num">{{ $commission }}</td>
                                    <td class="bp-num">{{ $netAmount }}</td>
                                    <td class="bp-num" style="{{ $winLose < 0 ? 'color:#dc2626;' : '' }}">{{ $winLose }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr>
                            <td colspan="12" style="background:#f8f9fc;border-top:2px solid #e2e8f0;"></td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalTurnover, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCommission, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalNetAmount, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;{{ $totalWinLose < 0 ? 'color:#dc2626;' : '' }}">{{ number_format($totalWinLose, 3, '.', '') }}</td>
                        </tr>
                    @else
                        <tr><td colspan="16" class="bp-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <p>No data found</p>
                        </td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <script src="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <script>
        function searchReceipt(url) {
            const date   = $('#datepicker-receipt').val();
            const no     = $('#receipt-no').val();
            const number = $('#number').val();
            const com_id = $('#company').find(":selected").val();
            if (date.length || no.length) {
                window.location = url + '?date=' + date + '&no=' + no + '&number=' + number + '&com_id=' + com_id;
            }
        }
        function clearSearch(url) { window.location = url; }
    </script>
</x-app-layout>
