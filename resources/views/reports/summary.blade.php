<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet"/>
    <style>

    </style>
    <div class="flex-col bg-white rounded-lg px-4 py-4">
            <div class="flex w-full space-x-2">
                <div class="">
                    <div class="relative max-w-sm">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input id="start-date" value="{{ $start_date }}" datepicker datepicker-buttons datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text" class="border border-gray-600 text-gray-900 rounded focus:ring-blue-500 focus:border-blue-500 block w-full ps-10" placeholder="Select date" >
                    </div>
                </div>
                <div class="">
                    <div class="relative max-w-sm">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input id="end-date" value="{{ $end_date }}" datepicker datepicker-buttons datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text" class="border border-gray-600 text-gray-900 rounded focus:ring-blue-500 focus:border-blue-500 block w-full ps-10" placeholder="Select date" >
                    </div>
                </div>
                <div class="">
                    <button class="w-full flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="searchReceipt('{{ route('reports.summary') }}')">
                        <svg class="size-6" viewBox="-2.64 -2.64 29.28 29.28" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="#ffffff"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M17.0392 15.6244C18.2714 14.084 19.0082 12.1301 19.0082 10.0041C19.0082 5.03127 14.9769 1 10.0041 1C5.03127 1 1 5.03127 1 10.0041C1 14.9769 5.03127 19.0082 10.0041 19.0082C12.1301 19.0082 14.084 18.2714 15.6244 17.0392L21.2921 22.707C21.6828 23.0977 22.3163 23.0977 22.707 22.707C23.0977 22.3163 23.0977 21.6828 22.707 21.2921L17.0392 15.6244ZM10.0041 17.0173C6.1308 17.0173 2.99087 13.8774 2.99087 10.0041C2.99087 6.1308 6.1308 2.99087 10.0041 2.99087C13.8774 2.99087 17.0173 6.1308 17.0173 10.0041C17.0173 13.8774 13.8774 17.0173 10.0041 17.0173Z" fill="#ffffff"></path> </g></svg>
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
                                <th class="py-2 border border-white">{{__('Weekday')}}</th>
                                <th class="py-2 border border-white">{{__('Invoice')}}</th>
                                <th class="py-2 border border-white">{{__('Turnover')}}</th>
                                <th class="py-2 border border-white">{{__('Commission')}}</th>
                                <th class="py-2 border border-white">{{__('Net Amount')}}</th>
                                <th class="py-2 border border-white">{{__('Compensate')}}</th>
                                <th class="py-2 border border-white">{{__('Win/Lose')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($data) && count($data))
                            @php
                                $totalInvoice = 0;
                                $turNover = 0;
                                $totalCompensate = 0;
                                $totalCommission = 0;
                                $totalNetAmount = 0;
                                $totalWinLose = 0;
                            @endphp
                            @foreach($data as $key => $row)
                                @php
                                    $diff = $row->Compensate - $row->NetAmount;
                                    $totalInvoice += (float)($row->total ?? 0);
                                    $turNover += (float)($row->Turnover ?? 0);
                                    $totalCommission += (float)($row->Commission ?? 0);
                                    $totalNetAmount += (float)($row->NetAmount ?? 0);
                                    $totalCompensate += (float)($row->Compensate ?? 0);

                                    $totalWinLose += $diff;
                                @endphp
                                <tr class="border border-gray-300 hover:bg-gray-100">
                                    <td class="py-2 px-1 border border-gray-300">{{ $key + 1 }}</td>
                                    <td class="py-2 px-1 border border-gray-300">{{ $row->date }}</td>
                                    <td class="py-2 px-1 border border-gray-300">{{ $row->draw_day }}</td>
                                    <td class="py-2 px-1 border border-gray-300">{{ $row->total }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{ number_format($row->Turnover, 3, '.', '') }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{ number_format($row->Commission, 3, '.', '') }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300"> {{ number_format($row->NetAmount, 3, '.', '') }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{ number_format($row->Compensate, 3, '.', '') }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">
                                        <span class="{{ $diff < 0 ? 'text-red-500' : 'text-black' }}">
                                            {{ number_format( $diff, 3, '.', '') }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach

                            <tr class="border border-gray-300 hover:bg-gray-100 bg-gray-200 font-bold">
                                <td colspan="3" class="text-center py-2 px-2 border border-gray-300">Total</td>
                                <td class="text-center py-2 px-2 border border-gray-300">{{ $totalInvoice }}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{ number_format( $turNover, 3, '.', '') }}</td>
                                <td class="text-right py-2 px-2 border border-gray-300"> {{ number_format($totalCommission, 3, '.', '') }}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{ number_format($totalNetAmount, 3, '.', '') }}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">{{ number_format($totalCompensate, 3, '.', '') }}</td>
                                <td class="text-right py-2 px-2 border border-gray-300">
                                <span class="{{ $totalWinLose < 0 ? 'text-red-500' : 'text-black' }}">
                                            {{ number_format( $totalWinLose, 3, '.', '') }}
                                        </span></td>
                            </tr>
                        @else
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300 text-center" colspan="9">No data</td>
                            </tr>
                        @endif
                        </tbody>

                    </table>
                </div>
            </div>
    </div>
   

    <script src="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.js') }}" ></script>
    <!-- jQuery -->
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>

<script>
    function searchReceipt(url){
        const start_date = $('#start-date').val();
        const end_date = $('#end-date').val();
        const no = $('#receipt-no').val()
        if(start_date.length || end_date.length || no.length){
            window.location = url +'?start_date='+start_date+'&end_date='+end_date;
        }
    }
</script>
</x-app-layout>