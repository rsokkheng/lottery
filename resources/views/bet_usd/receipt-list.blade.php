<x-app-layout>
    <link href="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('admin/plugins/toastr/css/toastr.min.css') }}">

    <div class="bp-wrap">
        <div class="bp-page-header">
            <div>
                <span class="bp-badge bp-badge-usd">Lotto Vietnam · USD</span>
                <div class="bp-page-title">{{ __('message.receipt_no') }} List</div>
            </div>
        </div>

        <div class="bp-filter">
            <div class="bp-filter-icon">
                <svg aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20"><path d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z"/></svg>
                <input id="datepicker-receipt" value="{{ $date }}" datepicker datepicker-buttons
                    datepicker-autoselect-today datepicker-autohide datepicker-format="yyyy-mm-dd" type="text"
                    style="width:160px;" placeholder="{{ __('message.date') }}">
            </div>
            <input type="text" id="receipt-no" value="{{ $no }}" style="width:160px;"
                placeholder="{{ __('message.receipt_no') }}">
            <button class="bp-search-btn" onclick="searchReceipt('{{ route('bet-usd.receipt-list') }}')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;"><circle cx="11" cy="11" r="7"/><path stroke-linecap="round" d="m16.5 16.5 5 5"/></svg>
                {{ __('message.search') }}
            </button>
        </div>

        <div class="bp-table-wrap" style="padding:0 0 8px;">
            <table class="bp-table">
                <thead>
                    <tr>
                        <th>{{ __('message.no') }}</th>
                        <th>{{ __('message.receipt_no') }}</th>
                        <th>{{ __('message.account') }}</th>
                        <th>{{ __('message.date') }}</th>
                        <th>{{ __('message.currency') }}</th>
                        <th class="bp-num">{{ __('message.total_amount') }}</th>
                        <th class="bp-num">{{ __('message.commission') }}</th>
                        <th class="bp-num">{{ __('message.net_amount') }}</th>
                        <th class="bp-num">{{ __('message.compensate') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if (isset($data) && count($data))
                        @php
                            $totalAmount     = 0;
                            $totalCommission = 0;
                            $totalNetAmount  = 0;
                            $totalCompensate = 0;
                        @endphp
                        @foreach ($data as $key => $row)
                            @php
                                $totalAmount     += $row->total_amount ?? 0;
                                $totalCommission += $row->commission ?? 0;
                                $totalNetAmount  += $row->net_amount ?? 0;
                                $totalCompensate += $row->compensate ?? 0;
                            @endphp
                            <tr class="{{ $row->compensate > 0 ? 'bp-win' : '' }}">
                                <td class="bp-center">{{ $key + 1 }}</td>
                                <td onclick="handleShowBet('{{ $row->receipt_id }}')" class="bp-center">
                                    <a href="#" onclick="return false;" style="color:#059669;font-weight:600;">
                                        {{ $row->receipt_no ?? '' }}
                                    </a>
                                </td>
                                <td>{{ $row->account ?? '' }}</td>
                                <td>{{ $row->bet_date ?? '' }}</td>
                                <td class="bp-center">USD</td>
                                <td class="bp-num">{{ number_format($row->total_amount, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row->commission, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row->net_amount, 3, '.', '') }}</td>
                                <td class="bp-num">{{ number_format($row->compensate, 3, '.', '') }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="5" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;text-align:center;">Total</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalAmount, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCommission, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalNetAmount, 3, '.', '') }}</td>
                            <td class="bp-num" style="background:#f8f9fc;border-top:2px solid #e2e8f0;font-weight:700;">{{ number_format($totalCompensate, 3, '.', '') }}</td>
                        </tr>
                    @else
                        <tr><td colspan="9" class="bp-empty">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                            <p>No data found</p>
                        </td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Receipt detail modal --}}
    <div id="static-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full"
        style="background:rgba(0,0,0,.45);">
        <div class="relative p-3 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-sm" style="border:1px solid #e2e8f0;">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h6 style="font-size:.95rem;font-weight:700;color:#1e293b;margin:0;">
                        {{ __('message.receipt_no') }}: <span id="receipt_no" style="color:#059669;"></span>
                    </h6>
                    <button type="button" style="background:none;border:none;cursor:pointer;color:#6b7280;padding:4px;" onclick="closeReceiptModal()">
                        <svg style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="p-4 w-full">
                    <table class="bp-table">
                        <thead>
                            <tr>
                                <th>{{ __('message.number') }}</th>
                                <th>{{ __('message.company') }}</th>
                                <th class="bp-num">{{ __('message.amount') }}</th>
                            </tr>
                        </thead>
                        <tbody id="getBetByReceipt"></tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="font-weight:700;">{{ __('message.total_amount') }}</td>
                                <td class="bp-num" id="totalAmount">0.00</td>
                            </tr>
                            <tr>
                                <td colspan="2" style="font-weight:700;">{{ __('message.due_amount') }}</td>
                                <td class="bp-num" id="dueAmount">0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="flex justify-end gap-2 p-4 border-t border-gray-200">
                    <button id="btn_pay" style="display:none;background:#16a34a;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-weight:600;cursor:pointer;"
                        type="button" onclick="payReceipt()">{{ __('Pay') }}</button>
                    <button type="button" onclick="printReceipt()"
                        style="background:#064e3b;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-weight:600;cursor:pointer;">{{ __('Print') }}</button>
                    <button type="button" onclick="closeReceiptModal()"
                        style="background:#e2e8f0;color:#374151;border:none;border-radius:8px;padding:8px 18px;font-weight:600;cursor:pointer;">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('admin/plugins/datepicker/flowbite/flowbite.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/toastr/js/toastr.min.js') }}"></script>
    <script>
        toastr.options = { "progressBar": true, "closeButton": true };

        function searchReceipt(url) {
            const date = $('#datepicker-receipt').val();
            const no   = $('#receipt-no').val();
            if (date.length || no.length) {
                window.location = url + '?date=' + date + '&no=' + no;
            }
        }

        function showReceiptModal() {
            const m = document.getElementById('static-modal');
            m.classList.remove('hidden'); m.classList.add('flex');
        }
        function closeReceiptModal() {
            const m = document.getElementById('static-modal');
            m.classList.add('hidden'); m.classList.remove('flex');
        }

        function handleShowBet(id) {
            document.getElementById('getBetByReceipt').innerHTML = '<tr><td colspan="3" style="text-align:center;padding:12px;">Loading...</td></tr>';
            showReceiptModal();
            fetch(`/lotto_usd/bet/${id}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('receipt_no').innerText = data?.no_receipt;
                    const tbody = document.getElementById('getBetByReceipt');
                    tbody.innerHTML = '';
                    let existWin = false;
                    data?.items?.forEach(item => {
                        const winClass = Boolean(item?.is_win) ? (existWin = true, 'bp-win') : '';
                        tbody.innerHTML += `<tr class="${winClass}">
                            <td>${item?.number ?? ''}</td>
                            <td>${item?.company ?? ''}</td>
                            <td class="bp-num">${item?.amount ?? ''}</td>
                        </tr>`;
                    });
                    document.getElementById('btn_pay').style.display = (!(data?.is_paid) && existWin) ? '' : 'none';
                    document.getElementById('totalAmount').innerText = Number(data?.totalAmount).toFixed(2) + ' (USD)';
                    document.getElementById('dueAmount').innerText = Number(data?.dueAmount).toFixed(2) + ' (USD)';
                })
                .catch(e => { console.error(e); closeReceiptModal(); });
        }

        function printReceipt() {
            const receiptNo = document.getElementById('receipt_no')?.innerText;
            if (!receiptNo) { alert("Receipt number not found!"); return; }
            const w = window.open('/lotto_usd/bet_receipt/' + receiptNo, '_blank');
            if (!w) { alert('Popup blocked! Please allow popups for this site.'); return; }
            w.onload = () => setTimeout(() => { w.print(); w.onafterprint = () => w.close(); }, 500);
        }

        function payReceipt() {
            const receipt_no = $('#receipt_no').text();
            fetch(`/bet_receipt_pay_usd/${receipt_no}`)
                .then(r => r.json())
                .then(data => data?.success ? toastr.success('Receipt was paid!') : toastr.error('Internal error!'))
                .catch(e => console.error(e));
        }
    </script>
</x-app-layout>
