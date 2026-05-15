<div>
    {{-- Notification --}}
    <div x-data="{ show: false, message: '', type: '' }" x-show="show" x-transition.opacity
        @bet-saved.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type || 'success';
        setTimeout(() => show = false, 3000);
        if (type === 'warning') {
            setTimeout(() => window.location.reload(), 1000);
        }
        "
        class="fixed top-4 right-4 z-50">
        <div class="px-4 py-2 rounded shadow-lg flex items-center gap-2"
            :class="{
                'bg-green-500 text-white': type === 'success',
                'bg-red-500 text-white': type === 'error',
                'bg-yellow-500 text-white': type === 'warning'
            }">
            <template x-if="type === 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </template>
            <template x-if="type === 'error'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </template>
            <template x-if="type === 'warning'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01M12 17h.01M12 3a9 9 0 110 18 9 9 0 010-18z" />
                </svg>
            </template>
            <span x-text="message"></span>
        </div>
    </div>

    <div class="bg-white shadow-md p-4 rounded-lg">
        {{-- Top Info Section --}}
        <div class="mb-4 text-sm">
            <table class="border-separate" style="border-spacing: 0 3px;">
                <tbody>
                    <tr>
                        <td class="pr-2 font-semibold whitespace-nowrap">{{ __('lang.bet-credit') }}:</td>
                        <td class="pr-8 whitespace-nowrap">{{ $betAccount ?? 0 }} VND</td>
                        <td class="pr-2 font-bold whitespace-nowrap">{{ __('lang.company') }} (GMT+7):</td>
                        <td class="text-gray-700">
                            @foreach ($timeClose as $time)
                                {{ $time->code }} ({{ $time->time_close }})@if (!$loop->last), @endif
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="pr-2 font-semibold whitespace-nowrap">{{ __('lang.outstanding') }}:</td>
                        <td class="pr-8 whitespace-nowrap">{{ $totalOutstanding }} VND</td>
                        <td class="pr-2 font-bold whitespace-nowrap">{{ __('lang.number-wildcard') }}:</td>
                        <td>* = any of 0,1,2,...,9 &nbsp; 11-19(11,12,...,19) small(00-49) big(50-99)</td>
                    </tr>
                    <tr>
                        <td class="pr-2 font-semibold whitespace-nowrap">{{ __('lang.total-amount') }}:</td>
                        <td class="pr-8 whitespace-nowrap">{{ $totalInvoice }} VND</td>
                        <td class="pr-2 font-bold whitespace-nowrap">{{ __('lang.shortcut-word') }}:</td>
                        <td>{{ __('lang.head-last-roll-roll7') }}</td>
                    </tr>
                    <tr>
                        <td class="pr-2 font-semibold whitespace-nowrap">{{ __('lang.total-due') }}:</td>
                        <td class="pr-8 whitespace-nowrap font-bold {{ $totalDue > $betAccount ? 'text-red-600' : '' }}">
                            {{ $totalDue }} VND
                        </td>
                        <td class="pr-2 font-bold whitespace-nowrap">{{ __('lang.time-left') }}:</td>
                        <td>
                            @foreach ($timeClose as $time)
                                <span class="countdown-timer font-semibold"
                                      data-close="{{ $time->time_close }}"
                                      data-code="{{ $time->code }}">
                                    {{ $time->code }}: {{ $time->time_close }}
                                </span>@if (!$loop->last) &nbsp;|&nbsp; @endif
                            @endforeach
                        </td>
                    </tr>
                    @if ($totalDue > $betAccount)
                    <tr>
                        <td colspan="4" class="pb-1">
                            <div class="flex items-center gap-2 bg-red-50 border border-red-300 text-red-700 rounded px-3 py-1.5 text-xs font-semibold">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                                </svg>
                                Insufficient credit! Please add
                                <span class="font-extrabold">
                                    {{ number_format(round($totalDue - $betAccount, 2), 2) }} VND
                                </span>
                                more to your account.
                            </div>
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="4" class="pt-1">
                            <div class="flex gap-2">
                                <button wire:click="handleSave"
                                    @if($totalDue > $betAccount) disabled @endif
                                    class="font-semibold px-8 py-1.5 rounded
                                        {{ $totalDue > $betAccount
                                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                            : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                                    {{ __('lang.save') }}
                                </button>
                                <button wire:click="handleReset"
                                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold px-8 py-1.5 rounded">
                                    {{ __('lang.reset') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Invoice List --}}
        @if (count($invoices) > 0)
        <div class="mb-3">
            <table class="text-xs border-collapse">
                <thead>
                    <tr>
                        <th class="border border-gray-500 px-3 py-1 text-left whitespace-nowrap">{{ __('lang.number') }}</th>
                        <th class="border border-gray-500 px-3 py-1 text-left whitespace-nowrap">{{ __('lang.channel') }}</th>
                        <th class="border border-gray-500 px-3 py-1 text-left whitespace-nowrap">{{ __('lang.amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td class="border border-gray-500 px-3 py-1">{{ $invoice['number'] }}</td>
                            <td class="border border-gray-500 px-3 py-1">{{ implode(', ', $invoice['chanel']) }}</td>
                            <td class="border border-gray-500 px-3 py-1">{{ implode(',', $invoice['amount']) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Package prices --}}
        <div class="flex pb-2 items-center text-xs">
            <div class="px-2">Chi Chu:</div>
            <div>
                @foreach ($packagePrice as $betType => $price)
                    <span class="border border-gray-500 px-1 py-1">{{ __($betType . ' x ' . $price) }}</span>
                @endforeach
            </div>
        </div>
        <p class="text-xs pb-2 text-center font-bold">{{ __('LƯU Ý: PHIẾU CHỈ CÓ GIÁ TRỊ TRONG 3 NGÀY') }}</p>

        {{-- Bet Table --}}
        <div x-data="popupHandler()" class="overflow-auto">
            <table class="w-full text-sm border-collapse border border-gray-300">
                <thead>
                    <tr class="text-white text-left" style="background-color:#1d4ed8">
                        <th class="border border-blue-500 px-2 py-2 whitespace-nowrap">{{ __('message.no') }}</th>
                        <th class="border border-blue-500 px-2 py-2 whitespace-nowrap">{{ __('lang.number') }}</th>
                        <th class="border border-blue-500 px-2 py-2 whitespace-nowrap">{{ __('lang.digit') }}</th>
                        <th class="border border-blue-500 px-2 py-2">{{ __('A') }}</th>
                        <th class="border border-blue-500 px-2 py-2">{{ __('B') }}</th>
                        <th class="border border-blue-500 px-2 py-2">{{ __('A+B') }}</th>
                        <th class="border border-blue-500 px-2 py-2 whitespace-nowrap">{{ __('lang.roll') }}</th>
                        <th class="border border-blue-500 px-2 py-2 whitespace-nowrap">{{ __('lang.roll7') }}</th>
                        <th class="border border-blue-500 px-2 py-2 whitespace-nowrap">{{ __('lang.roll-parlay') }}</th>
                        @foreach ($schedules as $key => $item)
                            <th class="border border-blue-500 px-2 py-2 text-center">
                                <div class="flex flex-col items-center gap-0.5">
                                    <input type="checkbox" wire:model="province_check.{{ $key }}"
                                        wire:click="handleProvinceCheck({{ $key }})"
                                        class="h-3 w-3 rounded-sm">
                                    <span>{{ $item['code'] }}</span>
                                </div>
                            </th>
                        @endforeach
                        <th class="border border-blue-500 px-2 py-2 whitespace-nowrap">{{ __('lang.total-amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($i = 0; $i < $totalRow; $i++)
                        <tr class="text-left hover:bg-gray-50">
                            {{-- No --}}
                            <td class="border border-gray-300 px-2 py-1 text-gray-600">
                                {{ sprintf('%02d', $i + 1) }}
                            </td>
                            {{-- Number --}}
                            <td class="border border-gray-300 py-1">
                                <input type="text" autocomplete="off"
                                    wire:model.defer="number.{{ $i }}"
                                    wire:input="handleInputNumber"
                                    class="w-24 h-7 rounded text-left border border-gray-300 focus:outline-none focus:border-blue-400"
                                    oninput="formatNumberInput(this)">
                            </td>
                            {{-- Digit --}}
                            <td class="border border-gray-300 px-1 text-gray-600">
                                {{ $digit[$i] ?? '-' }}
                            </td>
                            {{-- A --}}
                            <td class="border border-gray-300 px-1">
                                <div class="flex justify-start items-center">
                                    <input type="text" wire:model="a_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{ $i }})"
                                        @click.stop="showPopup('a_amount',{{ $i }}, $event)"
                                        x-ref="a_amount{{ $i }}"
                                        {{ isset($enableChanelA[$i]) && $enableChanelA[$i] ? '' : 'disabled' }}
                                        class="w-16 h-7 rounded text-left border {{ isset($enableChanelA[$i]) && $enableChanelA[$i] ? 'bg-white border-gray-300' : 'bg-gray-200 border-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                    <input type="checkbox" wire:model="a_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{ $i }},'ACheck')"
                                        {{ isset($enableChanelA[$i]) && !$enableChanelA[$i] ? 'disabled' : '' }}
                                        class="h-3 w-3 ml-0.5">
                                </div>
                            </td>
                            {{-- B --}}
                            <td class="border border-gray-300 px-1">
                                <div class="flex justify-start items-center">
                                    <input type="text" wire:model="b_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{ $i }})"
                                        @click.stop="showPopup('b_amount',{{ $i }}, $event)"
                                        x-ref="b_amount{{ $i }}"
                                        :disabled="{{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'false' : 'true' }}"
                                        class="w-16 h-7 rounded text-left border {{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'bg-white border-gray-300' : 'bg-gray-200 border-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                    <input type="checkbox" wire:model="b_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{ $i }},'BCheck')"
                                        :disabled="{{ isset($enableChanelB[$i]) && $enableChanelB[$i] ? 'false' : 'true' }}"
                                        class="h-3 w-3 ml-0.5">
                                </div>
                            </td>
                            {{-- A+B --}}
                            <td class="border border-gray-300 px-1">
                                <div class="flex justify-start items-center">
                                    <input type="text" wire:model="ab_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{ $i }})"
                                        @click.stop="showPopup('ab_amount',{{ $i }}, $event)"
                                        x-ref="ab_amount{{ $i }}"
                                        {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i] ? '' : 'disabled' }}
                                        class="w-16 h-7 rounded text-left border {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i] ? 'bg-white border-gray-300' : 'bg-gray-200 border-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                    <input type="checkbox" wire:model="ab_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{ $i }},'ABCheck')"
                                        {{ isset($enableChanelAB[$i]) && $enableChanelAB[$i] ? '' : 'disabled' }}
                                        class="h-3 w-3 ml-0.5">
                                </div>
                            </td>
                            {{-- Roll --}}
                            <td class="border border-gray-300 px-1">
                                <div class="flex justify-start items-center">
                                    <input type="text" id="roll_amount_{{ $i }}"
                                        wire:model="roll_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{ $i }})"
                                        @click.stop="showPopup('roll_amount',{{ $i }}, $event)"
                                        x-ref="roll_amount{{ $i }}"
                                        {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? '' : 'disabled' }}
                                        class="w-16 h-7 rounded text-left border {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? 'bg-white border-gray-300' : 'bg-gray-200 border-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                    <input type="checkbox" wire:model="roll_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{ $i }},'RCheck')"
                                        {{ isset($enableChanelRoll[$i]) && $enableChanelRoll[$i] ? '' : 'disabled' }}
                                        class="h-3 w-3 ml-0.5">
                                </div>
                            </td>
                            {{-- Roll 7 --}}
                            <td class="border border-gray-300 px-1 bg-yellow-100">
                                <div class="flex justify-start items-center">
                                    <input type="text" wire:model="roll7_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{ $i }})"
                                        @click.stop="showPopup('roll7_amount',{{ $i }}, $event)"
                                        x-ref="roll7_amount{{ $i }}"
                                        {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ? '' : 'disabled' }}
                                        class="w-16 h-7 rounded text-left border {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ? 'bg-white border-gray-300' : 'bg-gray-200 border-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                    <input type="checkbox" wire:model="roll7_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{ $i }},'R7Check')"
                                        {{ isset($enableChanelRoll7[$i]) && $enableChanelRoll7[$i] ? '' : 'disabled' }}
                                        class="h-3 w-3 ml-0.5">
                                </div>
                            </td>
                            {{-- Roll Parlay --}}
                            <td class="border border-gray-300 px-1">
                                <div class="flex justify-start items-center">
                                    <input type="text" wire:model="roll_parlay_amount.{{ $i }}"
                                        wire:input="handleInputAmount({{ $i }})"
                                        @click.stop="showPopup('roll_parlay_amount',{{ $i }}, $event)"
                                        x-ref="roll_parlay_amount{{ $i }}"
                                        {{ isset($enableChanelRollParlay[$i]) && $enableChanelRollParlay[$i] ? '' : 'disabled' }}
                                        class="w-16 h-7 rounded text-left border {{ isset($enableChanelRollParlay[$i]) && $enableChanelRollParlay[$i] ? 'bg-white border-gray-300' : 'bg-gray-200 border-gray-200 cursor-no-drop' }}"
                                        oninput="formatNumberValue(this)">
                                    <input type="checkbox" wire:model="roll_parlay_check.{{ $i }}"
                                        wire:click="handleCheckChanel({{ $i }},'RPCheck')"
                                        :checked="{{ isset($roll_parlay_check[$i]) && $roll_parlay_check[$i] ? 'true' : 'false' }}"
                                        {{ isset($enableCheckRollParlay[$i]) && $enableCheckRollParlay[$i] ? '' : 'disabled' }}
                                        class="h-3 w-3 ml-0.5">
                                </div>
                            </td>
                            {{-- Province Checkboxes --}}
                            @foreach ($schedules as $key => $item)
                                <td class="border border-gray-300 py-1 text-center">
                                    <div class="flex flex-col items-center">
                                        <input type="checkbox"
                                            wire:model.live="province_body_check.{{ $key }}.{{ $i }}"
                                            wire:change="handleProvinceBodyCheck({{ $key }},{{ $i }}, {{ $item }})"
                                            :checked="{{ isset($province_check[$key]) && $province_check[$key] ? 'true' : 'false' }}"
                                            class="h-3 w-3 rounded-sm">
                                        <span class="text-xs">{{ $item['code'] }}</span>
                                    </div>
                                </td>
                            @endforeach
                            {{-- Total --}}
                            <td class="border border-gray-300 px-2 py-1 font-medium text-left">
                                {{ $total_amount[$i] }}
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>

            {{-- Popup --}}
            <div x-show="show" x-transition @click.away="hidePopup()"
                :style="'top:' + posY + 'px; left:' + posX + 'px'"
                class="w-40 grid grid-cols-2 fixed bg-gray-200 border p-2 rounded shadow z-50 gap-2">
                <button @click="clearValue()" class="bg-blue-500 text-white p-1 rounded">CLS</button>
                <button @click="addValue(0.5)" class="bg-blue-500 text-white p-1 rounded">+0.5</button>
                <button @click="addValue(1)" class="bg-blue-500 text-white p-1 rounded">+1</button>
                <button @click="addValue(5)" class="bg-blue-500 text-white p-1 rounded">+5</button>
                <button @click="addValue(10)" class="bg-blue-500 text-white p-1 rounded">+10</button>
                <button @click="addValue(50)" class="bg-blue-500 text-white p-1 rounded">+50</button>
                <button @click="addValue(100)" class="bg-blue-500 text-white p-1 rounded">+100</button>
                <button @click="addValue(500)" class="bg-blue-500 text-white p-1 rounded">+500</button>
            </div>
        </div>
    </div>
</div>

<script>
    const handleEnterKey = (event, inputs) => {
        const currentInput = event.target;
        if (currentInput.disabled) return;
        if (event.key === 'Enter') {
            event.preventDefault();
            let nextIndex = Array.from(inputs).indexOf(currentInput) + 1;
            while (nextIndex < inputs.length && inputs[nextIndex].disabled) {
                nextIndex++;
            }
            if (nextIndex < inputs.length) {
                inputs[nextIndex].focus();
            }
        }
    }

    const formatNumberValue = (input) => {
        let value = input.value.replace(/[^0-9.]/g, '');
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }
        if (value.length > 5) {
            value = value.slice(0, 5);
        }
        input.value = value;
        const inputs = document.querySelectorAll('input[type="text"]');
        input.addEventListener('keydown', (event) => handleEnterKey(event, inputs));
    }

    function formatNumberInput(input) {
        let value = input.value;
        if (value.includes("#")) {
            value = value.replace(/[^0-9#]/g, '');
            let validFormat =
                /^(\d+|(\d{2}\#)|(\d{2}\#\d{1})|(\d{2}\#\d{2})|(\d{2}\#\d{2}\#)|(\d{2}\#\d{2}\#\d{1})|(\d{2}\#\d{2}\#\d{2})|(\d{2}\#\d{2}\#\d{2}\#)||(\d{2}\#\d{2}\#\d{2}\#\d{1})|(\d{2}\#\d{2}\#\d{2}\#\d{2}))$/;
            if (!validFormat.test(value)) {
                value = value.slice(0, -1);
            }
        } else if (value.startsWith("*")) {
            value = value.replace(/[^0-9\*]/g, '');
            if (!/^\*([0-9]{1,3})?$/.test(value)) {
                value = value.slice(0, -1);
            }
        } else if (value.endsWith("*")) {
            const validFormat = /^\*?\d{1,3}\*?$/;
            if (!validFormat.test(value)) {
                value = value.slice(0, -1);
            }
        } else {
            value = value.replace(/[^0-9]/g, '');
            if (value.length > 4) {
                value = value.slice(0, 4);
            }
        }
        input.value = value;
        const inputs = document.querySelectorAll('input[type="text"]');
        input.addEventListener('keydown', (event) => handleEnterKey(event, inputs));
    }

    const initializeInputs = () => {
        const inputs = document.querySelectorAll('input[type="text"]');
        inputs.forEach((input) => {
            input.addEventListener('keydown', (event) => handleEnterKey(event, inputs));
        });
    };

    function startCountdowns() {
        const spans = document.querySelectorAll('.countdown-timer');

        function tick() {
            const now = new Date();
            spans.forEach(span => {
                const code  = span.dataset.code;
                const parts = span.dataset.close.split(':');
                const close = new Date();
                close.setHours(parseInt(parts[0]), parseInt(parts[1]), parseInt(parts[2] || 0), 0);

                const diff = close - now;
                if (diff <= 0) {
                    span.textContent = `${code}: Closed`;
                    span.className = 'countdown-timer font-bold text-red-600';
                } else {
                    const h = Math.floor(diff / 3600000);
                    const m = Math.floor((diff % 3600000) / 60000);
                    const s = Math.floor((diff % 60000) / 1000);
                    const hms = (h > 0 ? `${h}h ` : '') + `${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
                    span.textContent = `${code}: ${hms}`;
                    span.className = diff < 300000
                        ? 'countdown-timer font-bold text-red-500'
                        : 'countdown-timer font-semibold text-blue-600';
                }
            });
        }

        tick();
        setInterval(tick, 1000);
    }

    window.addEventListener('DOMContentLoaded', () => {
        initializeInputs();
        startCountdowns();
    });

    popupHandler = function () {
        return {
            show: false,
            activeRow: null,
            activeCol: null,
            posX: 0,
            posY: 0,

            showPopup(col, row, event) {
                this.activeCol = col;
                this.activeRow = row;
                this.show = true;
                const rect = event.target.getBoundingClientRect();
                this.posX = rect.left;
                this.posY = rect.top - 180;
            },

            hidePopup() {
                this.show = false;
                this.activeRow = null;
                this.activeCol = null;
            },

            addValue(amount) {
                const input = this.$refs[`${this.activeCol}${this.activeRow}`];
                let current = parseFloat(input.value) || 0;
                let result = current + amount;
                input.value = result % 1 === 0 ? result.toString() : result.toFixed(1);
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.focus();
            },

            clearValue() {
                const input = this.$refs[`${this.activeCol}${this.activeRow}`];
                input.value = '';
                input.focus();
            },
        }
    }
</script>
