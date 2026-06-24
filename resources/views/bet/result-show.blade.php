<x-app-layout>
    <style>
        /* ── Tabs ── */
        .rs-tab { display:inline-block; cursor:pointer; padding:9px 18px; font-weight:700; font-size:.88rem;
                  color:#92400e; white-space:nowrap; border-radius:8px 8px 0 0; transition:all .15s; }
        .rs-tab.active { background:#78350f; color:#fff; }
        .rs-tab:not(.active):hover { background:#fef3c7; }

        /* ── Result table ── */
        .rs-wrap { max-width:960px; margin:20px auto; border-radius:12px; overflow:hidden;
                   box-shadow:0 4px 20px rgba(0,0,0,.12); border:1px solid #e2e8f0; }
        .rs-table { width:100%; border-collapse:collapse; text-align:center; }
        .rs-table td { border:1px solid #e5e7eb; vertical-align:middle; padding:0; }

        /* Header */
        .rs-head { background:linear-gradient(135deg,#b45309 0%,#92400e 100%); }
        .rs-head td { border-color:#c0714a; color:#fff; padding:16px 14px; }
        .rs-date      { font-size:1rem; font-weight:700; letter-spacing:.03em; }
        .rs-prov-name { font-size:1.4rem; font-weight:800; letter-spacing:.01em; }
        .rs-prov-code { font-size:.82rem; opacity:.85; margin-top:2px; }

        /* Prize label column */
        .rs-label { width:110px; min-width:110px; background:#fffbf0;
                    border-right:3px solid #e5e7eb; font-size:.92rem; font-weight:800;
                    color:#374151; padding:16px 10px; letter-spacing:.01em; }

        /* Data cells */
        .rs-cell { padding:14px 16px; }
        .rs-row-even { background:#fff; }
        .rs-row-odd  { background:#f9fafb; }
        .rs-row-even:hover, .rs-row-odd:hover { background:#fef9ec; }

        /* Winning numbers */
        .wn      { display:block; line-height:2; text-align:center; }
        .wn-red  { color:#dc2626; font-weight:800; font-size:22px; }
        .wn-blue { color:#1d4ed8; font-weight:800; font-size:20px; }
        .wn-num  { color:#1e293b; font-weight:700; font-size:18px; }

        @media (max-width:640px) {
            .rs-wrap      { margin:10px 8px; border-radius:8px; }
            .rs-prov-name { font-size:1rem; }
            .rs-label     { width:72px; min-width:72px; font-size:.78rem; padding:12px 6px; }
            .rs-cell      { padding:10px 6px; }
            .wn-red       { font-size:16px; }
            .wn-blue      { font-size:15px; }
            .wn-num       { font-size:14px; }
        }
    </style>

    <div class="bp-wrap">
        <div class="bp-page-header">
            <div>
                <span class="bp-badge bp-badge-vn">BET VIETNAM &middot; VND</span>
                <div class="bp-page-title">Lottery Results</div>
            </div>
            <div>
                <a class="hidden" id="href_show_result" href="{{ route('bet.result-show') }}"></a>
                <input type="date" id="date_input"
                    style="border:1px solid #d1d8e0;border-radius:8px;padding:6px 12px;font-size:.85rem;outline:none;"
                    value="{{ date('Y-m-d', strtotime(str_replace('/', '-', $data['date_show']))) }}"
                    max="{{ date('Y-m-d') }}">
            </div>
        </div>

        {{-- Tab navigation --}}
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
                    // MienBac: top→bottom = DB, Nhất, Nhì, Ba, Tư, Năm, Sáu, Bảy
                    $displayRows = [
                        ['label'=>'Đặc Biệt', 'prize'=>'GiaiDB',   'color'=>'red'],
                        ['label'=>'Nhất',      'prize'=>'GiaiNhat', 'color'=>'num'],
                        ['label'=>'Nhì',       'prize'=>'GiaiNhi',  'color'=>'num'],
                        ['label'=>'Ba',        'prize'=>'GiaiBa',   'color'=>'num'],
                        ['label'=>'Tư',        'prize'=>'GiaiTu',   'color'=>'num'],
                        ['label'=>'Năm',       'prize'=>'GiaiNam',  'color'=>'num'],
                        ['label'=>'Sáu',       'prize'=>'GiaiSau',  'color'=>'blue'],
                        ['label'=>'Bảy',       'prize'=>'GiaiBay',  'color'=>'red'],
                    ];
                } else {
                    // MienNam / MienTrung: top→bottom = Tám … Đặc Biệt
                    $displayRows = [
                        ['label'=>'Tám',       'prize'=>'GiaiTam',  'color'=>'red'],
                        ['label'=>'Bảy',       'prize'=>'GiaiBay',  'color'=>'blue'],
                        ['label'=>'Sáu',       'prize'=>'GiaiSau',  'color'=>'num'],
                        ['label'=>'Năm',       'prize'=>'GiaiNam',  'color'=>'num'],
                        ['label'=>'Tư',        'prize'=>'GiaiTu',   'color'=>'num'],
                        ['label'=>'Ba',        'prize'=>'GiaiBa',   'color'=>'num'],
                        ['label'=>'Nhì',       'prize'=>'GiaiNhi',  'color'=>'num'],
                        ['label'=>'Nhất',      'prize'=>'GiaiNhat', 'color'=>'num'],
                        ['label'=>'Đặc Biệt',  'prize'=>'GiaiDB',   'color'=>'red'],
                    ];
                }
            @endphp

            <div class="rs-wrap">
                <table class="rs-table">
                    <thead class="rs-head">
                        <tr>
                            <td class="rs-label" style="background:#7c2d12;border-color:#7c2d12;color:#fff;">
                                <span class="rs-date">{{ $data['date_show'] }}</span>
                                <input type="hidden" value="{{ $data['date_show'] }}" id="date_result" name="date_result"/>
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
                                        @php
                                            $prov = $result[$row['prize']]['provinces'][$sch['code']] ?? null;
                                            $cls  = match($row['color']) {
                                                'red'  => 'wn-red',
                                                'blue' => 'wn-blue',
                                                default => 'wn-num',
                                            };
                                        @endphp
                                        @if($prov)
                                            @php
                                                $rrList   = $prov['row_result'];
                                                $colCount = $rrList[0]['col_count'] ?? 1;
                                                $inpLen   = $rrList[0]['input_length'] ?? 4;
                                                $chunks   = array_chunk($rrList, $colCount);
                                            @endphp
                                            @foreach($chunks as $chunk)
                                                @php
                                                    $nums = collect($chunk)
                                                        ->pluck('winning_number')
                                                        ->map(fn($v) => $v ?? str_repeat('-', $inpLen))
                                                        ->join(' - ');
                                                @endphp
                                                <span class="wn {{ $cls }}">{{ $nums }}</span>
                                            @endforeach
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
