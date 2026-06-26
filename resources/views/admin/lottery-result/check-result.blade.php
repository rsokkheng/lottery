<x-admin>
    @section('title', 'VND/USD Check Result')
    @include('admin.lottery-result.navbar', ['data' => $data])

    <link href="{{ asset('admin/plugins/datepicker/css/bootstrap-datepicker-1-7-1.min.css') }}" rel="stylesheet"/>

    <div class="tab-content">
        <div class="container tab-pane active"><br>

            {{-- Filter bar --}}
            <div class="d-flex align-items-center gap-2 mb-3 flex-wrap" style="gap:10px">
                <span style="font-weight:600;font-size:13px">Date:</span>
                <input type="text" id="check-date-picker" data-date-format="dd/mm/yyyy"
                       value="{{ $data['current_date'] }}" placeholder="dd/mm/yyyy" readonly
                       style="border:1px solid #ced4da;padding:6px 10px;border-radius:4px;font-size:13px;width:130px">

                <select id="check-region" style="border:1px solid #ced4da;padding:6px 10px;border-radius:4px;font-size:13px">
                    <option value="{{ \App\Enums\HelperEnum::MienNamSlug->value }}"
                        {{ $data['type'] === \App\Enums\HelperEnum::MienNamSlug->value ? 'selected' : '' }}>
                        {{ __('lang.mien-nam') }}
                    </option>
                    <option value="{{ \App\Enums\HelperEnum::MienTrungSlug->value }}"
                        {{ $data['type'] === \App\Enums\HelperEnum::MienTrungSlug->value ? 'selected' : '' }}>
                        {{ __('lang.mien-trung') }}
                    </option>
                    <option value="{{ \App\Enums\HelperEnum::MienBacDienToanSlug->value }}"
                        {{ $data['type'] === \App\Enums\HelperEnum::MienBacDienToanSlug->value ? 'selected' : '' }}>
                        {{ __('lang.mien-bac') }}
                    </option>
                </select>

                <select id="check-currency" style="border:1px solid #ced4da;padding:6px 10px;border-radius:4px;font-size:13px">
                    <option value="VND" {{ $data['currency'] === 'VND' ? 'selected' : '' }}>VND</option>
                    <option value="USD" {{ $data['currency'] === 'USD' ? 'selected' : '' }}>USD</option>
                </select>

                <button id="btn-check-search" class="btn btn-primary btn-sm">Search</button>
            </div>

            {{-- Summary table --}}
            <div class="card">
                <div class="card-header" style="background:var(--vn-red,#DA251D);color:#fff">
                    <h3 class="card-title py-1" style="color:#fff">
                        🇻🇳 {{ $data['currency'] }} Check Result —
                        @if($data['type'] === \App\Enums\HelperEnum::MienNamSlug->value) {{ __('lang.mien-nam') }}
                        @elseif($data['type'] === \App\Enums\HelperEnum::MienTrungSlug->value) {{ __('lang.mien-trung') }}
                        @else {{ __('lang.mien-bac') }}
                        @endif
                        ({{ $data['current_date'] }})
                    </h3>
                </div>
                <div class="card-body p-0">
                    @php
                        $totReceipts = 0;
                        $totAmount   = 0;
                        $totNet      = 0;
                        $totComm     = 0;
                        $totComp     = 0;
                        $totWinLose  = 0;
                        foreach ($data['rows'] as $r) {
                            $totReceipts += $r['total_receipts'];
                            $totAmount   += $r['total_amount'];
                            $totNet      += $r['net_amount'];
                            $totComm     += $r['commission'];
                            $totComp     += $r['compensate'];
                            $totWinLose  += $r['win_lose'];
                        }
                    @endphp

                    <table class="table table-bordered table-striped text-center mb-0" style="font-size:13px">
                        <thead class="bg-dark text-white">
                            <tr>
                                <th class="py-2 px-2">#</th>
                                <th class="py-2 px-2">Province</th>
                                <th class="py-2 px-2">Receipts</th>
                                <th class="py-2 px-2 text-right">Turnover</th>
                                <th class="py-2 px-2 text-right">Commission</th>
                                <th class="py-2 px-2 text-right">Net Amount</th>
                                <th class="py-2 px-2 text-right">Win (Compensate)</th>
                                <th class="py-2 px-2 text-right">Win / Lose</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['rows'] as $i => $row)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="text-left font-weight-bold">
                                        {{ $row['province'] }}
                                        <small class="text-muted">({{ $row['code'] }})</small>
                                    </td>
                                    <td>{{ $row['total_receipts'] }}</td>
                                    <td class="text-right">{{ number_format($row['total_amount'], 2) }}</td>
                                    <td class="text-right">{{ number_format($row['commission'], 2) }}</td>
                                    <td class="text-right">{{ number_format($row['net_amount'], 2) }}</td>
                                    <td class="text-right text-success">{{ number_format($row['compensate'], 2) }}</td>
                                    <td class="text-right font-weight-bold">
                                        <span class="{{ $row['win_lose'] < 0 ? 'text-danger' : 'text-dark' }}">
                                            {{ number_format($row['win_lose'], 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">No schedules found for this date / region.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(count($data['rows']))
                        <tfoot class="bg-secondary text-white font-weight-bold">
                            <tr>
                                <td colspan="2" class="text-center">Total</td>
                                <td>{{ $totReceipts }}</td>
                                <td class="text-right">{{ number_format($totAmount, 2) }}</td>
                                <td class="text-right">{{ number_format($totComm, 2) }}</td>
                                <td class="text-right">{{ number_format($totNet, 2) }}</td>
                                <td class="text-right">{{ number_format($totComp, 2) }}</td>
                                <td class="text-right font-weight-bold">
                                    <span class="{{ $totWinLose < 0 ? 'text-warning' : 'text-white' }}">
                                        {{ number_format($totWinLose, 2) }}
                                    </span>
                                </td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>
                </div>
            </div>

        </div>
    </div>

    @section('js')
    <script>
    $(function () {
        $('#check-date-picker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true,
            todayHighlight: true,
            endDate: '+0d',
        });

        $('#btn-check-search').on('click', function () {
            const date     = $('#check-date-picker').val();
            const region   = $('#check-region').val();
            const currency = $('#check-currency').val();
            if (date) {
                window.location = '{{ route("admin.result.check-result") }}?date=' + date + '&region=' + region + '&currency=' + currency;
            }
        });
    });
    </script>
    @endsection
</x-admin>
