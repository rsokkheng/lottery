<x-app-layout>
    @php $currencyLabel = strtoupper(session('currency', 'VND')); @endphp

    <style>
        .rs-tab { display:inline-block; cursor:pointer; padding:8px 16px; font-weight:700; font-size:.88rem;
                  color:#92400e; white-space:nowrap; border-radius:8px 8px 0 0; transition:all .15s; }
        .rs-tab.active { background:#78350f; color:#fff; }
        .rs-tab:not(.active):hover { background:#fef3c7; }
        .responsive-table { width:100%; border-collapse:collapse; text-align:center; }
        .responsive-table td { border:1px solid #e5e7eb; padding:6px 4px; vertical-align:top; }
        .prize-column   { width:15%; font-size:1.1rem; font-weight:700; }
        .province-column { width:20%; }
        .province-name  { font-size:1.4rem; font-weight:700; }
        .province-code  { font-size:.95rem; }
        .winning-number { line-height:1.5; margin:2px 0; }
        .prize-label-mobile { font-size:1.1rem; font-weight:700; }
        @media (max-width:768px) {
            .responsive-table { font-size:.72rem; }
            .province-name  { font-size:1rem !important; }
            .province-code  { font-size:.75rem !important; }
            .winning-number { font-size:.72rem !important; }
            .prize-label-mobile { font-size:.85rem !important; }
        }
    </style>

    <div class="bp-wrap">
        <div class="bp-page-header">
            <div>
                <span class="bp-badge bp-badge-kh">BET KHMER &middot; {{ $currencyLabel }}</span>
                <div class="bp-page-title">Lottery Results</div>
            </div>
            <div>
                <a class="hidden" id="href_show_result" href="{{ route($khRoutePrefix . '.result-show') }}"></a>
                <input type="date" id="date_input"
                    style="border:1px solid #d1d8e0;border-radius:8px;padding:6px 12px;font-size:.85rem;outline:none;"
                    value="{{ date('Y-m-d', strtotime(str_replace('/', '-', $data['date_show']))) }}"
                    max="{{ date('Y-m-d') }}">
            </div>
        </div>

        <div style="padding:0 16px;border-bottom:2px solid #e2e8f0;background:#f8f9fc;">
            <input type="hidden" id="hidden_region" value="{{ $data['region']['slug'] ?? 'mien-nam' }}">
            <ul style="list-style:none;margin:0;padding:0;display:flex;gap:4px;padding-top:8px;overflow-x:auto;">
                <li>
                    <a class="rs-tab {{ $data['type'] === \App\Enums\HelperEnum::MienNamSlug->value ? 'active' : '' }}"
                       onclick="goShowResult('{{ \App\Enums\HelperEnum::MienNamSlug->value }}')">
                        {{ __('lang.mien-nam') }}
                    </a>
                </li>
                <li>
                    <a class="rs-tab {{ $data['type'] === \App\Enums\HelperEnum::MienTrungSlug->value ? 'active' : '' }}"
                       onclick="goShowResult('{{ \App\Enums\HelperEnum::MienTrungSlug->value }}')">
                        {{ __('lang.mien-trung') }}
                    </a>
                </li>
                <li>
                    <a class="rs-tab {{ $data['type'] === \App\Enums\HelperEnum::MienBacDienToanSlug->value ? 'active' : '' }}"
                       onclick="goShowResult('{{ \App\Enums\HelperEnum::MienBacDienToanSlug->value }}')">
                        {{ __('lang.mien-bac') }}
                    </a>
                </li>
            </ul>
        </div>

        <div style="overflow-x:auto;padding:16px;">
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

            <table class="responsive-table">
                <thead>
                    <tr style="background:#b45309;">
                        <td style="border:2px solid #fff;color:#fff;font-weight:700;padding:10px 6px;min-width:80px;">
                            {{ $data['date_show'] }}
                            <input type="hidden" value="{{ $data['date_show'] }}" id="date_result">
                        </td>
                        @foreach($schedule as $sch)
                            <td style="border:2px solid #fff;color:#fff;font-weight:700;padding:10px 6px;min-width:100px;">
                                <div class="province-name">{{ $sch['province'] }}</div>
                                <div class="province-code">({{ $sch['code'] }})</div>
                            </td>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($displayRows as $rowIdx => $row)
                        <tr style="{{ $rowIdx % 2 === 0 ? 'background:#fff;' : 'background:#f9fafb;' }}">
                            <td class="prize-label-mobile" style="font-weight:700;">{{ $row['label'] }}</td>

                            @foreach($schedule as $sch)
                                <td class="province-column">
                                    @if($row['type'] === 'pair')
                                        @php
                                            $prov1    = $result[$row['p1']]['provinces'][$sch['code']] ?? null;
                                            $prov2    = $result[$row['p2']]['provinces'][$sch['code']] ?? null;
                                            $colorCls = $row['color'] === 'red' ? 'text-red-600 font-bold' : 'text-blue-700 font-bold';
                                        @endphp
                                        @if($isMienBac && $row['color'] === 'red')
                                            @php
                                                $line1 = collect($prov1['row_result'] ?? [])->pluck('winning_number')->map(fn($v) => $v ?? '**')->join(' - ');
                                                $line2 = collect($prov2['row_result'] ?? [])->pluck('winning_number')->map(fn($v) => $v ?? '***')->join(' - ');
                                            @endphp
                                            <div style="display:flex;justify-content:center;padding:2px 0;">
                                                <span class="winning-number" style="color:#dc2626;font-weight:700;">{{ $line1 }}</span>
                                            </div>
                                            <div style="display:flex;justify-content:center;padding:2px 0;">
                                                <span class="winning-number" style="color:#dc2626;font-weight:700;">{{ $line2 }}</span>
                                            </div>
                                        @else
                                            @php
                                                $val1 = $prov1['row_result'][0]['winning_number'] ?? '--';
                                                $val2 = $prov2['row_result'][0]['winning_number'] ?? '---';
                                            @endphp
                                            <div style="display:flex;justify-content:center;padding:2px 0;">
                                                <span class="winning-number {{ $colorCls }}">{{ $val1 }} - {{ $val2 }}</span>
                                            </div>
                                        @endif
                                    @else
                                        @php $prov = $result[$row['prize']]['provinces'][$sch['code']] ?? null; @endphp
                                        @if($prov)
                                            @if($isMienBac)
                                                @php
                                                    $rrList   = $prov['row_result'];
                                                    $colCount = $rrList[0]['col_count'] ?? 1;
                                                    $chunks   = array_chunk($rrList, $colCount);
                                                @endphp
                                                @foreach($chunks as $chunk)
                                                    @php
                                                        $len  = $chunk[0]['input_length'] ?? 4;
                                                        $nums = collect($chunk)->pluck('winning_number')->map(fn($v) => $v ?? str_repeat('*', $len))->join(' - ');
                                                        $cls  = $chunk[0]['tailwind_class'] ?? 'text-gray-800 font-semibold';
                                                    @endphp
                                                    <div style="display:flex;justify-content:center;padding:2px 0;">
                                                        <span class="winning-number {{ $cls }}">{{ $nums }}</span>
                                                    </div>
                                                @endforeach
                                            @else
                                                @foreach($prov['row_result'] as $rr)
                                                    <div style="display:flex;justify-content:center;padding:2px 0;">
                                                        <span class="winning-number {{ $rr['tailwind_class'] ?? 'text-gray-800 font-semibold' }}">
                                                            {{ $rr['winning_number'] ?? str_repeat('*', $rr['input_length'] ?? 4) }}
                                                        </span>
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

    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <script>
        $(function () {
            $('#date_input').on('change', function () {
                const val = $(this).val();
                if (!val) return;
                const d   = new Date(val);
                const fmt = String(d.getDate()).padStart(2,'0') + '/' +
                            String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
                window.location = $('#href_show_result').attr('href') +
                                  '?date=' + fmt + '&region=' + $('#hidden_region').val();
            });
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
