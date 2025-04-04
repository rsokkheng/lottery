<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet"/>
    <style>

    </style>
    <div class="flex-col bg-white rounded-lg px-5 py-5">
        <div class="flex w-full space-x-2">
            <div class="">
                <div class="relative max-w-sm">
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
            <div>
                <select id="company" class="rounded">
                    @foreach($company as $val)
                        @if($company_id == $val['id'])
                            <option selected value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                        @else
                            <option value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <select class="rounded">
                    <option value="0">All package</option>
                    @foreach($digits as $val)
                        <option value="{{ $val['id'] }}">{{ $val['bet_type']}}</option>
                    @endforeach
                </select>
            </div>
            <div class="">
                <input type="text" id="number" value="{{ $number }}" class="rounded" placeholder="Number">
            </div>
            <div class="">
                <button class="w-full flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        onclick="searchReceipt('{{ route('bet.bet-number') }}')">
                    <svg class="si
                        cze-6" viewBox="-2.64 -2.64 29.28 29.28" fill="none" xmlns="http://www.w3.org/2000/svg"
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
            <div class="w-full overflow-auto py-4">
                <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                    <thead>
                    <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                        <th class="py-2 border border-white">{{__('No')}}</th>
                        <th class="py-2 border border-white">{{__('Date')}}</th>
                        <th class="py-2 border border-white">{{__('Number')}}</th>
                        <th class="py-2 border border-white">{{__('Digit')}}</th>
                        <th class="py-2 border border-white">{{__('Game')}}</th>
                        <th class="py-2 border border-white">{{__('Company')}}</th>
                        <th class="py-2 border border-white">{{__('Amount')}}</th>
                        <th class="py-2 border border-white">{{__('Odds')}}</th>
                        <th class="py-2 border border-white">{{__('Net')}}</th>
                        <th class="py-2 border border-white">{{__('Turnover')}}</th>
                        <th class="py-2 border border-white">{{__('Commission')}}</th>
                        <th class="py-2 border border-white">{{__('Net Amount')}}</th>
                        <th class="py-2 border border-white">{{__('Win/Lose')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($data) && count($data)>0)
                        @php
                        $totalTurnover =0;
                        $totalCommission=0;
                        $totalNetAmount=0;
                        @endphp
                        @foreach($data as $key => $row)
                            @php
                                $betNumber =$row->betNumber[0];
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
                                    $betNumberGame .= "AB";
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
                                 $commission =$row['total_amount']-($row['total_amount'] *$row['bePackageConfig']?->rate/100);
                                 $netAmount =$row['total_amount'] *$row['bePackageConfig']?->rate/100;
                                 $totalCommission +=$commission;
                                 $totalNetAmount +=$netAmount;
                                 $totalTurnover +=$row['total_amount'];
                            @endphp
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300">{{$key+1}}</td>
                                <td class="py-2 px-1 border border-gray-300">{{$row['bet_date']??''}}</td>
                                <td class="py-2 px-1 border border-gray-300">{{$row['number_format']??''}}</td>
                                <td class="py-2 px-1 border border-gray-300">{{$row['digit_format']??''}}</td>
                                <td class="py-2 px-1 border border-gray-300">{{$betNumberGame??''}}</td>
                                <td class="py-2 px-1 border border-gray-300">{{$row->betLotterySchedule->province_en}}</td>
                                <td class="py-2 px-1 border border-gray-300">{{ number_format($betNumberAmount ?? 0, 2) }}</td>
                                <td class="py-2 px-1 border border-gray-300">{{$row['bePackageConfig']?->price??''}}</td>
                                <td class="py-2 px-1 border border-gray-300">{{ number_format($row['bePackageConfig']?->rate ?? 0, 2) }}</td>
                                <td class="py-2 px-1 border border-gray-300">{{$row['total_amount']??''}}</td>
                                <td class="text-right py-2 px-1 border border-gray-300">{{$commission}}</td>
                                <td class="text-right py-2 px-1 border border-gray-300">{{$netAmount}}</td>
                                <td class="text-right py-2 px-1 border border-gray-300">{{$row['win_lose']??''}}</td>

                            </tr>
                        @endforeach
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td colspan="9"></td>
                            <td class="text-right py-2 px-1 border font-bold border-gray-300">{{$totalTurnover??'0.000'}}</td>
                            <td class="text-right py-2 px-1 border font-bold border-gray-300">{{$totalCommission??'0.00'}}</td>
                            <td class="text-right py-2 px-1 border font-bold border-gray-300">{{$totalNetAmount??'0.000'}}</td>
                            <td class="text-right py-2 px-1 border font-bold border-gray-300">{{$row['win_lose']??'0.000'}}</td>
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
    function searchReceipt(url){
        const date = $('#datepicker-receipt').val();
        const no = $('#receipt-no').val()
        const number = $('#number').val()
        const com_id = $('#company').find(":selected").val();
        if(date.length || no.length){
            window.location = url +'?date='+date+'&no='+no+'&number='+number+'&com_id='+com_id;
        }
    }
</script>
</x-app-layout>