<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet"/>

    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:flex bg-white rounded-lg px-4 py-4">
        <div class="relative flex items-center w-full lg:w-48">
            <div class="absolute inset-y-0 start-0 flex ps-4 items-center pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                </svg>
            </div>
            <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons
                   datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                   class="w-full border border-gray-600 text-gray-900 rounded focus:ring-blue-500 focus:border-blue-500 block w-full ps-10"
                   placeholder="Select date">
        </div>
        <div class="w-full lg:w-48">
            <select id="company" class="rounded w-full">
                @foreach($company as $val)
                    @if($company_id == $val['id'])
                        <option selected value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                    @else
                        <option value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="w-full lg:w-48">
            <input type="text" id="receipt-no" value="{{ $receiptNo }}" class="rounded w-full" placeholder="Receipt No">
        </div>
        <div class="w-full lg:w-48">
            <input type="text" id="number" value="{{ $number }}" class="rounded w-full" placeholder="Number">
        </div>
        <div class="w-full sm:w-16">
            <button class="wax-w-auto flex justify-center items-center bg-blue-500 text-white px-2 py-1 sm:py-2 rounded hover:bg-blue-600"
                    onclick="searchReceipt('{{ route('bet.bet-list') }}')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                </svg>
                <p> {{__('Search')}} </p>
            </button>
        </div>
    </div>
    <div class="flex w-full">
        <div class="w-full overflow-auto py-4">
            <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                <thead>
                <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('No')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Bet No')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Receipt No')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Account')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Date Time')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Number')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Digit')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Game')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Company')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Amount')}}</th>
                    <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Odds')}}</th>
                    <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">{{__('Net')}}</th>
                    <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">{{__('Turnover')}}</th>
                    <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">{{__('Commission')}}</th>
                    <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">{{__('Net Amount')}}</th>
                    <th class="py-2 border border-white  px-2 text-[12px] sm:text-base">{{__('Win/Lose')}}</th>
                </tr>
                </thead>
                <tbody>
                @if(isset($data) && count($data))
                    @php
                        $totalTurnover =0;
                        $totalCommission=0;
                        $totalNetAmount=0;
                        $No = 1;
                      
                    @endphp
                    @foreach($data as $key => $row)
                          
                          @foreach($row->betNumber as $bet)
                        @php
                            $betNumber =$bet;
                            $betNumberAmount = 0;
                            $betNumberGame ="";
                            if(intval($betNumber->a_amount)>0){
                                $betNumberAmount+=$betNumber->a_amount;
                                $betNumberGame .= "A";
                            }
                            if(intval($betNumber->b_amount)>0){
                                $betNumberAmount+=$betNumber->b_amount;
                                $betNumberGame .= "B";
                            }
                            if(intval($betNumber->ab_amount)>0){
                                $betNumberAmount+=$betNumber->ab_amount;
                                $betNumberGame .= "A+B";
                            }
                            if(intval($betNumber->roll_amount)>0){
                                $betNumberAmount+=$betNumber->roll_amount;
                                $betNumberGame .= "Roll";
                            }
                             if(intval($betNumber->roll7_amount)>0){
                                $betNumberAmount+=$betNumber->roll7_amount;
                                $betNumberGame .= "Roll7";
                            }
                             if(intval($betNumber->roll_parlay_amount)>0){
                                $betNumberAmount+=$betNumber->roll_parlay_amount;
                                $betNumberGame .= "Roll Parlay";
                            }

                            $commission = $betNumber->total_amount-($betNumber->total_amount *$row['bePackageConfig']?->rate/100);
                            $netAmount =$betNumber->total_amount * $row['bePackageConfig']?->rate/100;
                            $totalCommission +=$commission;
                            $totalNetAmount +=$netAmount;
                            $totalTurnover +=$betNumber->total_amount;

                        @endphp
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$No++}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['id']??''}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">
                                {{$row->beReceipt->receipt_no}}
                            </td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['user']?->name??''}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['created_at']??''}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['number_format']??''}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['digit_format']??''}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$betNumberGame??''}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row->betLotterySchedule->province_en}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{ number_format($betNumberAmount ?? 0, 2) }}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['bePackageConfig']?->price??''}}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{ number_format($row['bePackageConfig']?->rate ?? 0, 2) }}</td>
                            <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$betNumber->total_amount}}</td>
                            <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{ $commission}}</td>
                            <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$netAmount}}</td>
                            <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['win_lose']??''}}</td>
                        </tr>
                        @endforeach
                    @endforeach
                    <tr class="border border-gray-300 hover:bg-gray-100">
                        <td colspan="12"></td>
                        <td class="text-right py-2 px-1 border font-bold border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$totalTurnover??'0.000'}}</td>
                        <td class="text-right py-2 px-1 border font-bold border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$totalCommission??'0.00'}}</td>
                        <td class="text-right py-2 px-1 border font-bold border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$totalNetAmount??'0.000'}}</td>
                        <td class="text-right py-2 px-1 border font-bold border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['win_lose']??'0.000'}}</td>
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