<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />

    <div class="bp-wrap">

        <div class="bp-filter">
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="start-date" value="{{ $start_date }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                    style="width:150px;" placeholder="Start date">
            </div>
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="end-date" value="{{ $end_date }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                    style="width:150px;" placeholder="End date">
            </div>
            <button class="bp-search-btn" onclick="searchReceipt('{{ route('bet-usd.reports.summary') }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m16.5 16.5 5 5"/></svg>
                {{ __('message.search') }}
            </button>
            <button class="bp-search-btn" style="background:#6b7280;" onclick="clearSearch('{{ route('bet-usd.reports.summary') }}')">
                {{ __('message.clear') }}
            </button>
        </div>

        <div class="bp-table-wrap" style="padding:0 0 8px;">
            <table class="bp-table">
                <thead>
                    <tr>
                        <th>{{ __('message.no') }}</th>
                        <th>{{ __('message.date') }}</th>
                        <th>{{ __('message.weekday') }}</th>
                        <th class="bp-num">{{ __('message.invoice') }}</th>
                        <th class="bp-num">{{ __('message.turnover') }}</th>
                        <th class="bp-num">{{ __('message.commission') }}</th>
                        <th class="bp-num">{{ __('message.net_amount') }}</th>
                        <th class="bp-num">{{ __('message.compensate') }}</th>
                        <th class="bp-num">{{ __('message.win_lose') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data) && count($data))
                        @php
                            $totalInvoice    = 0;
                            $turNover        = 0;
                            $totalCommission = 0;
                            $totalNetAmount  = 0;
                            $totalCompensate = 0;
                            $totalWinLose    = 0;
                        @endphp
                        @foreach ($data as $key => $row)
                            @php
                                $diff = $row->Compensate - $row->NetAmount;
                                $totalInvoice    += (float) ($row->total ?? 0);
                                $turNover        += (float) ($row->Turnover ?? 0);
                                $totalCommission += (float) ($row->Commission ?? 0);
                                $totalNetAmount  += (float) ($row->NetAmount ?? 0);
                                $totalCompensate += (float) ($row->Compensate ?? 0);
                                $totalWinLose    += $diff;
                                $diffStyle = $diff < 0 ? 'color:#dc2626;' : '';
                            @endphp
                            <tr>
                                <td class="bp-center">{{ $key + 1 }}</td>
                                <td>{{ $row->date }}</td>
                                <td>{{ $row->draw_day }}</td>
                                <td class="bp-num">{{ $row->total }}</td>
                                <td class="bp-num">{{ number_format($row->Turnover, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row->Commission, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row->NetAmount, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row->Compensate, 3, '.', '') }}</td>
                                <td class="bp-num" style="{{ $diffStyle }}">{{ number_format($diff, 3, '.', '') }}</td>
                            </tr>
                        @endforeach
                        @php $twlStyle = $totalWinLose < 0 ? 'color:#dc2626;' : ''; @endphp
                        <tr>
                            <td colspan="3" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;text-align:center;">Total</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ $totalInvoice }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($turNover, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCommission, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalNetAmount, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCompensate, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;{{ $twlStyle }}">{{ number_format($totalWinLose, 3, '.', '') }}</td>
                        </tr>
                    @else
                        <tr><td colspan="9" class="bp-empty">
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
            const start_date = $('#start-date').val();
            const end_date   = $('#end-date').val();
            if (start_date.length || end_date.length) {
                ajaxLoad(url + '?start_date=' + start_date + '&end_date=' + end_date);
            }
        }
        function clearSearch(url) { ajaxLoad(url); }
    </script>
</x-app-layout>
