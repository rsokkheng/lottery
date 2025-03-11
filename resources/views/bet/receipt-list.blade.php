<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet"/>
    <style>

    </style>

{{--    </x-slot>--}}
    <div class="flex-col bg-white rounded-lg px-5 py-5">
            <div class="flex w-full space-x-2">
                <div class="">
                    <div class="relative max-w-sm">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/>
                            </svg>
                        </div>
                        <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text" class="border border-gray-600 text-gray-900 rounded focus:ring-blue-500 focus:border-blue-500 block w-full ps-10" placeholder="Select date" >
                    </div>
                </div>
                <div class="">
                    <input type="text" id="receipt-no" value="{{ $no }}" class="rounded" placeholder="Receipt No">
                </div>
                <div class="">
                    <button class="w-full flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600" onclick="searchReceipt('{{ route('bet.receipt-list') }}')">
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
                                <th class="py-2 border border-white">{{__('Receipt No')}}</th>
                                <th class="py-2 border border-white">{{__('Account')}}</th>
                                <th class="py-2 border border-white">{{__('Date')}}</th>
                                <th class="py-2 border border-white">{{__('Currency')}}</th>
                                <th class="py-2 border border-white">{{__('Total Amount')}}</th>
                                <th class="py-2 border border-white">{{__('Commission')}}</th>
                                <th class="py-2 border border-white">{{__('Net Amount')}}</th>
                                <th class="py-2 border border-white">{{__('Compensate')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if(isset($data) && count($data))
                            @foreach($data as $key => $row)
                                <tr class="border border-gray-300 hover:bg-gray-100">
                                    <td class="py-2 px-1 border border-gray-300">{{$key+1}}</td>
                                    <td class="py-2 px-1 border border-gray-300">
                                        <a href="#" data-modal-target="static-modal" data-modal-toggle="static-modal" class="active text-blue-800 hover:underline" data-toggle="modal" data-target="#detailModal">{{$row['receipt_no']??''}}</a>
                                    </td>
                                    <td class="py-2 px-1 border border-gray-300">{{$row['user_name']??''}}</td>
                                    <td class="py-2 px-1 border border-gray-300">{{$row['date']??''}}</td>
                                    <td class="py-2 px-1 border border-gray-300">{{$row['currency']??''}}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{$row['total_amount']??''}}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{$row['commission']??''}}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{$row['net_amount']??''}}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{$row['compensate']??''}}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300" colspan="9">No data</td>
                            </tr>
                        @endif

                        </tbody>
                    </table>
                </div>
            </div>
    </div>


{{--    Code Modal--}}


    <!-- Modal toggle -->
{{--    <button data-modal-target="static-modal" data-modal-toggle="static-modal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center " type="button">--}}
{{--        Toggle modal--}}
{{--    </button>--}}

    <!-- Main modal -->
    <div id="static-modal" data-modal-placement="top-left" data-modal-backdrop="static" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-3 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t  border-gray-200">
                    <h6 class="text-xl font-semibold text-gray-900 ">
                        Receipt No: 1122233
                    </h6>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center " data-modal-hide="static-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4 w-full">
                    <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                        <thead>
                        <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                            <th class="py-2 border border-white">{{__('Number')}}</th>
                            <th class="py-2 border border-white">{{__('Company')}}</th>
                            <th class="py-2 border border-white">{{__('Amount')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300">4053</td>
                                <td class="py-2 px-1 border border-gray-300">TG, KG</td>
                                <td class="py-2 px-1 border border-gray-300">0.5(R)</td>
                            </tr>
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300">4053</td>
                                <td class="py-2 px-1 border border-gray-300">TG, KG</td>
                                <td class="py-2 px-1 border border-gray-300">0.5(R)</td>
                            </tr>
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300">4053</td>
                                <td class="py-2 px-1 border border-gray-300">TG, KG</td>
                                <td class="py-2 px-1 border border-gray-300">0.5(R)</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <!-- Modal footer -->
                <div class="flex justify-content-end justify-items-end justify-end items-end p-4 md:p-5 border-t border-gray-200 rounded-b font-semibold">
                    <button data-modal-hide="static-modal" type="button" class="text-white bg-blue-800 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg px-5 py-2.5 text-center ">{{__('Print')}}</button>
                    <button data-modal-hide="static-modal" type="button" class="py-2.5 px-5 text-white bg-sky-400 ms-3 font-medium focus:outline-none rounded-lg border border-gray-200 hover:bg-sky-500 focus:z-10 focus:ring-4 focus:ring-gray-100 ">{{__('Close')}}</button>
                </div>
            </div>
        </div>
    </div>


    <script src="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.js') }}" ></script>
    <!-- jQuery -->
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>

<script>
    function searchReceipt(url){
        const date = $('#datepicker-receipt').val();
        const no = $('#receipt-no').val()
        console.log(date, no)
        if(date.length || no.length){
            window.location = url +'?date='+date+'&no='+no;
        }
    }
</script>
</x-app-layout>