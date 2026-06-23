<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />

    @php $currencyLabel = strtoupper(session('currency', 'VND')); @endphp

    <div class="bp-wrap">
        <div class="bp-page-header">
            <div>
                <span class="bp-badge bp-badge-kh">BET KHMER &middot; {{ $currencyLabel }}</span>
                <div class="bp-page-title">{{ __('message.win_lose') }} Report</div>
            </div>
        </div>

        <div class="bp-filter">
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                    style="width:160px;" placeholder="{{ __('message.date') }}">
            </div>
            <select id="company" style="width:160px;">
                @foreach ($companies as $val)
                    <option value="{{ $val['id'] }}" @selected($company == $val['id'])>{{ $val['label'] }}</option>
                @endforeach
            </select>
            <input type="text" id="bet-number" value="{{ $number }}" style="width:140px;" placeholder="{{ __('message.number') }}">
            <button class="bp-search-btn" onclick="searchWinning('{{ route($khRoutePrefix . '.bet-winning') }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m16.5 16.5 5 5"/></svg>
                {{ __('message.search') }}
            </button>
        </div>

        <div class="bp-table-wrap" style="padding:0 0 8px;">
            <table class="bp-table">
                <thead>
                    <tr>
                        <th>{{ __('message.no') }}</th>
                        <th>{{ __('message.receipt_no') }}</th>
                        <th>{{ __('message.account') }}</th>
                        <th>{{ __('message.date_time') }}</th>
                        <th>{{ __('message.bet_no') }}</th>
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
                        <th class="bp-num">{{ __('message.compensate') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data) && count($data))
                        @php $totalCompensate = 0; $totalCommission = 0; $totalNetAmount = 0; @endphp
                        @foreach ($data as $key => $row)
                            <tr>
                                <td class="bp-center">{{ $key + 1 }}</td>
                                <td class="bp-center">{{ $row['receipt_no'] ?? '' }}</td>
                                <td>{{ $row['account'] ?? '' }}</td>
                                <td>{{ $row['bet_date'] ?? '' }}</td>
                                <td class="bp-center">{{ $row['bet_id'] ?? '' }}</td>
                                <td class="bp-center">{{ $row['win_number'] ?? '' }}</td>
                                <td class="bp-center">{{ $row['bet_type'] == 'RP4(x)' ? 'PL2' : $row['bet_type'] }}</td>
                                <td class="bp-center">{{ $row['game'] ?? '' }}</td>
                                <td class="bp-center">{{ $row['company'] ?? '' }}</td>
                                <td class="bp-num">{{ number_format($row['amount'], 2, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row['odds'], 2, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row['net'], 2, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row['turnover'], 2, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row['commission'], 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row['net_amount'], 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row['compensate'], 3, '.', '') }}</td>
                            </tr>
                            @php
                                $totalCommission += (float) ($row['commission'] ?? 0);
                                $totalNetAmount  += (float) ($row['net_amount'] ?? 0);
                                $totalCompensate += (float) ($row['compensate'] ?? 0);
                            @endphp
                        @endforeach
                        <tr>
                            <td colspan="13" style="background:#f8f9fc;border-top:2px solid #e2e8f0;"></td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCommission, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalNetAmount, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCompensate, 3, '.', '') }}</td>
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
        function searchWinning(url) {
            const date    = $('#datepicker-receipt').val();
            const number  = encodeURIComponent($('#bet-number').val());
            const company = $('#company').val();
            if (date.length) {
                window.location = url + '?date=' + date + '&number=' + number + '&company=' + company;
            }
        }
    </script>
</x-app-layout>
