@php
    $isAdmin = in_array('admin', $roles ?? []);
@endphp

<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />
    <div class="flex-col bg-white rounded-lg px-4 py-4">
        <div class="">
            @php
                $selectedDate = request()->get('date', 'today'); // fallback to 'today' if nothing is selected
            @endphp

            <div class="flex items-center gap-4">
                <div>
                    <label for="startDate" class="block text-sm text-gray-700">Start Date</label>
                    <input id="startDate" value="{{ $startDate }}" datepicker datepicker-buttons
                        datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd"
                        class="px-4 py-2 border rounded bg-white text-gray-700 shadow">
                </div>
                <div>
                    <label for="endDate" class="block text-sm text-gray-700">End Date</label>
                    <input id="endDate" value="{{ $endDate }}" datepicker datepicker-buttons
                        datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd"
                        class="px-4 py-2 border rounded bg-white text-gray-700 shadow">
                </div>
                <div class="">
                    <label class="block text-sm text-gray-700">Company</label>
                    <div class="w-full lg:w-48">
                        <select id="company" class="rounded w-full">
                            @foreach ($company as $val)
                                @if ($company_id == $val['id'])
                                    <option selected value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                                @else
                                    <option value="{{ $val['id'] }}">{{ $val['label'] }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <button onclick="applyDateFilter()" class="px-6 py-2 bg-blue-600 text-white rounded shadow"
                        style="margin-top: 20px;">
                        Search
                    </button>
                </div>
                <div>
                    <button onclick="clearDateFilter()" class="px-6 py-2 bg-gray-300 text-gray-800 rounded shadow"
                        style="margin-top: 20px;">
                        Clear
                    </button>
                </div>
                <a href="javascript:history.back()" style="text-decoration: none; margin-top: 20px;"
                    class="text-blue-600 hover:underline inline-flex items-center">
                    <span style="padding: 5px; background-color: red; color: white;">Back</span>
                </a>
            </div>
        </div>
        <div class="flex w-full">
            <div class="w-full overflow-auto py-4">
                <table class="w-full border-collapse border border-gray-600 rounded-lg text-center">
                    <thead>
                        <tr class="bg-blue-500 border text-white font-bold text-nowrap">
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('No') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Date') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Number') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Digit') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Game') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Company') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Amount') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Odds') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Net') }}</th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Turnover') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Commission') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Net Amount') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Compensate') }}
                            </th>
                            <th class="py-2 border border-white px-2 text-[12px] sm:text-base">{{ __('Win/Lose') }}
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
                                        {{ $betNumber->bet_date ?? '' }}</td>
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
