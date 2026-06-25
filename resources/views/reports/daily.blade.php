<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />

    <div class="bp-wrap">

        <div class="bp-filter">
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="date" value="{{ $date }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                    style="width:160px;" placeholder="{{ __('message.date') }}">
            </div>
            <select id="company" style="width:160px;">
                @foreach ($company as $val)
                    <option value="{{ $val['id'] }}" @selected($company_id == $val['id'])>{{ $val['label'] }}</option>
                @endforeach
            </select>
            <button class="bp-search-btn" onclick="searchReceipt('{{ route('reports.daily') }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m16.5 16.5 5 5"/></svg>
                {{ __('message.search') }}
            </button>
            <button class="bp-search-btn" style="background:#6b7280;" onclick="clearSearch('{{ route('reports.daily') }}')">
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
                        <th>{{ __('message.account') }}</th>
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
                                $netAmount  = $row->net_amount;
                                $commission = $row->commission;
                                $diff       = $row->Compensate - $netAmount;
                                $totalInvoice    += (float) ($row->total_receipts ?? 0);
                                $turNover        += (float) ($row->total_amount ?? 0);
                                $totalCommission += (float) ($commission ?? 0);
                                $totalNetAmount  += (float) ($netAmount ?? 0);
                                $totalCompensate += (float) ($row->Compensate ?? 0);
                                $totalWinLose    += $diff;
                            @endphp
                            <tr>
                                <td class="bp-center">{{ $key + 1 }}</td>
                                <td>{{ $row->bet_date }}</td>
                                <td>{{ $row->draw_day }}</td>
                                <td>{{ $row->account }}</td>
                                <td class="bp-num">{{ $row->total_receipts }}</td>
                                <td class="bp-num">{{ number_format($row->total_amount, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($commission, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($netAmount, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row->Compensate, 3, '.', '') }}</td>
                                    @php $diffStyle = $diff < 0 ? 'color:#dc2626;' : ''; @endphp
                                <td class="bp-num" style="{{ $diffStyle }}">{{ number_format($diff, 3, '.', '') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="4" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;text-align:center;">Total</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ $totalInvoice }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($turNover, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCommission, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalNetAmount, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCompensate, 3, '.', '') }}</td>
                            @php $twlStyle = $totalWinLose < 0 ? 'color:#dc2626;' : ''; @endphp
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;{{ $twlStyle }}">{{ number_format($totalWinLose, 3, '.', '') }}</td>
                        </tr>
                    @else
                        <tr><td colspan="10" class="bp-empty">
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
            const date   = $('#date').val();
            const com_id = $('#company').find(":selected").val();
            if (date.length) {
                window.location = url + '?date=' + date + '&com_id=' + com_id;
            }
        }
        function clearSearch(url) { window.location = url; }
    </script>
</x-app-layout>
