<x-app-layout>
    {{--    <x-slot name="header">--}}
    {{--        <h2 class="font-semibold text-xl text-gray-800 leading-tight">--}}
    {{--            {{ __('Bet') }}--}}
    {{--        </h2>--}}
    {{--    </x-slot>--}}

    <body class="bg-gray-900 p-4 ">
    <div class="grid grid-cols-[20%_78%] gap-4 mx-4 mx-auto bg-white shadow-md py-4 rounded-lg">
        <div class="w-full px-2">
            <div class="mb-6">
                <div class="w-full max-w-md mx-auto  rounded-lg">
                    <div class="text-center font-bold py-2">
                        Day 18 Month 01 Year 2025
                    </div>
                    <table class="w-full text-sm border-collapse">
                        <thead>
                        <tr>
                            <th class="border border-gray-500 px-4 py-2 text-left">Number</th>
                            <th class="border border-gray-500 px-4 py-2 text-left">Channel</th>
                            <th class="border border-gray-500 px-4 py-2 text-left">Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="3" class="border border-gray-500 px-4 py-2 text-center">
                                Không Có Số
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-gray-500 px-4 py-2 font-bold">
                                Total Amount
                            </td>
                            <td class="border border-gray-500 px-4 py-2 text-right">0 (VND)</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-gray-500 px-4 py-2 font-bold">
                                Total Due
                            </td>
                            <td class="border border-gray-500 px-4 py-2 text-right">0 (VND)</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- Note Section -->
            <div class="mb-4">
                <div class="flex pb-2 items-center">
                    <div class="px-2 text-[12px]">Chi Chu:</div>
                    <div>
                        <span class="border border-gray-500 text-[12px]  px-1 py-1">2so x 75</span>
                        <span class="border border-gray-500 text-[12px]  px-1 py-1">3so x 650</span>
                        <span class="border border-gray-500 text-[12px]  px-1 py-1">4so x 6000</span>
                    </div>
                </div>
                <p class="text-[12px] pb-2 text-center font-bold">LƯU Ý: PHIẾU CHỈ CÓ GIÁ TRỊ TRONG 3 NGÀY</p>
                <!-- Print Button -->
                <button class="w-full flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                    </svg>
                    {{__(' PRINT')}}
                </button>

            </div>

        </div>

        <!-- Table Section -->
        <div class="overflow-auto w-full mx-auto">
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                <tr class="bg-blue-600">
                    <th class="border border-gray-300 p-2">No</th>
                    <th class="border border-gray-300 p-2">Number</th>
                    <th class="border border-gray-300 p-2">Digit</th>
                    <th class="border border-gray-300 p-2">A</th>
                    <th class="border border-gray-300 p-2">B</th>
                    <th class="border border-gray-300 p-2">A+B</th>
                    <th class="border border-gray-300 p-2">Roll</th>
                    <th class="border border-gray-300 p-2">Roll 7</th>
                    <th class="border border-gray-300 p-2">Roll Parlay</th>
                    <th class="border border-gray-300 p-2">
                        <div class="flex-column">
                            <input type="checkbox" class="h-3 w-3 rounded-sm">
                            {{__('HN')}}
                        </div>
                    </th>
                    <th class="border border-gray-300 p-2">
                        <div class="flex-column">
                            <input type="checkbox" class="h-3 w-3 rounded-sm">
                            {{__('TP')}}
                        </div>
                    </th>
                    <th class="border border-gray-300 p-2">
                        <div class="flex-column">
                            <input type="checkbox" class="h-3 w-3 rounded-sm">
                            {{__('LA')}}
                        </div>
                    </th>

                    <th class="border border-gray-300 p-2">
                        <div class="flex-column">
                            <input type="checkbox" class="h-3 w-3 rounded-sm">
                            {{__('BP')}}
                        </div>
                    </th>

                    <th class="border border-gray-300 p-2">
                        <div class="flex-column">
                            <input type="checkbox" class="h-3 w-3 rounded-sm">
                            {{__('HG')}}
                        </div>
                    </th>

                    <th class="border border-gray-300 p-2">
                        <div class="flex-column">
                            <input type="checkbox" class="h-3 w-3 rounded-sm">
                            {{__('DNA')}}
                        </div>
                    </th>
                    <th class="border border-gray-300 p-2">
                        <div class="flex-column">
                            <input type="checkbox" class="h-3 w-3 rounded-sm">
                            {{__('QNG')}}
                        </div>
                    </th>
                    <th class="border border-gray-300 p-2">
                        <div class="flex-column">
                            <input type="checkbox" class="h-3 w-3 rounded-sm">
                            {{__('DNO')}}
                        </div>
                    </th>
                    <th class="border border-gray-300 p-2">
                        {{__('Total Amount')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                @for ($i = 1; $i <= 15; $i++)
                    <tr class="text-center">
                        <td class="border border-gray-300 p-2">{{ sprintf('%02d', $i) }}</td>
                        <td class="border border-gray-300 p-2">
                            <input type="number" class="w-full h-8 rounded">
                        </td>
                        <td class="border border-gray-300 p-2">-</td>
                        <td class="border border-gray-300 p-2 ">
                            <div class="flex justify-center items-center">
                                <input type="number" class="w-full h-8 rounded">
                                <input type="checkbox" class="rounded-sm h-3 w-3">
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex justify-center items-center">
                                <input type="number" class="w-full h-8 rounded">
                                <input type="checkbox" class="rounded-sm h-3 w-3">
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex justify-center items-center">
                                <input type="number" class="w-full h-8 rounded">
                                <input type="checkbox" class="rounded-sm h-3 w-3">
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex justify-center items-center">
                                <input type="number" class="w-full h-8 rounded">
                                <input type="checkbox" class="rounded-sm h-3 w-3">
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2 bg-yellow-200">
                            <div class="flex justify-center items-center">
                                <input type="number" class="w-full h-8 rounded">
                                <input type="checkbox" class="rounded-sm h-3 w-3">
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex justify-center items-center">
                                <input type="number" class="w-full h-8 rounded">
                                <input type="checkbox" class="rounded-sm h-3 w-3">
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input type="checkbox" class="h-3 w-3 rounded-sm">
                                {{__('HN')}}
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input type="checkbox" class="h-3 w-3 rounded-sm">
                                {{__('TP')}}
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input type="checkbox" class="h-3 w-3 rounded-sm">
                                {{__('LA')}}
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input type="checkbox" class="h-3 w-3 rounded-sm">
                                {{__('BP')}}
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input type="checkbox" class="h-3 w-3 rounded-sm">
                                {{__('HG')}}
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input type="checkbox" class="h-3 w-3 rounded-sm">
                                {{__('DNA')}}
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input type="checkbox" class="h-3 w-3 rounded-sm">
                                {{__('QNG')}}
                            </div>
                        </td>
                        <td class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input type="checkbox" class="h-3 w-3 rounded-sm">
                                <p> {{__('DNO')}}</p>
                            </div>

                        </td>
                        <td class="border border-gray-300 p-2">0</td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
    </div>
    </body>

</x-app-layout>
