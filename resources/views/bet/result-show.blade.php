<x-app-layout>
    <style>
        .rs-tab { display:inline-block; cursor:pointer; padding:8px 16px; font-weight:700; font-size:.88rem;
                  color:#1e40af; white-space:nowrap; border-radius:8px 8px 0 0; transition:all .15s; }
        .rs-tab.active { background:#1e3a8a; color:#fff; }
        .rs-tab:not(.active):hover { background:#dbeafe; }
        .rs-result-table { width:100%; border-collapse:collapse; text-align:center; }
        .rs-result-table thead td { background:#ca8a04; color:#fff; font-weight:700; padding:10px 6px;
                                     border:2px solid #fff; font-size:.9rem; }
        .rs-result-table tbody tr:nth-child(even) { background:#f9fafb; }
        .rs-result-table tbody tr:nth-child(odd)  { background:#fff; }
        .rs-result-table tbody td { border:1px solid #e5e7eb; padding:6px 4px; vertical-align:top; }
        .rs-prize-label { font-size:1.1rem; font-weight:700; }
        .province-name  { font-size:1.4rem; font-weight:700; }
        .province-code  { font-size:.95rem; }
        .winning-number { line-height:1.5; margin:2px 0; }
        @media (max-width:768px) {
            .rs-result-table { font-size:.72rem; }
            .province-name  { font-size:1rem; }
            .province-code  { font-size:.75rem; }
            .winning-number { font-size:.72rem !important; }
            .rs-prize-label { font-size:.85rem; }
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
            <ul style="list-style:none;margin:0;padding:0;display:flex;gap:4px;padding-top:8px;">
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

        {{-- Results table --}}
        <div style="overflow-x:auto;padding:16px;">
            <table class="rs-result-table">
                <thead>
                    <tr>
                        <td style="min-width:80px;">
                            {{ $data['date_show'] }}
                            <input type="hidden" value="{{ $data['date_show'] }}" id="date_result" name="date_result" />
                        </td>
                        @foreach($data['form_result']['schedule'] as $val)
                            <td style="min-width:80px;">
                                <div class="province-name">{{ $val['province'] }}</div>
                                <div class="province-code">({{ $val['code'] }})</div>
                            </td>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['form_result']['result'] as $prize)
                        <tr>
                            <td class="rs-prize-label">{{ $prize['prize_label'] }}</td>
                            @foreach($prize['provinces'] as $province)
                                <td>
                                    @if($data['type'] === \App\Enums\HelperEnum::MienBacDienToanSlug->value)
                                        @php $c = 1; @endphp
                                        @foreach($province['row_result'] as $row)
                                            @if($c == 1)<div style="display:flex;justify-content:center;gap:4px;margin-bottom:2px;">@endif
                                            <span class="winning-number {{ $row['tailwind_class'] ?? '' }}">{{ $row['winning_number'] ?? '****' }}</span>
                                            @if($c >= $row['col_count'])</div>@php $c = 0; @endphp@endif
                                            @php $c++; @endphp
                                        @endforeach
                                    @else
                                        @foreach($province['row_result'] as $row)
                                            <div class="winning-number {{ $row['tailwind_class'] ?? '' }}">{{ $row['winning_number'] ?? '****' }}</div>
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

    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <script>
        $(function () {
            $('#date_input').on('change', function () {
                const d = new Date($(this).val());
                const fmt = String(d.getDate()).padStart(2,'0') + '/' +
                            String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
                $('#date_result').val(fmt);
                window.location = $('#href_show_result').attr('href') + '?date=' + fmt + '&region=' + $('#hidden_region').val();
            });
        });

        function goShowResult(region) {
            const raw = $('#date_input').val();
            let fmt = $('#date_result').val();
            if (raw) {
                const d = new Date(raw);
                fmt = String(d.getDate()).padStart(2,'0') + '/' + String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
            }
            window.location = $('#href_show_result').attr('href') + '?date=' + fmt + '&region=' + region;
        }
    </script>
</x-app-layout>
