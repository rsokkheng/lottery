@php
    $isAdmin = in_array('admin', $roles ?? []);
@endphp

<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />

    <div class="bp-wrap">
        <div class="bp-page-header">
            <div>
                <span class="bp-badge bp-badge-usd">Lotto Vietnam · USD</span>
                <div class="bp-page-title">{{ __('message.number') }} Detail</div>
            </div>
        </div>

        <div class="bp-filter">
            @if (Auth::user()->roles->pluck('name')->intersect(['admin', 'master', 'agent'])->isNotEmpty())
                <select id="member" name="member_id" style="width:160px;">
                    <option value="">All Members</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}" @selected($member_id == $member->id)>
                            {{ $member->name }}
                            @if ($isAdmin && $member->manager) - {{ $member->manager->name }} @endif
                        </option>
                    @endforeach
                </select>
            @endif
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                    style="width:160px;" placeholder="{{ __('message.date') }}">
            </div>
            <select id="company" style="width:160px;">
                @foreach ($company as $val)
                    <option value="{{ $val['id'] }}" @selected($company_id == $val['id'])>{{ $val['label'] }}</option>
                @endforeach
            </select>
            <select id="digit_type" style="width:100px;">
                @foreach ($digits as $val)
                    @php
                        $isSpecial   = $val['has_special'] == 1 && $val['bet_type'] == 'RP3';
                        $optionValue = $isSpecial ? 'RP3X' : $val['bet_type'];
                    @endphp
                    <option value="{{ $optionValue }}" @selected($digit_type == $optionValue)>{{ $optionValue }}</option>
                @endforeach
            </select>
            <input type="text" id="number" value="{{ $number }}" style="width:120px;" placeholder="{{ __('message.number') }}">
            <button class="bp-search-btn" onclick="searchReceipt('{{ route('bet-usd.bet-number') }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m16.5 16.5 5 5"/></svg>
                {{ __('message.search') }}
            </button>
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
                        <th class="bp-num">{{ __('message.win_lose') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data) && count($data) > 0)
                        @foreach ($data as $key => $betNumber)
                            @php $wlStyle = ($betNumber->win_lose ?? 0) < 0 ? 'color:#dc2626;' : ''; @endphp
                            <tr>
                                <td class="bp-center">{{ $key + 1 }}</td>
                                <td>{{ $betNumber->bet_date ?? '' }}</td>
                                <td class="bp-center">{{ $betNumber->generated_number ?? '' }}</td>
                                <td class="bp-center">{{ $betNumber->digit_format ?? '' }}</td>
                                <td class="bp-center">{{ $betNumber->bet_game ?? '' }}</td>
                                <td class="bp-center">{{ $betNumber->province_en ?? '' }}</td>
                                <td class="bp-num">{{ number_format($betNumber->get_roll_amount ?? 0, 2) }}</td>
                                <td class="bp-num">{{ $betNumber->price ?? 0 }}</td>
                                <td class="bp-num">{{ number_format($betNumber->rate ?? 0, 2) }}</td>
                                <td class="bp-num">{{ $betNumber->number_turnover ?? 0 }}</td>
                                <td class="bp-num">{{ number_format($betNumber->commission ?? 0, 2) }}</td>
                                <td class="bp-num">{{ number_format($betNumber->net_amount ?? 0, 2) }}</td>
                                <td class="bp-num" style="{{ $wlStyle }}">{{ number_format($betNumber->win_lose ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="9" style="background:#f8f9fc;border-top:2px solid #e2e8f0;"></td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalNetAmount['turnover'], 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalNetAmount['commission'], 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalNetAmount['net_amount'], 3, '.', '') }}</td>
                            @php $twlStyle = $totalNetAmount['win_lose'] < 0 ? 'color:#dc2626;' : ''; @endphp
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;{{ $twlStyle }}">{{ number_format($totalNetAmount['win_lose'], 3, '.', '') }}</td>
                        </tr>
                    @else
                        <tr><td colspan="13" class="bp-empty">
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
            const date       = $('#datepicker-receipt').val();
            const no         = $('#receipt-no').val();
            const number     = $('#number').val();
            const member_id  = $('#member').val();
            const digit_type = $('#digit_type').val();
            const com_id     = $('#company').find(":selected").val();
            if (date.length || no.length) {
                window.location = url + '?date=' + date + '&no=' + no + '&number=' + number +
                    '&com_id=' + com_id + '&member_id=' + member_id + '&digit_type=' + digit_type;
            }
        }
    </script>
</x-app-layout>
