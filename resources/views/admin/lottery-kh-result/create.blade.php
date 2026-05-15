<x-admin>
    @section('title', 'KHR Lottery Result')
    @include('admin.lottery-kh-result.navbar', ['data' => $data])

    <style>
        .pair-wrap  { display:flex; align-items:center; justify-content:center; gap:4px; }
        .pair-sep   { color:#6b7280; font-weight:600; font-size:13px; flex-shrink:0; }
        .inp-2d, .inp-3d { width:50px !important; }
        .stack-wrap { display:flex; flex-direction:column; gap:2px; align-items:center; }
        .input-wrap { display:flex; align-items:center; justify-content:center; }
        .bac-grid   { display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:2px; }
        .row-order  { color:#9ca3af; font-size:10px; padding-right:2px; }
        .kh-date-row { display:flex; align-items:center; gap:10px; margin-bottom:10px; }
        .kh-date-row input[type=text] { border:1px solid #ced4da; padding:6px 10px; border-radius:4px; font-size:13px; width:130px; }
        .kh-date-row button { background:#007bff; color:#fff; border:none; padding:6px 14px; border-radius:4px; cursor:pointer; font-size:13px; }
        .kh-date-row button:hover { background:#0056b3; }
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
                    <h4 class="py-2">Entry result of {{ __('lang.mien-nam') }} (VND)</h4>
                @elseif($data['type']===\App\Enums\HelperEnum::MienTrungSlug->value)
                    <h4 class="py-2">Entry result of {{ __('lang.mien-trung') }} (VND)</h4>
                @else
                    <h4 class="py-2">Entry result of {{ __('lang.mien-bac') }} (VND)</h4>
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
                                            if ($row['type'] === 'pair' && ($row['color'] ?? '') === 'red') $cellClass = 'text-danger p-1';
                                        @endphp
                                        <td class="{{ $cellClass }}">
                                            @if($row['type'] === 'pair')
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
                                                        placeholder="{{ str_repeat('0', $len1) }}"
                                                        autocomplete="off">
                                                    <span class="pair-sep">−</span>
                                                    <input type="text"
                                                        class="form-control border-0 shadow-none kh-win-number inp-3d"
                                                        name="[{{ $pCode }}][{{ $row['p2'] }}][{{ $schId }}][1]"
                                                        value="{{ $rr2['winning_number'] ?? '' }}"
                                                        maxlength="{{ $len2 }}"
                                                        data-max-length="{{ $len2 }}"
                                                        placeholder="{{ str_repeat('0', $len2) }}"
                                                        autocomplete="off">
                                                </div>
                                            @else
                                                @php
                                                    $prov = $result[$row['prize']]['provinces'][$sch['code']] ?? null;
                                                @endphp
                                                @if($prov)
                                                    @if($isMienBac)
                                                        <div class="bac-grid">
                                                            @foreach($prov['row_result'] as $rr)
                                                                <div class="input-wrap">
                                                                    <p class="row-order pt-2">{{ $rr['result_order'] }}</p>
                                                                    <input type="text"
                                                                        class="form-control border-0 shadow-none kh-win-number"
                                                                        style="width:{{ $rr['input_length'] <= 3 ? 44 : ($rr['input_length'] <= 4 ? 52 : 60) }}px"
                                                                        name="[{{ $prov['province_code'] }}][{{ $row['prize'] }}][{{ $prov['schedule_id'] }}][{{ $rr['result_order'] }}]"
                                                                        value="{{ $rr['winning_number'] ?? '' }}"
                                                                        maxlength="{{ $rr['input_length'] ?? 0 }}"
                                                                        data-max-length="{{ $rr['input_length'] ?? 0 }}"
                                                                        placeholder="0"
                                                                        autocomplete="off">
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="stack-wrap">
                                                            @foreach($prov['row_result'] as $rr)
                                                                <div class="input-wrap">
                                                                    @if(count($prov['row_result']) > 1)
                                                                        <p class="row-order pt-2">{{ $rr['result_order'] }}</p>
                                                                    @endif
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
                                                        </div>
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
            const pastedText = ((e.originalEvent || e).clipboardData || window.clipboardData)
                               .getData('text').replace(/\s+/g, '');
            e.preventDefault();

            let charIndex = 0;
            const $start  = $(this);
            const colIdx  = $start.closest('td').index();
            const $table  = $start.closest('table');
            let started   = false;

            $table.find('tr').each(function () {
                const $inputs = $(this).find('td').eq(colIdx).find('.kh-win-number');
                $inputs.each(function () {
                    if (!started) {
                        if (this === $start[0]) started = true;
                        else return;
                    }
                    if (charIndex >= pastedText.length) return;
                    const maxLen = parseInt($(this).attr('maxlength')) || pastedText.length;
                    const part   = pastedText.slice(charIndex, charIndex + maxLen);
                    $(this).val(part);
                    charIndex += part.length;
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
                if (!val || val.length !== max) {
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
