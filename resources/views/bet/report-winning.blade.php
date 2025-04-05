<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet"/>
    <style>

    </style>

    {{--    </x-slot>--}}
    <div class="flex-col bg-white rounded-lg px-5 py-5">
        <div class="flex lg:w-2/4 md:w-2/3 max-sm:w-full max-sm:flex-col gap-4">
            <div class="flex w-full">
                <div class="relative w-full">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                             fill="currentColor" viewBox="0 0 20 20">
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                        </svg>
                    </div>
                    <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons
                           datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                           class="border border-gray-600 text-gray-900 rounded focus:ring-blue-500 focus:border-blue-500 block w-full ps-10"
                           placeholder="Select date">
                </div>
            </div>
            <div class="flex w-full">
                <select id="company" class=" w-full rounded">
                    @foreach($companies as $val)
                        @if($company == $val['id'])
                            <option selected value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                        @else
                            <option value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="flex w-full">
                <input type="text" id="bet-number" value="{{ $number }}" class="w-full rounded" placeholder="Bet number">
            </div>
            <div class="flex w-full">
                <button class="w-full flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        onclick="searchWinning('{{ route('bet.bet-winning') }}')">
                    <svg class="size-6" viewBox="-2.64 -2.64 29.28 29.28" fill="none" xmlns="http://www.w3.org/2000/svg"
                         stroke="#ffffff">
                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                        <g id="SVGRepo_iconCarrier">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                  d="M17.0392 15.6244C18.2714 14.084 19.0082 12.1301 19.0082 10.0041C19.0082 5.03127 14.9769 1 10.0041 1C5.03127 1 1 5.03127 1 10.0041C1 14.9769 5.03127 19.0082 10.0041 19.0082C12.1301 19.0082 14.084 18.2714 15.6244 17.0392L21.2921 22.707C21.6828 23.0977 22.3163 23.0977 22.707 22.707C23.0977 22.3163 23.0977 21.6828 22.707 21.2921L17.0392 15.6244ZM10.0041 17.0173C6.1308 17.0173 2.99087 13.8774 2.99087 10.0041C2.99087 6.1308 6.1308 2.99087 10.0041 2.99087C13.8774 2.99087 17.0173 6.1308 17.0173 10.0041C17.0173 13.8774 13.8774 17.0173 10.0041 17.0173Z"
                                  fill="#ffffff"></path>
                        </g>
                    </svg>
                    {{__('Search')}}
                </button>
            </div>

        </div>
        <div class="flex w-full">
            <div class="w-full overflow-auto overflow-x-auto py-4">
                <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                    <thead>
                    <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                        <th class="py-2 px-2 border border-white">{{__('No')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Receipt No')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Account')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Date Time')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Bet No')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Number')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Digit')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Game')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Company')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Amount')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Odds')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Net')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Turnover')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Commission')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Net Amount')}}</th>
                        <th class="py-2 px-2 border border-white">{{__('Compensate')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($data) && count($data))
                        @php
                            $totalCompensate =0;
                            $totalCommission=0;
                            $totalNetAmount=0;
                        @endphp
                        @foreach($data as $key => $row)
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-2 border border-gray-300">{{$key+1}}</td>
                                <td class="py-2 px-2 border border-gray-300">{{$row['receipt_no']??''}}</td>
                                <td class="py-2 px-2 border border-gray-300">{{$row['account']??''}}</td>
                                <td class="py-2 px-2 border border-gray-300">{{$row['bet_date']??''}}</td>
                                <td class="py-2 px-2 border border-gray-300">{{$row['bet_no']??''}}</td>
                                <td class="py-2 px-2 border border-gray-300">{{$row['win_number']??''}}</td>
                                <td class="py-2 px-2 border border-gray-300">{{$row['bet_type']??''}}</td>
                                <td class="py-2 px-2 border border-gray-300">
                                   @php echo $row['game']??''; @endphp
                                </td>
                                <td class="py-2 px-1 border border-gray-300">{{$row['company']??''}}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{$row['amount']??0}}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{$row['odds']??0}}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{$row['net']??0}}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{$row['turnover']??0}}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{$row['commission']??0}}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{$row['net_amount']??0}}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{$row['compensate']??0}}</td>
                            </tr>
                            @php
                                $totalCommission += (float)$row['commission']??0;
                                $totalNetAmount += (float)$row['net_amount']??0;
                                $totalCompensate += (float)$row['compensate']??0;
                            @endphp
                        @endforeach
                        <tr class="border border-gray-300  hover:bg-gray-100">
                            <td colspan="13"></td>
                            <td class="text-right py-2 px-2 border bg-gray-200 font-bold border-gray-300">{{$totalCommission??'0.00'}}</td>
                            <td class="text-right py-2 px-2 border bg-gray-200 font-bold border-gray-300">{{$totalNetAmount??'0.000'}}</td>
                            <td class="text-right py-2 px-2 border bg-gray-200 font-bold border-gray-300">{{$totalCompensate??'0.000'}}</td>
                        </tr>
                    @else
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td class="py-2 px-2 border border-gray-300" colspan="16">No data</td>
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
        function searchWinning(url) {
            const date = $('#datepicker-receipt').val();
            const number = $('#bet-number').val()
            const numberEncoded = encodeURIComponent(number); // "12%2324%2334"
            const company = $('#company').val()
            if (date.length || no.length) {
                window.location = url + '?date=' + date + '&number=' + numberEncoded + '&company=' + company;
            }
        }
        
    </script>
</x-app-layout>