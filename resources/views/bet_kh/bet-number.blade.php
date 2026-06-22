@php
    $isAdmin = in_array('admin', $roles ?? []);
@endphp

<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />
    <div class="flex-col bg-white rounded-lg px-4 py-4">
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:flex bg-white rounded-lg">
            @if (Auth::user()->roles->pluck('name')->intersect(['admin', 'master', 'agent'])->isNotEmpty())
                <div class="w-full lg:w-48">
                    <select id="member" name="member_id" class="rounded w-full">
                        <option value="">All Members</option>
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" {{ $member_id == $member->id ? 'selected' : '' }}>
                                {{ $member->name }}
                                @if ($isAdmin && $member->manager)
                                    - {{ $member->manager->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="w-full lg:w-48">
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                        </svg>

                    </div>
                    <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons
                        datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                        class="border border-gray-600 text-gray-900 rounded focus:ring-blue-500 focus:border-blue-500 block w-full ps-10"
                        placeholder="Select date">
                </div>
            </div>

            <div class="w-full lg:w-48">
                <select id="company" class="rounded w-full">
                    @foreach ($company as $val)
                        @if ($company_id == $val['id'])
                            <option selected value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                        @else
                            <option value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="w-full lg:w-48">
                <select id="digit_type" class="rounded w-full">
                    @foreach ($digits as $val)
                        @php
                            $isSpecial = $val['has_special'] == 1 && $val['bet_type'] == 'RP3';
                            $optionValue = $isSpecial ? 'RP3X' : $val['bet_type'];
                        @endphp

                        <option value="{{ $optionValue }}" {{ $digit_type == $optionValue ? 'selected' : '' }}>
                            {{ $optionValue }}
                        </option>
                    @endforeach

                </select>
            </div>
            <div class="w-full lg:w-48">
                <input type="text" id="number" value="{{ $number }}" class="rounded w-full"
                    placeholder="{{ __('message.number') }}">
            </div>
            <div class="w-full sm:w-16">
                <button
                    class="wax-w-auto flex justify-center items-center bg-blue-500 text-white px-2 py-1 sm:py-2 rounded hover:bg-blue-600"
                    onclick="searchReceipt('{{ route('bet-kh.bet-number') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <p class="whitespace-nowrap"> {{ __('message.search') }} </p>
                </button>
            </div>


        </div>
        <div class="flex w-full">
            <div class="w-full overflow-auto py-4">
                <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                    <thead>
                        <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.no') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.date') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                                {{ __('message.number') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                                {{ __('message.digit') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.game') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                                {{ __('message.company') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                                {{ __('message.amount') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.ddds') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.net') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                                {{ __('message.turnover') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                                {{ __('message.commission') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                                {{ __('message.net_amount') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                                {{ __('message.win_lose') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data) && count($data) > 0)
                            @php $rowNo = 1; @endphp
                            @foreach ($data as $key => $betNumber)
                                @php
                                    $rate      = $betNumber->rate ?? 0;
                                    $winAmount = $betNumber->total_amount_number_win ?? 0;
                                    $isHN      = ($betNumber->code ?? '') === 'HN';
                                    $digitLen  = (int) filter_var($betNumber->digit_format ?? '2D', FILTER_SANITIZE_NUMBER_INT);
                                    $gameTypes = array_filter([
                                        'A'     => (float)($betNumber->a_amount ?? 0),
                                        'B'     => (float)($betNumber->b_amount ?? 0),
                                        'C'     => (float)($betNumber->c_amount ?? 0),
                                        'D'     => (float)($betNumber->d_amount ?? 0),
                                        'ABCD'  => (float)($betNumber->abcd_amount ?? 0),
                                        'Roll'  => (float)($betNumber->roll_amount ?? 0),
                                        'Roll2' => (float)($betNumber->roll2_amount ?? 0),
                                        'RP'    => (float)($betNumber->roll_parlay_amount ?? 0),
                                    ], fn($v) => $v > 0);
                                    $isFirst = true;
                                @endphp
                                @foreach ($gameTypes as $gameLabel => $gameAmount)
                                    @php
                                        $multiplier = match ($gameLabel) {
                                            'A'    => $isHN ? 4 : 1,
                                            'ABCD' => $isHN ? ($digitLen == 3 ? 6 : 7) : 4,
                                            'Roll' => $isHN
                                                ? ($digitLen == 2 ? 32 : ($digitLen == 3 ? 25 : 19))
                                                : ($digitLen == 2 ? 23 : ($digitLen == 3 ? 19 : 12)),
                                            'Roll2' => $isHN ? 1 : 7,
                                            default => 1,
                                        };
                                        $calcAmount  = $gameAmount * $multiplier;
                                        $gCommission = $calcAmount - ($calcAmount * $rate / 100);
                                        $gNetAmount  = $calcAmount * $rate / 100;
                                        $gWinLose    = ($isFirst ? $winAmount : 0) - $gNetAmount;
                                        $isFirst     = false;
                                    @endphp
                                    <tr class="border border-gray-300 hover:bg-gray-100">
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ $rowNo++ }}</td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ $betNumber->bet_date ?? '' }}</td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ $betNumber->generated_number ?? '' }}</td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ $betNumber->digit_format ?? '' }}</td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas font-medium">{{ $gameLabel }}</td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">
                                            {{ match((int)($betNumber->company_id ?? 0)) { 1 => '4PM', 2 => '5PM', 3 => '6PM', default => '-' } }}
                                        </td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ number_format($calcAmount, 2) }}</td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ $betNumber->price ?? 0 }}</td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ number_format($rate, 2) }}</td>
                                        <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ number_format($calcAmount, 2) }}</td>
                                        <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ number_format($gCommission, 2) }}</td>
                                        <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ number_format($gNetAmount, 2) }}</td>
                                        <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas {{ $gWinLose < 0 ? 'text-red-500' : '' }}">{{ number_format($gWinLose, 2) }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td colspan="9"></td>
                                <td class="text-right py-2 px-1 border font-bold border-gray-300">
                                    {{ number_format($totalNetAmount['turnover'], 3, '.', '') }}</td>
                                <td class="text-right py-2 px-1 border font-bold border-gray-300">
                                    {{ number_format($totalNetAmount['commission'], 3, '.', '') }}</td>
                                <td class="text-right py-2 px-1 border font-bold border-gray-300">
                                    {{ number_format($totalNetAmount['net_amount'], 3, '.', '') }}</td>
                                <td
                                    class="text-right py-2 px-1 border font-bold border-gray-300 {{ $totalNetAmount['win_lose'] < 0 ? 'text-red-500' : '' }}">
                                    {{ number_format($totalNetAmount['win_lose'], 3, '.', '') }}</td>
                            </tr>
                        @else
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300" colspan="15">No data</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <script src="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.js') }}"></script>
    <!-- jQuery -->
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>

    <script>
        function searchReceipt(url) {
            const date = $('#datepicker-receipt').val();
            const no = $('#receipt-no').val()
            const number = $('#number').val()
            const member_id = $('#member').val()
            const digit_type = $('#digit_type').val()
            const com_id = $('#company').find(":selected").val();
            if (date.length || no.length) {
                window.location = url + '?date=' + date + '&no=' + no + '&number=' + number + '&com_id=' + com_id +
                    '&member_id=' + member_id + '&digit_type=' + digit_type;
            }
        }
    </script>
</x-app-layout>
