<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet"/>
    <style>

    </style>

    {{--    </x-slot>--}}
    <div class="flex-col bg-white rounded-lg px-5 py-5 ">
        <div class="grid grid-cols-2  sm:flex sm:justify-start sm:items-center sm:space-x-2">
            <div class="mr-4">
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
            <div class="">
                <input type="text" id="receipt-no" value="{{ $no }}" class="rounded" placeholder="Receipt No">
            </div>
            <div class="mt-4 sm:mt-0">
                <button class="max-w-auto px-2 py-1 sm:py-2 sm:w-full flex justify-center items-center bg-blue-500 text-white rounded hover:bg-blue-600"
                        onclick="searchReceipt('{{ route('bet.receipt-list') }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                   <p class="text-[12px] sm:text-base">{{__('Search')}}</p>
                </button>
            </div>

        </div>
        <div class="flex w-full">
            <div class="w-full overflow-auto py-4">
                <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                    <thead>
                    <tr class="bg-blue-500 border text-white font-bold text-nowrap ">
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('No')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Receipt No')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Account')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Date')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Currency')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Total Amount')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Commission')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Net Amount')}}</th>
                        <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{__('Compensate')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(isset($data) && count($data))
                        @foreach($data as $key => $row)
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300">{{$key+1}}</td>
                                <td onclick="handleShowBet('{{$row['id']}}')" class="py-2 px-1 border border-gray-300">
                                    <a href="#" data-modal-target="static-modal" data-modal-toggle="static-modal"
                                       class="active text-blue-800 hover:underline whitespace-nowrap text-[12px] sm:text-base" data-toggle="modal"
                                       data-target="#detailModal">{{$row['receipt_no']??''}}</a>
                                </td>
                                <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['user_name']??''}}</td>
                                <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['date']??''}}</td>
                                <td class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['currency']??''}}</td>
                                <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['total_amount']??''}}</td>
                                <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['commission']??''}}</td>
                                <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['net_amount']??''}}</td>
                                <td class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[12px] sm:text-base">{{$row['compensate']??''}}</td>
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
    <!-- Main modal -->
    <div id="static-modal" data-modal-placement="top-left" data-modal-backdrop="static" tabindex="-1" aria-hidden="true"
         class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-3 w-full max-w-2xl max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-sm">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t  border-gray-200">
                    <h6 class="text-xl font-semibold text-gray-900 ">
                        <div class="flex">
                         <p>Receipt No:</p>
                         <p id="receipt_no"></p>
                        </div>
                    </h6>
                    <button type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center "
                            data-modal-hide="static-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
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
                        <tbody id="getBetByReceipt">
                        </tbody>
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <th colspan="2" class="py-2 px-1 border border-gray-300">{{__('Total Amount')}}</th>
                            <th class="py-2 px-1 border border-gray-300" id="totalAmount">0.00</th>
                        </tr>
                        <tr class="border border-gray-300 hover:bg-gray-100">
                            <th colspan="2" class="py-2 px-1 border border-gray-300">{{__('Due Amount')}}</th>
                            <th class="py-2 px-1 border border-gray-300" id="dueAmount">0.00</th>
                        </tr>

                    </table>

                </div>
                <!-- Modal footer -->
                <div class="flex justify-content-end justify-items-end justify-end items-end p-4 md:p-5 border-t border-gray-200 rounded-b font-semibold">
                    <button data-modal-hide="static-modal" type="button" class="text-white bg-blue-800 hover:bg-blue-900 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg px-5 py-2.5 text-center " onclick="printReceipt()" >{{__('Print')}}</button>
                    <button data-modal-hide="static-modal" type="button" class="py-2.5 px-5 text-white bg-sky-400 ms-3 font-medium focus:outline-none rounded-lg border border-gray-200 hover:bg-sky-500 focus:z-10 focus:ring-4 focus:ring-gray-100 ">{{__('Close')}}</button>
                </div>
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
            if (date.length || no.length) {
                window.location = url + '?date=' + date + '&no=' + no;
            }
        }

        function handleShowBet(id) {

            fetch(`/lotto_vn/bet/${id}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Bet details:', data);
                    let totalAmount =data?.totalAmount;
                    let dueAmount = data?.dueAmount;
                     document.getElementById('receipt_no').innerText = data?.no_receipt;

                    const getBetByReceipt = document.getElementById('getBetByReceipt');
                    getBetByReceipt.innerHTML = '';
                    data?.items?.forEach(item => {
                        getBetByReceipt.innerHTML += `
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td class="py-2 px-1 border border-gray-300">${item?.number??""}</td>
                                <td class="py-2 px-1 border border-gray-300">${item?.company??""}</td>
                                <td class="py-2 px-1 border border-gray-300">${item?.amount??""}</td>
                            </tr>`;
                    });
                    // Update Total Amount and Due Amount
                    document.getElementById('totalAmount').innerText = Number(totalAmount).toFixed(2)+ ' (VND)';
                    document.getElementById('dueAmount').innerText = Number(dueAmount).toFixed(2)+ ' (VND)';
                })
                .catch(error => console.error('Error:', error));
        }
        function printReceipt() {
            var receiptNo = document.getElementById('receipt_no')?.innerText;
            if (!receiptNo) {
                alert("Receipt number not found!");
                return;
            }
            var printWindow = window.open('/bet_receipt/' + receiptNo, '_blank');

            if (!printWindow) {
                alert('Popup blocked! Please allow popups for this site.');
                return;
            }

            printWindow.onload = function () {
                setTimeout(() => {
                    printWindow.print();
                    printWindow.onafterprint = function () {
                        printWindow.close();
                    };
                }, 500);
            };
        }
    </script>
</x-app-layout>