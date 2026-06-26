@php $isAdmin = in_array('admin', $roles ?? []); @endphp

<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />

    <div class="bp-wrap">
        <div class="bp-filter">
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="startDate" value="{{ $startDate }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd"
                    style="width:150px;" placeholder="{{ __('message.start_date') }}">
            </div>
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="endDate" value="{{ $endDate }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd"
                    style="width:150px;" placeholder="{{ __('message.end_date') }}">
            </div>
            <select id="company" style="width:160px;">
                @foreach ($company as $val)
                    <option value="{{ $val['id'] }}" {{ $company_id == $val['id'] ? 'selected' : '' }}>{{ $val['label'] }}</option>
                @endforeach
            </select>
            <button class="bp-search-btn" onclick="applyDateFilter()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m16.5 16.5 5 5"/></svg>
                {{ __('message.search') }}
            </button>
            <button class="bp-search-btn" style="background:#6b7280;" onclick="clearDateFilter()">
                {{ __('message.clear') }}
            </button>
            <a href="javascript:history.back()"
                style="display:inline-flex;align-items:center;gap:6px;background:#dc2626;color:#fff;padding:5px 14px;border-radius:7px;font-size:.82rem;font-weight:700;text-decoration:none;">
                ← {{ __('message.back') }}
            </a>
        </div>

        <div class="bp-table-wrap" style="padding:0 0 8px;">
            <table class="bp-table">
                <thead>
                    <tr>
                        <th>{{ __('message.no') }}</th>
                        <th>{{ __('message.date') }}</th>
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
                        <th class="bp-num">{{ __('message.win_lose') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data) && count($data) > 0)
                        @foreach ($data as $key => $betNumber)
                            <tr style="{{ $betNumber->compensate > 0 ? 'background:#fef2f2;' : '' }}">
                                <td class="bp-center">{{ $key + 1 }}</td>
                                <td class="bp-center" style="white-space:nowrap;">{{ $betNumber->created_at ?? '' }}</td>
                                <td class="bp-center" style="font-weight:700;">{{ $betNumber->generated_number ?? '' }}</td>
                                <td class="bp-center">{{ $betNumber->digit_format ?? '' }}</td>
                                <td class="bp-center">{{ $betNumber->bet_game ?? '' }}</td>
                                <td class="bp-center">{{ $betNumber->province_en ?? '' }}</td>
                                <td class="bp-num">{{ number_format($betNumber->get_roll_amount ?? 0, 2) }}</td>
                                <td class="bp-num">{{ $betNumber->price ?? 0 }}</td>
                                <td class="bp-num">{{ number_format($betNumber->rate ?? 0, 2) }}</td>
                                <td class="bp-num">{{ $betNumber->number_turnover ?? 0 }}</td>
                                <td class="bp-num">{{ number_format($betNumber->commission ?? 0, 2) }}</td>
                                <td class="bp-num">{{ number_format($betNumber->net_amount ?? 0, 2) }}</td>
                                <td class="bp-num">{{ number_format($betNumber->compensate ?? 0, 2) }}</td>
                                @php $wlnc = ($betNumber->win_lose ?? 0) < 0 ? 'bp-neg' : ''; @endphp
                                <td class="bp-num {{ $wlnc }}">{{ number_format($betNumber->win_lose ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr style="background:#f8f9fc;font-weight:700;border-top:2px solid #e2e8f0;">
                            <td colspan="9"></td>
                            <td class="bp-num">{{ number_format($totalNetAmount['turnover'], 3, '.', '') }}</td>
                            <td class="bp-num">{{ number_format($totalNetAmount['commission'], 3, '.', '') }}</td>
                            <td class="bp-num">{{ number_format($totalNetAmount['net_amount'], 3, '.', '') }}</td>
                            <td class="bp-num">{{ number_format($totalNetAmount['compensate'], 3, '.', '') }}</td>
                            <td class="bp-num" style="{{ $totalNetAmount['win_lose'] < 0 ? 'color:#dc2626;' : '' }}">
                                {{ number_format($totalNetAmount['win_lose'], 3, '.', '') }}
                            </td>
                        </tr>
                    @else
                        <tr><td colspan="14" class="bp-empty">
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
        function applyDateFilter() {
            const start  = document.getElementById('startDate').value;
            const end    = document.getElementById('endDate').value;
            const com_id = $('#company').find(':selected').val();
            if (start && end) {
                const url = new URL(window.location.href);
                url.searchParams.set('startDate', start);
                url.searchParams.set('endDate', end);
                url.searchParams.set('com_id', com_id);
                url.searchParams.delete('date');
                ajaxLoad(url.toString());
            } else {
                alert('Please select both start and end dates.');
            }
        }
        function clearDateFilter() {
            const url = new URL(window.location.href);
            url.searchParams.delete('startDate');
            url.searchParams.delete('endDate');
            url.searchParams.delete('com_id');
            url.searchParams.delete('date');
            ajaxLoad(url.toString());
        }
    </script>
</x-app-layout>
