<x-admin>
    @section('title', 'KHR Lottery Result')
    @include('admin.lottery-kh-result.navbar', ['data' => $data])

    <style>
        .pair-wrap  { display:flex; align-items:center; justify-content:center; gap:6px; }
        .pair-sep   { color:#6b7280; font-weight:600; font-size:15px; flex-shrink:0; }
        .inp-2d     { width:64px !important; height:42px !important; font-size:16px !important; text-align:center; }
        .inp-3d     { width:72px !important; height:42px !important; font-size:16px !important; text-align:center; }
        .stack-wrap { display:flex; flex-direction:column; gap:4px; align-items:center; }
        .input-wrap { display:flex; align-items:center; justify-content:center; }
        .bac-grid   { display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:4px; }
        .row-order  { color:#9ca3af; font-size:11px; padding-right:2px; }
        .kh-date-row { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
        .kh-date-row input[type=text] { border:1px solid #ced4da; padding:6px 10px; border-radius:4px; font-size:13px; width:130px; }
        .kh-date-row button { background:#007bff; color:#fff; border:none; padding:6px 14px; border-radius:4px; cursor:pointer; font-size:13px; }
        .kh-date-row button:hover { background:#0056b3; }
        /* bigger inputs across all cells */
        .kh-win-number { height:42px !important; font-size:16px !important; font-weight:600; text-align:center; padding:2px 4px !important; }
        table.table td { vertical-align:middle; padding:10px 6px; }
        table.table td:first-child { font-size:15px; font-weight:700; min-width:60px; }
    </style>

    <link href="{{ asset('admin/plugins/datepicker/css/bootstrap-datepicker-1-7-1.min.css') }}" rel="stylesheet"/>

    <div class="tab-content">
        <div class="container tab-pane active">
            <div style="width:100%">

                <div class="kh-date-row">
                    <span style="font-weight:600;font-size:13px">Date:</span>
                    <input type="text" id="kh-date-picker" data-date-format="dd/mm/yyyy"
                           value="{{ $data['current_date'] }}" placeholder="dd/mm/yyyy" readonly>
                    <button id="btn-go-date">Go</button>
                    <a href="{{ route($data['url']['index']) }}" class="btn btn-sm btn-secondary">← Back</a>
                </div>

                @if($data['type']===\App\Enums\HelperEnum::MienNamSlug->value)
                    <h4 class="py-2">Entry result of {{ __('lang.mien-nam') }} (Cambodia)</h4>
                @elseif($data['type']===\App\Enums\HelperEnum::MienTrungSlug->value)
                    <h4 class="py-2">Entry result of {{ __('lang.mien-trung') }} (Cambodia)</h4>
                @else
                    <h4 class="py-2">Entry result of {{ __('lang.mien-bac') }} (Cambodia)</h4>
                @endif

                <form id="kh-form-result">
                    <input type="hidden" id="kh-region"    value="{{ $data['type'] }}">
                    <input type="hidden" id="kh-date"      value="{{ $data['current_date'] }}">
                    <input type="hidden" id="kh-index-url" value="{{ route($data['url']['index']) }}">

                    @php
                        $isMienBac = $data['type'] === \App\Enums\HelperEnum::MienBacDienToanSlug->value;
                        $result    = $data['form_result']['result'];
                        $schedule  = $data['form_result']['schedule'];

                        if ($isMienBac) {
                            $displayRows = [
                                // bac-top: two stacked sub-rows — p1 (KH_GiaiBay, 4×2D) above p2 (KH_GiaiSau, 3×3D)
                                ['label'=>'1/A', 'type'=>'bac-top', 'p1'=>'KH_GiaiBay', 'p2'=>'KH_GiaiSau', 'color'=>'red'],
                                ['label'=>'2',   'type'=>'single',  'prize'=>'KH_GiaiNam'],
                                ['label'=>'3',   'type'=>'single',  'prize'=>'KH_GiaiTu'],
                                ['label'=>'4',   'type'=>'single',  'prize'=>'KH_GiaiBa'],
                                ['label'=>'5',   'type'=>'single',  'prize'=>'KH_GiaiNhi'],
                                ['label'=>'6',   'type'=>'single',  'prize'=>'KH_GiaiNhat'],
                                ['label'=>'7/B', 'type'=>'pair',    'p1'=>'KH_B_2D', 'p2'=>'KH_B_3D', 'color'=>'blue', 'optional'=>true],
                                ['label'=>'8/C', 'type'=>'pair',    'p1'=>'KH_C_2D', 'p2'=>'KH_C_3D', 'color'=>'blue', 'optional'=>true],
                                ['label'=>'9/D', 'type'=>'pair',    'p1'=>'KH_D_2D', 'p2'=>'KH_D_3D', 'color'=>'blue', 'optional'=>true],
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
                                ['label'=>'8/B',  'type'=>'pair',   'p1'=>'KH_B_2D', 'p2'=>'KH_B_3D', 'color'=>'blue', 'optional'=>true],
                                ['label'=>'9/C',  'type'=>'pair',   'p1'=>'KH_C_2D', 'p2'=>'KH_C_3D', 'color'=>'blue', 'optional'=>true],
                                ['label'=>'10/D', 'type'=>'pair',   'p1'=>'KH_D_2D', 'p2'=>'KH_D_3D', 'color'=>'blue', 'optional'=>true],
                            ];
                        }
                    @endphp

                    <table class="table table-bordered rounded-lg text-center table-striped" style="width:100%">
                        <thead class="bg-dark">
                            <tr>
                                <td class="text-white">{{ $data['current_date'] }}</td>
                                @foreach($schedule as $sch)
                                    <td class="text-white">
                                        <div>{{ $sch['province'] }}</div>
                                        <div class="text-sm">({{ $sch['code'] }})</div>
                                    </td>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($displayRows as $row)
                                <tr>
                                    <td class="text-black">{{ $row['label'] }}</td>

                                    @foreach($schedule as $sch)
                                        @php
                                            $cellClass = 'text-primary p-1';
                                            if (in_array($row['type'], ['pair','bac-top']) && ($row['color'] ?? '') === 'red') $cellClass = 'text-danger p-1';
                                        @endphp
                                        <td class="{{ $cellClass }}">
                                            @if($row['type'] === 'bac-top')
                                                {{-- MienBac 1/A: GiaiBay (4×2D) row above GiaiSau (3×3D) row --}}
                                                @php
                                                    $prov1 = $result[$row['p1']]['provinces'][$sch['code']] ?? null;
                                                    $prov2 = $result[$row['p2']]['provinces'][$sch['code']] ?? null;
                                                    $pCode = $sch['code'];
                                                @endphp
                                                @if($prov1)
                                                    <div class="bac-grid mb-1">
                                                        @foreach($prov1['row_result'] as $rr)
                                                            @php $len = $rr['input_length'] ?? 2; $w = $len <= 2 ? 56 : ($len <= 3 ? 68 : 80); @endphp
                                                            @if(!$loop->first)<span class="pair-sep">−</span>@endif
                                                            <input type="text"
                                                                class="form-control border-0 shadow-none kh-win-number"
                                                                style="width:{{ $w }}px"
                                                                name="[{{ $pCode }}][{{ $row['p1'] }}][{{ $prov1['schedule_id'] }}][{{ $rr['result_order'] }}]"
                                                                value="{{ $rr['winning_number'] ?? '' }}"
                                                                maxlength="{{ $len }}"
                                                                data-max-length="{{ $len }}"
                                                                placeholder="{{ str_repeat('0', $len) }}"
                                                                autocomplete="off">
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($prov2)
                                                    <div class="bac-grid">
                                                        @foreach($prov2['row_result'] as $rr)
                                                            @php $len = $rr['input_length'] ?? 3; $w = $len <= 2 ? 56 : ($len <= 3 ? 68 : 80); @endphp
                                                            @if(!$loop->first)<span class="pair-sep">−</span>@endif
                                                            <input type="text"
                                                                class="form-control border-0 shadow-none kh-win-number"
                                                                style="width:{{ $w }}px"
                                                                name="[{{ $pCode }}][{{ $row['p2'] }}][{{ $prov2['schedule_id'] }}][{{ $rr['result_order'] }}]"
                                                                value="{{ $rr['winning_number'] ?? '' }}"
                                                                maxlength="{{ $len }}"
                                                                data-max-length="{{ $len }}"
                                                                placeholder="{{ str_repeat('0', $len) }}"
                                                                autocomplete="off">
                                                        @endforeach
                                                    </div>
                                                @endif
                                            @elseif($row['type'] === 'pair')
                                                @php
                                                    $prov1 = $result[$row['p1']]['provinces'][$sch['code']] ?? null;
                                                    $prov2 = $result[$row['p2']]['provinces'][$sch['code']] ?? null;
                                                    $rr1   = $prov1['row_result'][0] ?? [];
                                                    $rr2   = $prov2['row_result'][0] ?? [];
                                                    $len1  = $rr1['input_length'] ?? 2;
                                                    $len2  = $rr2['input_length'] ?? 3;
                                                    $schId = $prov1['schedule_id'] ?? ($sch['id'] ?? '');
                                                    $pCode = $sch['code'];
                                                @endphp
                                                <div class="pair-wrap">
                                                    <input type="text"
                                                        class="form-control border-0 shadow-none kh-win-number inp-2d"
                                                        name="[{{ $pCode }}][{{ $row['p1'] }}][{{ $schId }}][1]"
                                                        value="{{ $rr1['winning_number'] ?? '' }}"
                                                        maxlength="{{ $len1 }}"
                                                        data-max-length="{{ $len1 }}"
                                                        @if(!empty($row['optional'])) data-optional="true" @endif
                                                        placeholder="{{ str_repeat('0', $len1) }}"
                                                        autocomplete="off">
                                                    <span class="pair-sep">−</span>
                                                    <input type="text"
                                                        class="form-control border-0 shadow-none kh-win-number inp-3d"
                                                        name="[{{ $pCode }}][{{ $row['p2'] }}][{{ $schId }}][1]"
                                                        value="{{ $rr2['winning_number'] ?? '' }}"
                                                        maxlength="{{ $len2 }}"
                                                        data-max-length="{{ $len2 }}"
                                                        @if(!empty($row['optional'])) data-optional="true" @endif
                                                        placeholder="{{ str_repeat('0', $len2) }}"
                                                        autocomplete="off">
                                                </div>
                                            @else
                                                @php
                                                    $prov = $result[$row['prize']]['provinces'][$sch['code']] ?? null;
                                                @endphp
                                                @if($prov)
                                                    @if($isMienBac)
                                                        @php
                                                            $colCount = $prov['row_result'][0]['col_count'] ?? count($prov['row_result']);
                                                            $chunks   = array_chunk($prov['row_result'], $colCount);
                                                        @endphp
                                                        @foreach($chunks as $chunk)
                                                        <div class="bac-grid {{ !$loop->last ? 'mb-1' : '' }}">
                                                            @foreach($chunk as $rr)
                                                                @php $w = ($rr['input_length'] ?? 4) <= 4 ? 72 : 84; @endphp
                                                                @if(!$loop->first)<span class="pair-sep">−</span>@endif
                                                                <input type="text"
                                                                    class="form-control border-0 shadow-none kh-win-number"
                                                                    style="width:{{ $w }}px"
                                                                    name="[{{ $prov['province_code'] }}][{{ $row['prize'] }}][{{ $prov['schedule_id'] }}][{{ $rr['result_order'] }}]"
                                                                    value="{{ $rr['winning_number'] ?? '' }}"
                                                                    maxlength="{{ $rr['input_length'] ?? 0 }}"
                                                                    data-max-length="{{ $rr['input_length'] ?? 0 }}"
                                                                    placeholder="0"
                                                                    autocomplete="off">
                                                            @endforeach
                                                        </div>
                                                        @endforeach
                                                    @else
                                                        @foreach($prov['row_result'] as $rr)
                                                            <div class="d-flex w-full justify-content-center">
                                                                <p class="pr-1 pt-2 text-secondary" style="height:100%!important">{{ $rr['result_order'] }}</p>
                                                                <input type="text"
                                                                    class="form-control border-0 shadow-none kh-win-number"
                                                                    name="[{{ $prov['province_code'] }}][{{ $row['prize'] }}][{{ $prov['schedule_id'] }}][{{ $rr['result_order'] }}]"
                                                                    value="{{ $rr['winning_number'] ?? '' }}"
                                                                    maxlength="{{ $rr['input_length'] ?? 0 }}"
                                                                    data-max-length="{{ $rr['input_length'] ?? 0 }}"
                                                                    placeholder="0"
                                                                    autocomplete="off">
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

                    <div class="d-flex mt-4 justify-content-end">
                        <button type="submit" id="btn-kh-save" class="m-2 btn btn-success btn-md">Save</button>
                        <button type="button" id="btn-kh-clear" class="m-2 btn btn-secondary btn-md">Clear</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    @section('js')
    <script>
    $(function () {
        $('#kh-date-picker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            endDate: '+0d',
        });

        $('#btn-go-date').on('click', function () {
            const d = $('#kh-date-picker').val();
            if (d) window.location = '{{ route($data["url"]["create"]) }}?date=' + d;
        });

        $('.kh-win-number').each(function () {
            const max = parseInt($(this).data('max-length')) || 0;
            $(this).on('input', function () {
                $(this).val($(this).val().replace(/[^0-9]/g, '').substring(0, max));
            });
        });

        $('table').on('paste', '.kh-win-number', function (e) {
            e.preventDefault();
            const raw = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('text');
            const pastedText = raw.replace(/\D/g, '');
            if (!pastedText) return;

            let charIndex = 0;
            const $start  = $(this);
            const colIdx  = $start.closest('td').index();
            const $table  = $start.closest('table');
            let started   = false;

            $table.find('tr').each(function () {
                if (charIndex >= pastedText.length) return false;
                const $td = $(this).find('td').eq(colIdx);
                if (!$td.length) return;

                $td.find('.kh-win-number').each(function () {
                    if (!started) {
                        if (this === $start[0]) started = true;
                        else return;
                    }
                    if (charIndex >= pastedText.length) return false;
                    const maxLen = parseInt($(this).attr('maxlength')) || pastedText.length;
                    $(this).val(pastedText.slice(charIndex, charIndex + maxLen));
                    charIndex += maxLen;
                });
            });
        });

        $('#btn-kh-clear').on('click', function () {
            $('.kh-win-number').val('').removeClass('is-invalid');
        });

        $('#kh-form-result').on('submit', function (e) {
            e.preventDefault();
            $('#btn-kh-save').prop('disabled', true);

            const resultDate = $('#kh-date').val();
            const region     = $('#kh-region').val();
            const indexUrl   = $('#kh-index-url').val();
            let rows = [], isInvalid = false;

            $('.kh-win-number').each(function () {
                const name = $(this).attr('name') || '';
                const val  = $(this).val();
                const max  = parseInt($(this).data('max-length')) || 0;
                const arr  = name.match(/\[(.*?)\]/g)?.map(s => s.replace(/\[|\]/g, '')) || [];

                $(this).removeClass('is-invalid');
                if (!val) { return; }
                if (val.length !== max) {
                    $(this).addClass('is-invalid');
                    isInvalid = true;
                    return;
                }
                rows.push({
                    result_date:    resultDate,
                    winning_number: val,
                    province_code:  arr[0] ?? '',
                    prize_level:    arr[1] ?? '',
                    schedule_id:    arr[2] ?? '',
                    result_order:   arr[3] ?? '',
                });
            });

            if (isInvalid) {
                toastr.warning('Invalid input');
                $('#btn-kh-save').prop('disabled', false);
                return;
            }

            $.ajax({
                url: '{{ route("admin.result.store-winning-result-kh") }}',
                type: 'POST',
                data: {
                    _token:        '{{ csrf_token() }}',
                    data:          rows,
                    result_region: region,
                },
                dataType: 'json',
                success: function (res) {
                    if (res.success) {
                        toastr.success('Save successfully!');
                        setTimeout(() => { window.location = indexUrl; }, 800);
                    } else {
                        toastr.warning(res.message || 'Invalid input!');
                        $('#btn-kh-save').prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    toastr.warning(xhr?.statusText);
                    $('#btn-kh-save').prop('disabled', false);
                }
            });
        });
    });
    </script>
    @endsection
</x-admin>
