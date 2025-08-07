<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />

    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:flex bg-white rounded-lg px-4 py-4">
        <div class="relative flex items-center w-full lg:w-48">
            <div class="absolute inset-y-0 start-0 flex ps-4 items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                </svg>
            </div>
            <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons
                datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                class="w-full border border-gray-600 text-gray-900 rounded focus:ring-blue-500 focus:border-blue-500 block w-full ps-10"
                placeholder="Select date">
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
            <input type="text" id="receipt-no" value="{{ $receiptNo }}" class="rounded w-full"
                placeholder="Receipt No">
        </div>
        <div class="w-full lg:w-48">
            <input type="text" id="number" value="{{ $number }}" class="rounded w-full"
                placeholder="Number">
        </div>
        <div class="w-full sm:w-16">
            <button
                class="wax-w-auto flex justify-center items-center bg-blue-500 text-white px-2 py-1 sm:py-2 rounded hover:bg-blue-600"
                onclick="searchReceipt('{{ route('bet-usd.bet-list') }}')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <p> {{ __('message.search') }} </p>
            </button>
        </div>
    </div>
    <div class="flex w-full">
        <div class="w-full overflow-auto py-4">
            <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                <thead>
                    <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.no') }}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.bet_no') }}
                        </th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                            {{ __('message.receipt_no') }}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.account') }}
                        </th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">
                            {{ __('message.date_time') }}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.number') }}
                        </th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.digit') }}
                        </th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.game') }}
                        </th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.company') }}
                        </th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.amount') }}
                        </th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.odds') }}
                        </th>
                        <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">{{ __('message.net') }}
                        </th>
                        <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">
                            {{ __('message.turnover') }}</th>
                        <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">
                            {{ __('message.commission') }}</th>
                        <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">
                            {{ __('message.net_amount') }}</th>
                        <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">
                            {{ __('message.win_lose') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data) && count($data))
                        @php
                            $totalTurnover = 0;
                            $totalCommission = 0;
                            $totalNetAmount = 0;
                            $No = 1;
                            $winLose = 0;
                            $totalWinLose = 0;

                        @endphp
                        @foreach ($data as $key => $row)
                            @foreach ($row->betNumberUSD as $bet)
                                @php
                                    $betNumber = $bet;
                                    $betNumberAmount = 0;
                                    $betNumberGame = '';
                                    if ($betNumber->a_amount > 0) {
                                        $betNumberAmount += $betNumber->a_amount;
                                        $betNumberGame .= 'A';
                                    }
                                    if ($betNumber->b_amount > 0) {
                                        $betNumberAmount += $betNumber->b_amount;
                                        $betNumberGame .= 'B';
                                    }
                                    if ($betNumber->ab_amount > 0) {
                                        $betNumberAmount += $betNumber->ab_amount;
                                        $betNumberGame .= 'A+B';
                                    }
                                    if ($betNumber->roll_amount > 0) {
                                        $betNumberAmount += $betNumber->roll_amount;
                                        $betNumberGame .= 'Roll';
                                    }
                                    if ($betNumber->roll7_amount > 0) {
                                        $betNumberAmount += $betNumber->roll7_amount;
                                        $betNumberGame .= 'Roll7';
                                    }
                                    if ($betNumber->roll_parlay_amount > 0) {
                                        $betNumberAmount += $betNumber->roll_parlay_amount;
                                        $betNumberGame .= 'Roll Parlay';
                                    }

                                    $commission =
                                        $bet->total_amount -
                                        ($bet->total_amount * $row['bePackageConfig']?->rate) / 100;
                                    $netAmount = ($bet->total_amount * $row['bePackageConfig']?->rate) / 100;
                                    $prizeAmount = $betNumber?->betNumberWin?->betWinning->win_amount ?? 0;
                                    $totalCommission += $commission;
                                    $totalNetAmount += $netAmount;
                                    $totalTurnover += $bet->total_amount;
                                    $winLose = $prizeAmount - $netAmount;
                                    $totalWinLose += $winLose;

                                @endphp
                                <tr
                                    class="border border-gray-300 hover:bg-gray-100 {{ $bet->betNumberWin != null ? 'bg-red-100 hover:bg-black-200 text-black-500' : '' }}">
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $No++ }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $row['id'] ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $row->beReceiptUSD->receipt_no }}
                                    </td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $row['user']?->name ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $row['created_at'] ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $bet->generated_number }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $row['digit_format'] ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $betNumberGame ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $row->betLotterySchedule->province_en }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ number_format($betNumberAmount ?? 0, 2) }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $row['bePackageConfig']?->price ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ number_format($row['bePackageConfig']?->rate ?? 0, 2) }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $betNumber->total_amount }}</td>
                                    <td
                                        class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $commission }}</td>
                                    <td
                                        class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                        {{ $netAmount }}</td>
                                    <td
                                        class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base {{ $winLose < 0 ? 'text-red-500' : '' }}">
                                        {{ $winLose }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td colspan="12"></td>
                            <td
                                class="text-right py-2 px-1 border font-bold border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                {{ number_format($totalTurnover, 3, '.', '') }} </td>
                            <td
                                class="text-right py-2 px-1 border font-bold border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                {{ number_format($totalCommission, 3, '.', '') }}</td>
                            <td
                                class="text-right py-2 px-1 border font-bold border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                {{ number_format($totalNetAmount, 3, '.', '') }}</td>
                            <td
                                class="text-right py-2 px-1 border font-bold border-gray-300 whitespace-nowrap text-[12px] sm:text-base {{ $totalWinLose < 0 ? 'text-red-500' : '' }}">
                                {{ number_format($totalWinLose, 3, '.', '') }}</td>
                        </tr>
                    @else
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td class="py-2 px-1 border border-gray-300" colspan="16">No data</td>
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
            const com_id = $('#company').find(":selected").val();
            if (date.length || no.length) {
                window.location = url + '?date=' + date + '&no=' + no + '&number=' + number + '&com_id=' + com_id;
            }
        }
    </script>
</x-app-layout>
