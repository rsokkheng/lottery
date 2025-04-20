<div>
    {{-- notification save success   --}}
    <div x-data="{ show: false, message: '' }"
         x-show="show"
         x-transition.opacity
         @bet-saved.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
         class="fixed top-4 right-4 z-50"
    >
        <div class="bg-green-500 text-white px-4 py-2 rounded shadow-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="message"></span>
        </div>
    </div>
    {{--End notification--}}

    <div class="grid grid-cols-1 xl:grid-cols-[30%_68%] gap-4 xl:mx-auto bg-white shadow-md py-4 rounded-lg space-x-2">
        <div class="w-[90%] px-2 mb-6 mx-auto w-full max-w-md rounded-lg">
            <div class="text-center font-bold py-2">
                {{ now()->format('d M Y') }}
            </div>
            <table class="text-sm border-collapse">
                <thead>
                <tr>
                    <th class="w-[20%] border border-gray-500 px-4 py-2 text-left">{{ __('Number') }}</th>
                    <th class="w-[40%] border border-gray-500 px-4 py-2 text-left">{{ __('Channel') }}</th>
                    <th class="w-[40%] border border-gray-500 px-4 py-2 text-left">{{ __('Amount') }}</th>
                </tr>
                </thead>
                <tbody>
                @if (count($invoices) > 0)
                    @foreach ($invoices as $invoice)
                        <tr>
                            <th class="w-[20%] border border-gray-500 px-4 py-2 text-left">
                                {{ $invoice['number'] }}
                            </th>
                            <th class="w-[40%] border border-gray-500 px-4 py-2 text-left">

                                {{ implode(', ', $invoice['chanel']) }}
                            </th>
                            <th class="w-[40%] border border-gray-500 px-4 py-2 text-left">
                                {{ implode(',', $invoice['amount']) }}
                            </th>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="3" class="border border-gray-500 px-4 py-2 text-center">
                            {{ __('Không Có Số') }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td colspan="2" class="border border-gray-500 px-4 py-2 font-bold">
                        {{ __('Total Amount') }}
                    </td>
                    <td class="border border-gray-500 px-4 py-2 text-right">

                        {{ $totalInvoice }} (VND)
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="border border-gray-500 px-4 py-2 font-bold">

                        {{ __('Total Due') }}
                    </td>
                    <td class="border border-gray-500 px-4 py-2 text-right">{{ $totalDue }} (VND)</td>
                </tr>
                </tbody>
            </table>
            <div class="flex p-2 items-center">
                <div class="px-2 text-[12px]">Chi Chu:</div>
                <div>
                    <span class="border border-gray-500 text-[12px]  px-1 py-1">{{ __('2so x 75') }}</span>
                    <span class="border border-gray-500 text-[12px]  px-1 py-1">{{ __('3so x 650') }}</span>
                    <span class="border border-gray-500 text-[12px]  px-1 py-1">{{ __('4so x 6000') }}</span>
                </div>
            </div>
            <p class="text-[12px] pb-2 text-center font-bold">{{ __('LƯU Ý: PHIẾU CHỈ CÓ GIÁ TRỊ TRONG 3 NGÀY') }}
            </p>
            <!-- Print Button -->
            <button wire:click="handleSave"
                    class="hidden w-full lg:flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5Zm-3 0h.008v.008H15V10.5Z"/>
                </svg>
                {{ __(' PRINT') }}
            </button>

            <button wire:click="handleSave"
                    class="lg:hidden w-full flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9.75v6.75m0 0-3-3m3 3 3-3m-8.25 6a4.5 4.5 0 0 1-1.41-8.775 5.25 5.25 0 0 1 10.233-2.33 3 3 0 0 1 3.758 3.848A3.752 3.752 0 0 1 18 19.5H6.75Z"/>
                </svg>
                {{ __(' SAVE') }}
            </button>

        </div>


        <!-- Table header Section -->
        <div class="overflow-auto w-full mx-auto relative">
            <div class="flex whitespace-nowrap mb-2">
                <p class="text-md font-bold">{{__('Time Left:')}}</p>
                @foreach ($timeClose as $time)
                    <p class="text-md font-bold px-2 " id="time-{{ $time->id }}">
                        {{ $time->time_close }} ({{ $time->code }})
                    </p>
                @endforeach
            </div>
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                <tr class="bg-blue-600 text-white" style="background-color:rgb(198 145 18)">
                    <th class="border border-gray-300">{{ __('No') }}</th>
                    <th class="border border-gray-300 p-2">{{ __('Number') }}</th>
                    <th class="border border-gray-300">{{ __('Digit') }}</th>
                    <th class="border border-gray-300">{{ __('A') }}</th>
                    <th class="border border-gray-300">{{ __('B') }}</th>
                    <th class="border border-gray-300">{{ __('A+B') }}</th>
                    <th class="border border-gray-300">{{ __('Roll') }}</th>
                    <th class="border border-gray-300">{{ __('Roll 7') }}</th>
                    <th class="border border-gray-300">{{ __('Roll Parlay') }}</th>
                    @foreach ($schedules as $key => $item)
                        <th class="border border-gray-300">
                            <div class="flex-column">
                                <input
                                        type="checkbox"
                                        wire:model="province_check.{{ $key }}"
                                        wire:click="handleProvinceCheck({{ $key }})"
                                        class="h-3 w-3 rounded-sm">
                                {{ $item['code'] }}
                            </div>
                        </th>
                    @endforeach

                    <th class="border border-gray-300">
                        {{ __('Total Amount') }}
                    </th>
                </tr>
                </thead>
                <tbody>
                @for ($i = 0; $i < $totalRow; $i++)
                    <tr class="text-center">
                        <!--No-->
                        <td class="border border-gray-300">
                            {{ sprintf('%02d', $i + 1) }}
                        </td>
                        <!--Number-->
                        <td class="border border-gray-300 py-2">
                            <input
                                    type="text"
                                    autocomplete="off"
                                    wire:model.defer="number.{{ $i }}"
                                    wire:input="handleInputNumber"
                                    class="w-[100px] lg:w-full h-8 rounded"
                                    oninput="formatNumberInput(this)">
                        </td>
                        <!--Digit-->
                        <td class="border border-gray-300">
                            {{ $digit[$i] ?? '-' }}
                        </td>
                        <!--A-->
                        <td class="border border-gray-300">
                            <div class="flex justify-center items-center">
                                <input
                                        type="text"
                                        wire:model="a_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{$i}})"
                                        {{ isset($enableChanelA[$i]) && $enableChanelA[$i] ? '' : 'disabled' }}
                                        class="w-[100px] lg:w-full h-8 rounded focus:ring-0 translate-0 {{ isset($enableChanelA[$i]) && $enableChanelA[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                <input type="checkbox"
                                       id="a_check_{{ $i }}"
                                       wire:model="a_check.{{ $i }}"
                                       wire:click="handleCheckChanel({{$i}},'ACheck')"
                                       {{ isset($enableChanelA[$i]) && !$enableChanelA[$i] ? 'disabled' : '' }}
                                       class="rounded-sm h-3 w-3 {{ isset($enableChanelA[$i]) && $enableChanelA[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}">
                            </div>
                        </td>
                        <!--B-->
                        <td class="border border-gray-300">
                            <div class="flex justify-center items-center">
                                <input
                                        type="text"
                                        wire:model="b_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{$i}})"
                                        wire:input="handleInputNumber"
                                        :disabled="{{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'false' : 'true' }}"
                                        class="w-[100px] lg:w-full h-8 rounded {{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">

                                <input
                                        type="checkbox"
                                        wire:model="b_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{$i}},'BCheck')"
                                        :disabled="{{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'false' : 'true' }}"
                                        class="rounded-sm h-3 w-3 {{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}">
                            </div>
                        </td>
                        <!--A+B-->
                        <td class="border border-gray-300">
                            <div class="flex justify-center items-center">
                                <input
                                        type="text"
                                        wire:model="ab_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{$i}})"
                                        {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i] ? '' : 'disabled' }}
                                        class="w-[100px] lg:w-full h-8 rounded {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                <input
                                        type="checkbox"
                                        wire:model="ab_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{$i}},'ABCheck')"
                                        {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i] ? '' : 'disabled' }}
                                        class="rounded-sm h-3 w-3 {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}">
                            </div>
                        </td>
                        <!--Roll-->
                        <td class="border border-gray-300">
                            <div class="flex justify-center items-center">
                                <input
                                        type="text"
                                        id="roll_amount_{{ $i }}"
                                        wire:model="roll_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{$i}})"
                                        {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? '' : 'disabled' }}
                                        class="w-[100px] lg:w-full h-8 rounded {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                <input
                                        type="checkbox"
                                        wire:model="roll_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{$i}},'RCheck')"
                                        {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? '' : 'disabled' }}
                                        class="rounded-sm h-3 w-3 {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}">
                            </div>
                        </td>
                        <!--Roll 7-->
                        <td class="border border-gray-300 bg-yellow-200">
                            <div class="flex justify-center items-center">
                                <input
                                        type="text"
                                        wire:model="roll7_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{$i}})"
                                        {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ? '' : 'disabled' }}
                                        class="w-[100px] lg:w-full h-8 rounded {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                <input
                                        type="checkbox"
                                        wire:model="roll7_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{$i}},'R7Check')"
                                        {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ? '' : 'disabled' }}
                                        class="rounded-sm h-3 w-3 {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}">
                            </div>
                        </td>
                        <!--Roll Parlay-->
                        <td class="border border-gray-300">
                            <div class="flex justify-center items-center">
                                <input
                                        type="text"
                                        wire:model="roll_parlay_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{$i}})"
                                        {{ isset($enableChanelRollParlay[$i]) && $enableChanelRollParlay[$i] ? '' : 'disabled' }}
                                        class="w-[100px] lg:w-full h-8 rounded {{ isset($enableChanelRollParlay[$i]) && $enableChanelRollParlay[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                <input
                                        type="checkbox"
                                        wire:model="roll_parlay_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{$i}},'RPCheck')"
                                        :checked="{{ isset($roll_parlay_check[$i]) && $roll_parlay_check[$i] ? 'true':'false'}}"
                                        {{ isset($enableCheckRollParlay[$i]) && $enableCheckRollParlay[$i] ? '' : 'disabled' }}
                                        class="rounded-sm h-3 w-3 {{ isset($enableCheckRollParlay[$i]) && $enableCheckRollParlay[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}">

                            </div>
                        </td>

                        @foreach ($schedules as $key => $item)
                            <td class="border border-gray-300 py-2">
                                <div class="flex-column">
                                    <input
                                            type="checkbox"
                                            wire:model.live="province_body_check.{{ $key }}.{{ $i }}"
                                            wire:change="handleProvinceBodyCheck({{ $key }},{{ $i }}, {{$item}})"
                                            :checked="{{ isset($province_check[$key]) && $province_check[$key] ? 'true' : 'false' }}"
                                            class="h-3 w-3 rounded-sm">
                                    {{ $item['code'] }}
                                </div>
                            </td>
                        @endforeach

                        <!--Total Amount-->
                        <td class="border border-gray-300 p-2">{{ $total_amount[$i] }}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Function to move focus to the next input on Enter key press
    const handleEnterKey = (event, inputs) => {
        const currentInput = event.target;
        if (currentInput.disabled) {
            return; // Do nothing if the current input is disabled
        }

        if (event.key === 'Enter') {
            event.preventDefault(); // Prevent form submission if inside a form

            let nextIndex = Array.from(inputs).indexOf(currentInput) + 1;

            // Skip disabled inputs
            while (nextIndex < inputs.length && inputs[nextIndex].disabled) {
                nextIndex++;
            }

            // Move focus to the next enabled input, if exists
            if (nextIndex < inputs.length) {
                inputs[nextIndex].focus();
            }
        }
    }

    // Example: Attach the handleEnterKey to the input fields
    const formatNumberValue = (input, nextInput) => {
        let value = input.value.replace(/[^0-9.]/g, ''); // Allow only digits (0-9)
        // Allow only one dot
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
         // Limit to 5 characters
         if (value.length > 5) {
            value = value.slice(0, 5);
        }
        input.value = value; // Update the input value with the formatted number

        // Add event listener to handle the Enter key
        input.addEventListener('keydown', (event) => handleEnterKey(event, nextInput));
    }

    // Example: formatNumberInput with Enter key functionality
    function formatNumberInput(input, nextInput) {
        let value = input.value;

        // Logic for handling specific number formats like '#', '*' and others
        if (value.includes("#")) {
            value = value.replace(/[^0-9#]/g, ''); // Remove invalid characters
            let validFormat =
                /^(\d+|(\d{2}\#)|(\d{2}\#\d{1})|(\d{2}\#\d{2})|(\d{2}\#\d{2}\#)|(\d{2}\#\d{2}\#\d{1})|(\d{2}\#\d{2}\#\d{2})|(\d{2}\#\d{2}\#\d{2}\#)||(\d{2}\#\d{2}\#\d{2}\#\d{1})|(\d{2}\#\d{2}\#\d{2}\#\d{2}))$/;
            if (!validFormat.test(value)) {
                value = value.slice(0, -1); // Remove the last character if invalid
            }
        } else if (value.startsWith("*")) {
            value = value.replace(/[^0-9\*]/g, ''); // Remove invalid characters
            if (!/^\*([0-9]{1,3})?$/.test(value)) {
                value = value.slice(0, -1); // Remove the last character if invalid
            }
        } else if (value.startsWith("*") || value.endsWith("*")) {
            const validFormat = /^\*?\d{1,3}\*?$/;
            if (!validFormat.test(value)) {
                value = value.slice(0, -1); // Remove the last character if invalid
            }
        } else {
            value = value.replace(/[^0-9]/g, ''); // Remove invalid characters
            if (value.length > 4) {
                value = value.slice(0, 4); // Restrict to 4 digits
            }
        }

        input.value = value; // Update the input value

        // Add event listener to handle the Enter key
        input.addEventListener('keydown', (event) => handleEnterKey(event, nextInput));
    }

    // Function to initialize all input fields with next field navigation
    const initializeInputs = () => {
        const inputs = document.querySelectorAll('input[type="text"]'); // Select only input[type="text"]

        inputs.forEach((input) => {
            // Add event listener to handle Enter key press for each input
            input.addEventListener('keydown', (event) => handleEnterKey(event, inputs));
        });
    };

    // Initialize all input fields when the page loads
    window.addEventListener('DOMContentLoaded', initializeInputs);
</script>

