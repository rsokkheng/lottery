
<div>
   
     {{-- Option 1: Using Alpine.js --}}
{{-- <div x-data="{ checkboxA: false, checkboxB: false }">
    <div class="mb-4">
        <label class="inline-flex items-center">S
            <input type="checkbox" 
                   x-model="checkboxA"
                   @change="if(checkboxA) checkboxB = true"
                   class="form-checkbox">
            <span class="ml-2">Checkbox A</span>
        </label>
    </div>

    <div class="mb-4">
        <label class="inline-flex items-center">
            <input type="checkbox" 
                   x-model="checkboxB"
                   class="form-checkbox">
            <span class="ml-2">Checkbox B</span>
        </label>
    </div>
</div> --}}

{{-- Option 2: Using Plain JavaScript --}}
{{-- <div>
    <div class="mb-4">
        <label class="inline-flex items-center">
            <input type="checkbox" 
                   id="lheckboxA"
                   onchange="document.getElementById('locationB').checked = this.checked"
                   class="form-checkbox">
            <span class="ml-2">Checkbox A</span>
        </label>
    </div>

    <div class="mb-4">
        <label class="inline-flex items-center">
            <input type="checkbox" 
                   id="locationB"
                   class="form-checkbox">
            <span class="ml-2">Checkbox B</span>
        </label>
    </div>
</div>
   --}}
    

    <div class="grid grid-cols-[20%_78%] gap-4 mx-auto bg-white shadow-md py-4 rounded-lg">
        <div class="w-full px-2">
            <div class="mb-6">
                <div class="w-full max-w-md mx-auto  rounded-lg">
                    <div class="text-center font-bold py-2">
                        {{__('Day 18 Month 01 Year 2025')}}
                    </div>
                    <table class="w-full text-sm border-collapse">
                        <thead>
                        <tr>
                            <th class="border border-gray-500 px-4 py-2 text-left">{{__('Number')}}</th>
                            <th class="border border-gray-500 px-4 py-2 text-left">{{__('Channel')}}</th>
                            <th class="border border-gray-500 px-4 py-2 text-left">{{__('Amount')}}</th>
                        </tr>
                        </thead>
                         <tbody>
                        <tr>
                            <td colspan="3" class="border border-gray-500 px-4 py-2 text-center">
                                {{__('Không Có Số')}}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-gray-500 px-4 py-2 font-bold">
                                {{__('Total Amount')}}
                            </td>
                            <td class="border border-gray-500 px-4 py-2 text-right">{{__('0 (VND)')}}</td>
                        </tr>
                        <tr>
                            <td colspan="2" class="border border-gray-500 px-4 py-2 font-bold">

                                {{__('Total Due')}}
                            </td>
                            <td class="border border-gray-500 px-4 py-2 text-right">{{__('0 (VND)')}}</td>
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
                        <span class="border border-gray-500 text-[12px]  px-1 py-1">{{__('2so x 75')}}</span>
                        <span class="border border-gray-500 text-[12px]  px-1 py-1">{{__('3so x 650')}}</span>
                        <span class="border border-gray-500 text-[12px]  px-1 py-1">{{__('4so x 6000')}}</span>
                    </div>
                </div>
                <p class="text-[12px] pb-2 text-center font-bold">{{__('LƯU Ý: PHIẾU CHỈ CÓ GIÁ TRỊ TRONG 3 NGÀY')}}</p>
                <!-- Print Button -->
                <button wire:click="handleSave" class="w-full flex justify-center items-center bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
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
                    <span><small>Time Left:</small> 10:11:36 (HN)08:08:36 (VL)08:13:37 (BD)08:05:37 (TV)09:10:37 (GL)09:10:37 (NT)</span>
                <tr class="bg-blue-600 text-white" style="background-color:rgb(198 145 18)">
                    <th class="border border-gray-300 p-2">{{__('No')}}</th>
                    <th class="border border-gray-300 p-2">{{__('Number')}}</th>
                    <th class="border border-gray-300 p-2">{{__('Digit')}}</th>
                    <th class="border border-gray-300 p-2">{{__('A')}}</th>
                    <th class="border border-gray-300 p-2">{{__('B')}}</th>
                    <th class="border border-gray-300 p-2">{{__('A+B')}}</th>
                    <th class="border border-gray-300 p-2">{{__('Roll')}}</th>
                    <th class="border border-gray-300 p-2">{{__('Roll 7')}}</th>
                    <th class="border border-gray-300 p-2">{{__('Roll Parlay')}}</th>
                    @foreach ($province as $key=>$item)
                        <th class="border border-gray-300 p-2">
                            <div class="flex-column">
                                <input
                                        type="checkbox"
                                        id="locationA"
                                        wire:click="handleCheckLocation({{ $key }})"
                                         {{-- onchange="document.getElementById('locationBody.{{ $key }}').checked = this.checked" --}}
                                        wire:model="location.{{$key}}"
                                        class="h-3 w-3 rounded-sm">
                                {{ $item['code'] }}
                            </div>
                        </th>
                    @endforeach

                    <th class="border border-gray-300 p-2">
                        {{__('Total Amount')}}
                    </th>
                </tr>
                </thead>
                <tbody>
                @for ($i = 1; $i <= 15; $i++)
                    <tr class="text-center">
                        <!--No-->
                        <td class="border border-gray-300 p-2">{{ sprintf('%02d', $i) }}</td>

                        <!--Number-->
                        <td class="border border-gray-300 p-2">
                            <input
                                    type="text"
                                    autocomplete="off"
                                    id="number{{$i}}"
                                    wire:model.defer="number.{{$i}}"
                                    wire:input="handleInputNumber"
                                    class="w-full h-8 rounded"
                                    oninput="formatNumberInput(this)"
                            >
                        </td>
                        <!--Digit-->
                        <td class="border border-gray-300 p-2">{{$digit[$i]??"-"}}</td>
                        <!--A-->
                        <td class="border border-gray-300 p-2 ">
                            <div class="flex justify-center items-center">
                                <input
                                        type="text"
                                        id="chanelA{{$i}}"
                                        wire:input="handleInputNumber"
                                        wire:model="chanelA.{{ $i }}"
                                        {{ isset($enableChanelA[$i]) && $enableChanelA[$i] ? '' : 'disabled' }}
                                        class="w-full h-8 rounded focus:ring-0 translate-0 {{ isset($enableChanelA[$i]) && $enableChanelA[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)"
                                >
                                <input
                                        type="checkbox"
                                        id="checkA{{$i}}"
                                        wire:model.defer="checkA.{{$i}}"
                                        {{ isset($enableChanelA[$i]) && !$enableChanelA[$i] ? 'disabled' : '' }}
                                        class="rounded-sm h-5 w-5 {{ isset($enableChanelA[$i]) && $enableChanelA[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                >
                            </div>
                        </td>
                        <!--B-->
                        <td class="border border-gray-300 p-2">
                            <div class="flex justify-center items-center">
                            <input
                               type="text"
                                id="chanelB{{$i}}"
                                wire:model.defer="chanelB.{{$i}}"
                                :disabled="{{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'false' : 'true' }}"
                                class="w-full h-8 rounded {{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                oninput="formatNumberValue(this)">

                            <input
                                type="checkbox"
                                id="checkB{{$i}}"
                                wire:model.defer="checkB.{{$i}}"
                                :disabled="{{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'false' : 'true' }}"
                                class="rounded-sm h-5 w-5 {{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}">

                        </td>
                        <!--A+B-->
                        <td class="border border-gray-300 p-2">
                            <div class="flex justify-center items-center">
                                <input
                                       type="text"
                                        id="chanelAB{{$i}}"
                                        wire:model.defer="chanelAB.{{$i}}"
                                        {{isset($enableChanelAB[$i]) && $enableChanelAB[$i] ?'':'disabled'}}
                                        class="w-full h-8 rounded {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i]  ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)"
                                >
                                <input
                                        type="checkbox"
                                        id="checkAB.{{$i}}"
                                        wire:model.defer="checkAB.{{$i}}"
                                        {{isset($enableChanelAB[$i]) && $enableChanelAB[$i] ?'': 'disabled'}}
                                        class="rounded-sm h-5 w-5 {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"

                                >
                            </div>
                        </td>
                        <!--Roll-->
                        <td class="border border-gray-300 p-2">
                            <div class="flex justify-center items-center">
                                <input
                                       type="text"
                                        id="chanelRoll"
                                        wire:model.defer="chanelRoll.{{$i}}"
                                        {{isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ?'': 'disabled'}}
                                        class="w-full h-8 rounded {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)"
                                >
                                <input type="checkbox"
                                       id="checkRoll"
                                       wire:model.defer="checkRoll.{{$i}}"
                                       {{isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ?'': 'disabled'}}
                                       class="rounded-sm h-5 w-5 {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"

                                >
                            </div>
                        </td>
                        <!--Roll 7-->
                        <td class="border border-gray-300 p-2 bg-yellow-200">
                            <div class="flex justify-center items-center">
                                <input
                                       type="text"
                                        id="chanelRoll7"
                                        wire:model.defer="chanelRoll7.{{$i}}"
                                        {{isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ?'': 'disabled'}}
                                        class="w-full h-8 rounded {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)"
                                >
                                <input
                                        type="checkbox"
                                        id="checkRoll7"
                                        wire:model.defer="checkRoll7.{{$i}}"
                                        {{isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ?'': 'disabled'}}
                                        class="rounded-sm h-5 w-5 {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i]? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"

                                >
                            </div>
                        </td>
                        <!--Roll Parlay-->
                        <td class="border border-gray-300 p-2">
                            <div class="flex justify-center items-center">
                            <input
                                       type="text"
                                        id="chanelRollParlay"
                                        wire:model.defer="chanelRollParlay.{{$i}}"
                                       {{ isset($enableChanelRollParlay[$i]) && $enableChanelRollParlay[$i] ? '' : 'disabled' }}
                                        class="w-full h-8 rounded {{ isset($enableChanelRollParlay[$i]) && $enableChanelRollParlay[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)"
                                >
                                <input
                                type="checkbox"
                                id="checkRollParlay"
                                wire:model.defer="checkRollParlay.{{$i}}"
                                {{ isset($enableChanelRollParlay[$i]) && $enableChanelRollParlay[$i] ? '' : 'disabled' }}
                                class="rounded-sm h-5 w-5 {{ isset($enableChanelRollParlay[$i]) && $enableChanelRollParlay[$i] ? 'bg-white' : 'bg-gray-200 cursor-no-drop' }}"
                            >

                            </div>
                        </td>
                        @foreach ($province as $key => $item)
                            <td  class="border border-gray-300 p-2">
                                <div class="flex-column">
                                    <input
                                            type="checkbox"
                                            wire:model="locationBody.{{$key}}"
                                            class="h-3 w-3 rounded-sm"
                                    >
                                    {{$item['code']}}
                                </div>
                            </td>
                        @endforeach
                    
                        <!--Total Amount-->
                        <td class="border border-gray-300 p-2">{{$totalAmount}}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
        </div>
    </div>
    </body>
</div>

<script>
   const formatNumberValue = (input) => {
        let value = input.value.replace(/[^0-9]/g, ''); // Allow only digits (0-9)
        if (value.length > 5) {
            value = value.slice(0, 5); // Restrict to 4 digits
        }
        input.value = value; // Update the input value with the formatted number
    }

    function formatNumberInput(input) {
        let value = input.value;

        if (value.includes("#")) {
            // Allow patterns with # as specified
            value = value.replace(/[^0-9#]/g, ''); // Remove invalid characters
            let validFormat = /^(\d+|(\d{2}\#)|(\d{2}\#\d{1})|(\d{2}\#\d{2})|(\d{2}\#\d{2}\#)|(\d{2}\#\d{2}\#\d{1})|(\d{2}\#\d{2}\#\d{2})|(\d{2}\#\d{2}\#\d{2}\#)||(\d{2}\#\d{2}\#\d{2}\#\d{1})|(\d{2}\#\d{2}\#\d{2}\#\d{2}))$/;
            if (!validFormat.test(value)) {
                value = value.slice(0, -1); // Remove the last character if invalid
            }
        } else if (value.startsWith("*")) {
            value = value.replace(/[^0-9\*]/g, ''); // Remove invalid characters
            // Ensure it starts with * followed by 1 to 3 digits
            if (!/^\*([0-9]{1,3})?$/.test(value)) {
                value = value.slice(0, -1); // Remove the last character if invalid
            }
        } else if (value.startsWith("*") || value.endsWith("*")) {
            const validFormat = /^\*?\d{1,3}\*?$/;
            if (!validFormat.test(value)) {
                value = value.slice(0, -1); // Remove the last character if invalid
            }
        } else {
            // Fallback for other cases, allow only numbers
            value = value.replace(/[^0-9]/g, ''); // Remove invalid characters
            if (value.length > 4) {
                value = value.slice(0, 4); // Restrict to 4 digits
            }
        }

        input.value = value; // Update the input value
    }


</script>


