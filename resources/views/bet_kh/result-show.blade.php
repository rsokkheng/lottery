<x-app-layout>
    <style>
        html, body { height:100%; margin:0; padding:0; }
        .full-screen-container { min-height:100vh; display:flex; flex-direction:column; }
        .content-wrapper { flex:1; display:flex; flex-direction:column; }
        .table-container { flex:1; display:flex; flex-direction:column; }
        .table-wrapper   { flex:1; overflow:auto; }
        .responsive-table { width:100%; height:100%; min-height:400px; }

        @media (max-width: 768px) {
            .date-controls { flex-direction:column; gap:0.5rem; }
            .tab-navigation { overflow-x:auto; -webkit-overflow-scrolling:touch; }
            .responsive-table { width:100%; table-layout:fixed; font-size:clamp(0.58rem,1.93vw,0.85rem); }
            .responsive-table td { word-wrap:break-word; overflow-wrap:break-word; white-space:normal; padding:0.25rem; vertical-align:top; }
            .table-wrapper { overflow-x:hidden; }
            .prize-column   { width:20%; font-size:clamp(0.68rem,2.42vw,0.97rem); }
            .province-column { width:20%; font-size:clamp(0.58rem,1.93vw,0.85rem); }
            .winning-number { font-size:clamp(0.58rem,1.74vw,0.85rem) !important; line-height:1.3; margin:0.1rem 0; }
            .province-name  { font-size:clamp(0.77rem,2.9vw,1.21rem) !important; }
            .province-code  { font-size:clamp(0.58rem,1.93vw,0.97rem) !important; }
            .prize-label-mobile { font-size:clamp(0.68rem,2.42vw,0.97rem) !important; }
        }
    </style>

    <div class="full-screen-container bg-white">
        <div class="content-wrapper px-2 sm:px-4 py-2 sm:py-4">

            <!-- Date Control -->
            <div class="date-controls flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4 mb-3 sm:mb-4">
                <a class="hidden" id="href_show_result" href="{{ route('bet-kh.result-show') }}"></a>
                <input type="date"
                       id="date_input"
                       class="px-3 py-2 border-2 border-blue-400 rounded focus:border-blue-600 focus:outline-none text-sm sm:text-base w-full sm:w-auto"
                       value="{{ date('Y-m-d', strtotime(str_replace('/', '-', $data['date_show']))) }}"
                       max="{{ date('Y-m-d') }}">
            </div>

            <!-- Tab Navigation -->
            <div class="mb-3 sm:mb-4">
                <input type="hidden" id="hidden_region" value="{{ $data['region']['slug'] ?? 'mien-nam' }}">
                <div class="tab-navigation">
                    <ul class="flex flex-row font-bold text-sm sm:text-lg text-center text-gray-500 border-b">
                        <li class="flex-shrink-0">
                            <a class="inline-block cursor-pointer px-3 py-2 sm:px-4 sm:py-3 text-blue-800 whitespace-nowrap {{ $data['type']===\App\Enums\HelperEnum::MienNamSlug->value ? 'bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600 rounded-t-lg':'hover:bg-blue-50' }}"
                               onclick="goShowResult('{{ \App\Enums\HelperEnum::MienNamSlug->value }}')">{{ __('lang.mien-nam') }}</a>
                        </li>
                        <li class="flex-shrink-0">
                            <a class="inline-block cursor-pointer px-3 py-2 sm:px-4 sm:py-3 text-blue-800 whitespace-nowrap {{ $data['type']===\App\Enums\HelperEnum::MienTrungSlug->value ? 'bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600 rounded-t-lg':'hover:bg-blue-50' }}"
                               onclick="goShowResult('{{ \App\Enums\HelperEnum::MienTrungSlug->value }}')">{{ __('lang.mien-trung') }}</a>
                        </li>
                        <li class="flex-shrink-0">
                            <a class="inline-block cursor-pointer px-3 py-2 sm:px-4 sm:py-3 text-blue-800 whitespace-nowrap {{ $data['type']===\App\Enums\HelperEnum::MienBacDienToanSlug->value ? 'bg-blue-500 text-white font-bold border-t border-l border-r border-blue-600 rounded-t-lg':'hover:bg-blue-50' }}"
                               onclick="goShowResult('{{ \App\Enums\HelperEnum::MienBacDienToanSlug->value }}')">{{ __('lang.mien-bac') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container border border-gray-300 rounded-lg overflow-hidden">
                <div class="table-wrapper overflow-y-auto">

                    @php
                        $isMienBac = $data['type'] === \App\Enums\HelperEnum::MienBacDienToanSlug->value;
                        $result    = $data['form_result']['result'];
                        $schedule  = $data['form_result']['schedule'];

                        if ($isMienBac) {
                            $displayRows = [
                                ['label'=>'1/A', 'type'=>'pair',   'p1'=>'GiaiBay', 'p2'=>'GiaiSau',  'color'=>'red'],
                                ['label'=>'2',   'type'=>'single', 'prize'=>'GiaiNam'],
                                ['label'=>'3',   'type'=>'single', 'prize'=>'GiaiTu'],
                                ['label'=>'4',   'type'=>'single', 'prize'=>'GiaiBa'],
                                ['label'=>'5',   'type'=>'single', 'prize'=>'GiaiNhi'],
                                ['label'=>'6',   'type'=>'single', 'prize'=>'GiaiNhat'],
                                ['label'=>'7/B', 'type'=>'pair',   'p1'=>'KH_B_2D', 'p2'=>'KH_B_3D', 'color'=>'blue'],
                                ['label'=>'8/C', 'type'=>'pair',   'p1'=>'KH_C_2D', 'p2'=>'KH_C_3D', 'color'=>'blue'],
                                ['label'=>'9/D', 'type'=>'pair',   'p1'=>'KH_D_2D', 'p2'=>'KH_D_3D', 'color'=>'blue'],
                            ];
                        } else {
                            $displayRows = [
                                ['label'=>'1/A',  'type'=>'pair',   'p1'=>'1/A',     'p2'=>'GiaiBay',  'color'=>'red'],
                                ['label'=>'2',    'type'=>'single', 'prize'=>'GiaiSau'],
                                ['label'=>'3',    'type'=>'single', 'prize'=>'GiaiNam'],
                                ['label'=>'4',    'type'=>'single', 'prize'=>'GiaiTu'],
                                ['label'=>'5',    'type'=>'single', 'prize'=>'GiaiBa'],
                                ['label'=>'6',    'type'=>'single', 'prize'=>'GiaiNhi'],
                                ['label'=>'7',    'type'=>'single', 'prize'=>'GiaiNhat'],
                                ['label'=>'8/B',  'type'=>'pair',   'p1'=>'KH_B_2D', 'p2'=>'KH_B_3D', 'color'=>'blue'],
                                ['label'=>'9/C',  'type'=>'pair',   'p1'=>'KH_C_2D', 'p2'=>'KH_C_3D', 'color'=>'blue'],
                                ['label'=>'10/D', 'type'=>'pair',   'p1'=>'KH_D_2D', 'p2'=>'KH_D_3D', 'color'=>'blue'],
                            ];
                        }
                    @endphp

                    <table class="responsive-table border-collapse text-center">
                        <thead class="bg-yellow-600 sticky top-0">
                            <tr class="border-gray-300">
                                <td class="border-2 py-2 px-1 text-lg max-md:text-sm text-white font-bold prize-column">
                                    {{ $data['date_show'] }}
                                    <input type="hidden" value="{{ $data['date_show'] }}" id="date_result">
                                </td>
                                @foreach($schedule as $sch)
                                    <td class="border-2 text-white font-bold px-1 province-column">
                                        <div class="flex-col w-full py-2">
                                            <div class="province-name text-2xl max-md:text-lg">{{ $sch['province'] }}</div>
                                            <div class="province-code text-lg max-md:text-sm">({{ $sch['code'] }})</div>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($displayRows as $rowIdx => $row)
                                <tr class="{{ $rowIdx % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                    <td style="font-size:20px;font-weight:600"
                                        class="border border-gray-300 text-black text-xl max-md:text-md py-2 px-1 prize-column prize-label-mobile {{ $rowIdx % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                        {{ $row['label'] }}
                                    </td>

                                    @foreach($schedule as $sch)
                                        <td class="p-1 border border-gray-300 align-top province-column">
                                            @if($row['type'] === 'pair')
                                                @php
                                                    $prov1     = $result[$row['p1']]['provinces'][$sch['code']] ?? null;
                                                    $prov2     = $result[$row['p2']]['provinces'][$sch['code']] ?? null;
                                                    $colorCls  = $row['color'] === 'red'
                                                                 ? 'text-red-600 font-bold'
                                                                 : 'text-blue-700 font-bold';
                                                @endphp
                                                @if($isMienBac && $row['color'] === 'red')
                                                    {{-- MienBac 1/A: all 2D joined on line 1, all 3D on line 2 --}}
                                                    @php
                                                        $line1 = collect($prov1['row_result'] ?? [])
                                                                    ->pluck('winning_number')
                                                                    ->map(fn($v) => $v ?? '**')
                                                                    ->join(' - ');
                                                        $line2 = collect($prov2['row_result'] ?? [])
                                                                    ->pluck('winning_number')
                                                                    ->map(fn($v) => $v ?? '***')
                                                                    ->join(' - ');
                                                    @endphp
                                                    <div class="flex w-full justify-center py-0.5">
                                                        <h6 class="winning-number text-red-600 font-bold">{{ $line1 }}</h6>
                                                    </div>
                                                    <div class="flex w-full justify-center py-0.5">
                                                        <h6 class="winning-number text-red-600 font-bold">{{ $line2 }}</h6>
                                                    </div>
                                                @else
                                                    {{-- MienNam/MienTrung pair, or MienBac B/C/D pair --}}
                                                    @php
                                                        $val1 = $prov1['row_result'][0]['winning_number'] ?? '--';
                                                        $val2 = $prov2['row_result'][0]['winning_number'] ?? '---';
                                                    @endphp
                                                    <div class="flex w-full justify-center py-0.5">
                                                        <h6 class="winning-number {{ $colorCls }}">{{ $val1 }} - {{ $val2 }}</h6>
                                                    </div>
                                                @endif

                                            @else
                                                @php
                                                    $prov = $result[$row['prize']]['provinces'][$sch['code']] ?? null;
                                                @endphp
                                                @if($prov)
                                                    @if($isMienBac)
                                                        {{-- MienBac: group by col_count, join within group by " - " --}}
                                                        @php
                                                            $rrList   = $prov['row_result'];
                                                            $colCount = $rrList[0]['col_count'] ?? 1;
                                                            $chunks   = array_chunk($rrList, $colCount);
                                                        @endphp
                                                        @foreach($chunks as $chunk)
                                                            @php
                                                                $len  = $chunk[0]['input_length'] ?? 4;
                                                                $nums = collect($chunk)
                                                                            ->pluck('winning_number')
                                                                            ->map(fn($v) => $v ?? str_repeat('*', $len))
                                                                            ->join(' - ');
                                                                $cls  = $chunk[0]['tailwind_class'] ?? 'text-gray-800 font-semibold';
                                                            @endphp
                                                            <div class="flex w-full justify-center py-0.5">
                                                                <h6 class="winning-number {{ $cls }}">{{ $nums }}</h6>
                                                            </div>
                                                        @endforeach
                                                    @else
                                                        {{-- MienNam/MienTrung: stack each number --}}
                                                        @foreach($prov['row_result'] as $rr)
                                                            <div class="flex w-full justify-center py-0.5">
                                                                <h6 class="winning-number {{ $rr['tailwind_class'] ?? 'text-gray-800 font-semibold' }}">
                                                                    {{ $rr['winning_number'] ?? str_repeat('*', $rr['input_length'] ?? 4) }}
                                                                </h6>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <script>
        $(function () {
            $('#date_input').on('change', function () {
                const val = $(this).val();
                if (!val) return;
                const d   = new Date(val);
                const fmt = String(d.getDate()).padStart(2,'0') + '/' +
                            String(d.getMonth()+1).padStart(2,'0') + '/' +
                            d.getFullYear();
                window.location = $('#href_show_result').attr('href') +
                                  '?date=' + fmt + '&region=' + $('#hidden_region').val();
            });

            function adjustLayout() {
                const avail = window.innerHeight -
                              ($('.date-controls').outerHeight() + $('.tab-navigation').outerHeight() + 100);
                $('.table-container').css('height', Math.max(400, avail) + 'px');
            }
            adjustLayout();
            $(window).resize(adjustLayout);
        });

        function goShowResult(region) {
            const val = $('#date_input').val();
            let fmt   = $('#date_result').val();
            if (val) {
                const d = new Date(val);
                fmt = String(d.getDate()).padStart(2,'0') + '/' +
                      String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
            }
            window.location = $('#href_show_result').attr('href') + '?date=' + fmt + '&region=' + region;
        }
    </script>
</x-app-layout>
