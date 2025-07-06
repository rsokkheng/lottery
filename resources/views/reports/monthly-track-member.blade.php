<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet"/>
    <style>

    </style>
    <div class="flex-col bg-white rounded-lg px-4 py-4">
            <div class="flex w-full space-x-2">
                <div class="">
                @php
                    $selectedDate = request()->get('date', 'today'); // fallback to 'today' if nothing is selected
                @endphp

                    <div class="flex items-center gap-4">
                        <div>
                            <label for="startDate" class="block text-sm text-gray-700">Start Date</label>
                            <input 
                                id="startDate" 
                                value="{{ $startDate }}"
                                datepicker datepicker-buttons datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd"
                                class="px-4 py-2 border rounded bg-white text-gray-700 shadow">
                        </div>
                        <div>
                            <label for="endDate" class="block text-sm text-gray-700">End Date</label>
                            <input 
                                id="endDate" 
                                value="{{ $endDate }}"
                                datepicker datepicker-buttons datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd"
                                class="px-4 py-2 border rounded bg-white text-gray-700 shadow">
                        </div>
                        <div>
                            <button onclick="applyDateFilter()" class="px-6 py-2 bg-blue-600 text-white rounded shadow" style="margin-top: 20px;">
                            Search
                            </button>
                        </div>
                        <div>
                        <button 
                                onclick="clearDateFilter()" 
                                class="px-6 py-2 bg-gray-300 text-gray-800 rounded shadow" style="margin-top: 20px;">
                                Clear
                            </button>
                        </div>
                    </div>
                </div>
               <a href="{{ route('reports.monthly-tracking') }}" style="text-decoration: none; margin-top: 20px; " class="text-blue-600 hover:underline inline-flex items-center">
                 <span style="padding: 5px; background-color: red; color: white;">Back</span> <span>{{ $managerName->username}}</span> 
                </a>
            </div>
            <div class="flex w-full">
                <div class="w-full overflow-auto py-4">
                    <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                        <thead>
                            <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                                <th class="py-2 border border-white">{{__('No')}}</th>
                                <th class="py-2 border border-white">{{__('Date')}}</th>
                                <th class="py-2 border border-white">{{__('Account')}}</th>
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
                                $netAmount =0;
                                $commission = 0;
                                $compensate = 0;
                            @endphp
                            @foreach($data as $key => $row)
                            @php
                                    $netAmount  =  $row->net_amount;
                                    $commission = $row->commission;
                                    $diff =  $row->Compensate - $netAmount ;
                                    $totalInvoice += (float)($row->total_receipts ?? 0);
                                    $turNover += (float)($row->total_amount ?? 0);
                                    $totalCommission += (float)($commission ?? 0);
                                    $totalNetAmount += (float)( $netAmount ?? 0);
                                    $totalCompensate += (float)($row->Compensate ?? 0);
                                    $totalWinLose += $diff;
                                @endphp
                                <tr class="border border-gray-300 hover:bg-gray-100">
                                    <td class="py-2 px-1 border border-gray-300">{{ $key + 1 }}</td>
                                    <td class="py-2 px-1 border border-gray-300">{{ $row->bet_date }}</td>
                                    <td class="py-2 px-1 border border-gray-300">
                                    {{ $row->account }}
                                    </td>

                                    <td class="py-2 px-1 border border-gray-300">{{ $row->total_receipts }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{ number_format($row->total_amount, 3, '.', '') }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{ number_format($commission, 3, '.', '') }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300"> {{ number_format($netAmount, 3, '.', '') }}</td>
                                    <td class="text-right py-2 px-1 border border-gray-300">{{ number_format( $row->Compensate, 3, '.', '') }}</td>
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
                                <td class="py-2 px-1 border border-gray-300 text-center" colspan="10">No data</td>
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
function applyDateFilter() {
    const start = document.getElementById('startDate').value;
    const end = document.getElementById('endDate').value;

    if (start && end) {
        const url = new URL(window.location.href);
        url.searchParams.set('startDate', start);
        url.searchParams.set('endDate', end);
        // Optional: remove old 'date' param if it existed
        url.searchParams.delete('date');
        window.location.href = url.toString();
    } else {
        alert('Please select both start and end dates.');
    }
}
function clearDateFilter() {
    const url = new URL(window.location.href);
    url.searchParams.delete('startDate');
    url.searchParams.delete('endDate');
    url.searchParams.delete('date');
    window.location.href = url.toString();
}
</script>
</x-app-layout>