@php
  $isAdmin = in_array('admin', $roles ?? []);
@endphp

<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet"/>
    <div class="flex-col bg-white rounded-lg px-4 py-4">
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4 lg:flex bg-white rounded-lg">
            @if(Auth::user()->roles->pluck('name')->intersect(['admin', 'manager'])->isNotEmpty())
             <div class="w-full lg:w-48">
             <select id="member" name="member_id" class="rounded w-full">
                <option value="">All Members</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ $member_id == $member->id ? 'selected' : '' }}>
                        {{ $member->name }}
                        @if($isAdmin && $member->manager)
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
                            <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
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
                <select id="digit_type" class="rounded w-full">
                @foreach($digits as $val)
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
                <input type="text" id="number" value="{{ $number }}" class="rounded w-full" placeholder="Number">
            </div>
            <div class="w-full sm:w-16">
                <button class="wax-w-auto flex justify-center items-center bg-blue-500 text-white px-2 py-1 sm:py-2 rounded hover:bg-blue-600"
                        onclick="searchReceipt('{{ route('bet-usd.bet-number') }}')">
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
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Date')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Number')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Digit')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Game')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Company')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Amount')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Odds')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Net')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Turnover')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Commission')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Net Amount')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Win/Lose')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($data) && count($data)>0)
                        @foreach($data as $key=>$betNumber)
                                <tr class="border border-gray-300 hover:bg-gray-100">
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{$key+1}}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{$betNumber->bet_date??''}}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{$betNumber->generated_number??''}}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{$betNumber->digit_format??''}}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{$betNumber->bet_game??''}}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{$betNumber->province_en??''}}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ number_format($betNumber->get_roll_amount?? 0, 2) }}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{$betNumber->price??0}}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{ number_format($betNumber->rate ?? 0, 2) }}</td>
                                    <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{$betNumber->number_turnover?? 0}}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{number_format($betNumber->commission?? 0, 2)}}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas">{{number_format($betNumber->net_amount?? 0, 2)}}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-bas {{number_format($betNumber->win_lose?? 0, 2) < 0 ? 'text-red-500' : ''}}">{{number_format($betNumber->win_lose?? 0, 2)}}</td>
                                </tr>
                        @endforeach
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <td colspan="9"></td>
                            <td class="text-right py-2 px-1 border font-bold border-gray-300">{{ number_format( $totalNetAmount['turnover'], 3, '.', '')}}</td>
                            <td class="text-right py-2 px-1 border font-bold border-gray-300">{{  number_format( $totalNetAmount['commission'], 3, '.', '')}}</td>
                            <td class="text-right py-2 px-1 border font-bold border-gray-300">{{ number_format( $totalNetAmount['net_amount'], 3, '.', '')}}</td>
                            <td class="text-right py-2 px-1 border font-bold border-gray-300 {{ $totalNetAmount['win_lose'] < 0 ? 'text-red-500' : ''}}">{{number_format( $totalNetAmount['win_lose'], 3, '.', '')}}</td>
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
        const member_id = $('#member').val()
        const digit_type = $('#digit_type').val()
        const com_id = $('#company').find(":selected").val();
        if(date.length || no.length){
            window.location = url +'?date='+date+'&no='+no+'&number='+number+'&com_id='+com_id+'&member_id='+member_id+'&digit_type='+digit_type;
        }
    }
</script>
</x-app-layout>