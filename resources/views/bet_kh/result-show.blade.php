<x-app-layout>
    @php $currencyLabel = strtoupper(session('currency', 'VND')); @endphp

    <style>
        /* ── Tabs ── */
        .rs-tab { display:inline-block; cursor:pointer; padding:12px 24px; font-weight:700; font-size:.88rem;
                  color:#92400e; white-space:nowrap; border-radius:8px 8px 0 0; transition:all .15s; }
        .rs-tab.active { background:#78350f; color:#fff; }
        .rs-tab:not(.active):hover { background:#fef3c7; }

        /* ── Result table ── */
        .rs-wrap { max-width:960px; margin:28px auto; border-radius:14px; overflow:hidden;
                   box-shadow:0 4px 24px rgba(0,0,0,.13); border:1px solid #e2e8f0; }
        .rs-table { width:100%; border-collapse:collapse; text-align:center; }
        .rs-table td { border:1px solid #e5e7eb; vertical-align:middle; padding:0; }

        /* Header */
        .rs-head { background:linear-gradient(135deg,#b45309 0%,#92400e 100%); }
        .rs-head td { border-color:#c0714a; color:#fff; padding:22px 18px; }
        .rs-date { font-size:1rem; font-weight:700; letter-spacing:.03em; }
        .rs-prov-name { font-size:1.5rem; font-weight:800; letter-spacing:.01em; }
        .rs-prov-code { font-size:.84rem; opacity:.85; margin-top:4px; }

        /* Prize label column */
        .rs-label { width:110px; min-width:110px; background:#fffbf0;
                    border-right:3px solid #e5e7eb; font-size:1rem; font-weight:800;
                    color:#374151; padding:22px 14px; letter-spacing:.02em; }

        /* Data cells */
        .rs-cell { padding:20px 20px; }
        .rs-row-even { background:#fff; }
        .rs-row-odd  { background:#f9fafb; }
        .rs-row-even:hover, .rs-row-odd:hover { background:#fef9ec; }

        /* Winning numbers */
        .wn       { display:block; line-height:2.4; text-align:center; }
        .wn-red   { color:#dc2626; font-weight:800; font-size:24px; }
        .wn-blue  { color:#1d4ed8; font-weight:800; font-size:22px; }
        .wn-num   { color:#1e293b; font-weight:700; font-size:20px; }
        .wn-sep   { color:#9ca3af; font-size:14px; margin:0 2px; }

        @media (max-width:640px) {
            .rs-wrap         { margin:12px 8px; border-radius:8px; }
            .rs-prov-name    { font-size:1rem; }
            .rs-label        { width:70px; min-width:70px; font-size:.8rem; padding:14px 8px; }
            .rs-cell         { padding:12px 8px; }
            .wn-red          { font-size:17px; }
            .wn-blue         { font-size:16px; }
            .wn-num          { font-size:15px; }
        }
    </style>

    <div class="bp-wrap">
        <div class="bp-page-header">
            <div>
                <span class="bp-badge bp-badge-kh">Lotto Cambodia · {{ $currencyLabel }}</span>
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

        <div style="padding:8px 16px 24px;">
            @php
                $isMienBac = $data['type'] === \App\Enums\HelperEnum::MienBacDienToanSlug->value;
                $result    = $data['form_result']['result'];
                $schedule  = $data['form_result']['schedule'];

                if ($isMienBac) {
                    $displayRows = [
                        ['label'=>'1/A', 'type'=>'pair',   'p1'=>'KH_GiaiBay', 'p2'=>'KH_GiaiSau',  'color'=>'red'],
                        ['label'=>'2',   'type'=>'single', 'prize'=>'KH_GiaiNam'],
                        ['label'=>'3',   'type'=>'single', 'prize'=>'KH_GiaiTu'],
                        ['label'=>'4',   'type'=>'single', 'prize'=>'KH_GiaiBa'],
                        ['label'=>'5',   'type'=>'single', 'prize'=>'KH_GiaiNhi'],
                        ['label'=>'6',   'type'=>'single', 'prize'=>'KH_GiaiNhat'],
                        ['label'=>'7/B', 'type'=>'pair',   'p1'=>'KH_B_2D', 'p2'=>'KH_B_3D', 'color'=>'blue'],
                        ['label'=>'8/C', 'type'=>'pair',   'p1'=>'KH_C_2D', 'p2'=>'KH_C_3D', 'color'=>'blue'],
                        ['label'=>'9/D', 'type'=>'pair',   'p1'=>'KH_D_2D', 'p2'=>'KH_D_3D', 'color'=>'blue'],
                    ];
                } else {
                    $displayRows = [
                        ['label'=>'1/A',  'type'=>'pair',   'p1'=>'1/A',         'p2'=>'KH_GiaiBay',  'color'=>'red'],
                        ['label'=>'2',    'type'=>'single', 'prize'=>'KH_GiaiSau'],
                        ['label'=>'3',    'type'=>'single', 'prize'=>'KH_GiaiNam'],
                        ['label'=>'4',    'type'=>'single', 'prize'=>'KH_GiaiTu'],
                        ['label'=>'5',    'type'=>'single', 'prize'=>'KH_GiaiBa'],
                        ['label'=>'6',    'type'=>'single', 'prize'=>'KH_GiaiNhi'],
                        ['label'=>'7',    'type'=>'single', 'prize'=>'KH_GiaiNhat'],
                        ['label'=>'8/B',  'type'=>'pair',   'p1'=>'KH_B_2D', 'p2'=>'KH_B_3D', 'color'=>'blue'],
                        ['label'=>'9/C',  'type'=>'pair',   'p1'=>'KH_C_2D', 'p2'=>'KH_C_3D', 'color'=>'blue'],
                        ['label'=>'10/D', 'type'=>'pair',   'p1'=>'KH_D_2D', 'p2'=>'KH_D_3D', 'color'=>'blue'],
                    ];
                }
            @endphp

            <div class="rs-wrap">
                <table class="rs-table">
                    <thead class="rs-head">
                        <tr>
                            <td class="rs-label" style="background:#7c2d12;border-color:#7c2d12;">
                                <span class="rs-date">{{ $data['date_show'] }}</span>
                                <input type="hidden" value="{{ $data['date_show'] }}" id="date_result">
                            </td>
                            @foreach($schedule as $sch)
                                <td style="padding:12px 10px;">
                                    <div class="rs-prov-name">{{ $sch['province'] }}</div>
                                    <div class="rs-prov-code">({{ $sch['code'] }})</div>
                                </td>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($displayRows as $rowIdx => $row)
                            @php $rowBg = $rowIdx % 2 === 0 ? 'rs-row-even' : 'rs-row-odd'; @endphp
                            <tr class="{{ $rowBg }}">
                                <td class="rs-label">{{ $row['label'] }}</td>

                                @foreach($schedule as $sch)
                                    <td class="rs-cell">
                                        @if($row['type'] === 'pair')
                                            @php
                                                $prov1 = $result[$row['p1']]['provinces'][$sch['code']] ?? null;
                                                $prov2 = $result[$row['p2']]['provinces'][$sch['code']] ?? null;
                                            @endphp
                                            @if($isMienBac && $row['color'] === 'red')
                                                @php
                                                    $line1 = collect($prov1['row_result'] ?? [])->pluck('winning_number')->map(fn($v) => $v ?? '**')->join(' - ');
                                                    $line2 = collect($prov2['row_result'] ?? [])->pluck('winning_number')->map(fn($v) => $v ?? '***')->join(' - ');
                                                @endphp
                                                <span class="wn wn-red">{{ $line1 }}</span>
                                                <span class="wn wn-red" style="font-size:19px;margin-top:4px;">{{ $line2 }}</span>
                                            @else
                                                @php
                                                    $val1 = $prov1['row_result'][0]['winning_number'] ?? '--';
                                                    $val2 = $prov2['row_result'][0]['winning_number'] ?? '---';
                                                    $cls  = $row['color'] === 'red' ? 'wn-red' : 'wn-blue';
                                                @endphp
                                                <span class="wn {{ $cls }}">{{ $val1 }} - {{ $val2 }}</span>
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
                                                        @endphp
                                                        <span class="wn wn-num">{{ $nums }}</span>
                                                    @endforeach
                                                @else
                                                    @foreach($prov['row_result'] as $rr)
                                                        <span class="wn wn-num">
                                                            {{ $rr['winning_number'] ?? str_repeat('*', $rr['input_length'] ?? 4) }}
                                                        </span>
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
