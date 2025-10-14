@php
    $isAdmin = in_array('admin', $roles ?? []);
@endphp

<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />
    <div class="flex-col bg-white rounded-lg px-4 py-4">
        <div class="flex w-full">
            <div class="w-full">
                @php
                    $selectedDate = request()->get('date', 'today'); // fallback to 'today' if nothing is selected
                @endphp

                <div class="flex flex-wrap gap-4">
                    <!-- Start Date -->
                    <div class="flex-1 min-w-[150px] max-w-[200px]">
                        <label for="startDate" class="block text-sm text-gray-700">{{ __('message.start_date') }}</label>
                        <input id="startDate" value="{{ $startDate }}" datepicker datepicker-buttons
                            datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd"
                            class="w-full px-4 py-2 border rounded bg-white text-gray-700 shadow">
                    </div>

                    <!-- End Date -->
                    <div class="flex-1 min-w-[150px] max-w-[200px]">
                        <label for="endDate" class="block text-sm text-gray-700">{{ __('message.end_date') }}</label>
                        <input id="endDate" value="{{ $endDate }}" datepicker datepicker-buttons
                            datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd"
                            class="w-full px-4 py-2 border rounded bg-white text-gray-700 shadow">
                    </div>

                    <!-- Company -->
                    <div class="flex-1 min-w-[150px] max-w-[200px]">
                        <label class="block text-sm text-gray-700">{{ __('message.company') }}</label>
                        <select id="company" class="w-full rounded border px-2 py-2">
                            @foreach ($company as $val)
                                @if ($company_id == $val['id'])
                                    <option selected value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                                @else
                                    <option value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Button -->
                    <div class="flex items-end">
                        <button onclick="applyDateFilter()"
                            class="px-6 py-2 bg-blue-600 text-white rounded shadow w-full sm:w-auto">
                            {{ __('message.search') }}
                        </button>
                    </div>

                    <!-- Clear Button -->
                    <div class="flex items-end">
                        <button onclick="clearDateFilter()"
                            class="px-6 py-2 bg-gray-300 text-gray-800 rounded shadow w-full sm:w-auto">
                            {{ __('message.clear') }}
                        </button>
                    </div>

                    <!-- Back Button -->
                    <div class="flex items-end">
                        <a href="javascript:history.back()" style="text-decoration: none;"
                            class="text-blue-600 hover:underline inline-flex items-center">
                            <span class="px-6 py-2 bg-red-600 text-white rounded shadow">{{ __('message.back') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="flex w-full">
            <div class="w-full overflow-auto py-4">
                <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                    <thead>
                        <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.no') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.date') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.number') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.digit') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.game') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.company') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.amount') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.odds') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.net') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.turnover') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.commission') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.net_amount') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.compensate') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('message.win_lose') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (isset($data) && count($data) > 0)
                            @foreach ($data as $key => $betNumber)
                                <tr
                                    class="border border-gray-300 hover:bg-gray-100 {{ $betNumber->compensate > 0 ? 'bg-red-100 hover:bg-red-200 text-black-500' : '' }} ">
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ $key + 1 }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ $betNumber->created_at ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ $betNumber->generated_number ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ $betNumber->digit_format ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ $betNumber->bet_game ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ $betNumber->province_en ?? '' }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ number_format($betNumber->get_roll_amount ?? 0, 2) }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ $betNumber->price ?? 0 }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ number_format($betNumber->rate ?? 0, 2) }}</td>
                                    <td
                                        class="py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ $betNumber->number_turnover ?? 0 }}</td>
                                    <td
                                        class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ number_format($betNumber->commission ?? 0, 2) }}</td>
                                    <td
                                        class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ number_format($betNumber->net_amount ?? 0, 2) }}</td>
                                    <td
                                        class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas">
                                        {{ number_format($betNumber->compensate ?? 0, 2) }}</td>
                                    <td
                                        class="text-right py-2 px-1 border border-gray-300 whitespace-nowrap text-[16px] sm:text-bas {{ number_format($betNumber->win_lose ?? 0, 2) < 0 ? 'text-red-500' : '' }}">
                                        {{ number_format($betNumber->win_lose ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="border border-gray-300 hover:bg-gray-100">
                                <td colspan="9"></td>
                                <td class="text-right py-2 px-1 border font-bold border-gray-300">
                                    {{ number_format($totalNetAmount['turnover'], 3, '.', '') }}</td>
                                <td class="text-right py-2 px-1 border font-bold border-gray-300">
                                    {{ number_format($totalNetAmount['commission'], 3, '.', '') }}</td>
                                <td class="text-right py-2 px-1 border font-bold border-gray-300">
                                    {{ number_format($totalNetAmount['net_amount'], 3, '.', '') }}</td>
                                <td class="text-right py-2 px-1 border font-bold border-gray-300">
                                    {{ number_format($totalNetAmount['compensate'], 3, '.', '') }}</td>
                                <td
                                    class="text-right py-2 px-1 border font-bold border-gray-300 {{ $totalNetAmount['win_lose'] < 0 ? 'text-red-500' : '' }}">
                                    {{ number_format($totalNetAmount['win_lose'], 3, '.', '') }}</td>
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
        function applyDateFilter() {
            const start = document.getElementById('startDate').value;
            const end = document.getElementById('endDate').value;
            const com_id = $('#company').find(":selected").val();

            if (start && end) {
                const url = new URL(window.location.href);
                url.searchParams.set('startDate', start);
                url.searchParams.set('endDate', end);
                url.searchParams.set('com_id', com_id);
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
            url.searchParams.delete('com_id');
            url.searchParams.delete('date');
            window.location.href = url.toString();
        }
    </script>
</x-app-layout>
