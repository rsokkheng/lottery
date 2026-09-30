{{-- Shared 80mm smart mini POS receipt. Expects: $currency, $back_url + receipt data from printReceiptNo() --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $receipt_no }}</title>
    <style>
        /* Set 80mm receipt print size */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            width: 80mm;
            margin: 0;
            padding: 10px;
            text-align: center;
        }

        .receipt {
            width: 100%;
            text-align: left;
            border-bottom: 1px dashed black;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: center;
        }
        .details {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            font-size: 15px;
            padding: 5px;
            text-align: center;
            border: 1px solid black; /* Added border */
        }

        .total-row {
            font-weight: bold;
        }

        .footer1 {
            font-size: 10px;
            margin-top: 10px;
        }
        .footer {
            font-size: 12px;
            margin-top: 10px;
        }
        .footer_bold{
            margin-top: 10px;
            font-size: 14px;
            font-weight: bold;
        }

        /* Screen-only toolbar: phone chooses "print here" or "send to Mini POS" */
        .pos-toolbar {
            position: sticky;
            top: 0;
            z-index: 10;
            background: #fff;
            border-bottom: 1px solid #ccc;
            padding: 2mm 0;
            margin-bottom: 2mm;
            font-size: 14px;
        }

        .pos-toolbar .btns {
            display: flex;
            gap: 2mm;
        }

        .pos-toolbar button {
            flex: 1;
            padding: 10px 4px;
            font-size: 14px;
            font-weight: bold;
            border: 0;
            border-radius: 6px;
            color: #fff;
            background: #1e40af;
        }

        .pos-toolbar button.send {
            background: #15803d;
        }

        .pos-toolbar button:disabled {
            opacity: .6;
        }

        .pos-toolbar label {
            display: block;
            margin-top: 2mm;
            font-size: 12px;
        }

        .pos-toolbar .status {
            margin-top: 2mm;
            font-weight: bold;
            min-height: 1em;
        }

        .pos-toolbar .status.ok { color: #15803d; }
        .pos-toolbar .status.err { color: #b91c1c; }

        @media print {
            .pos-toolbar { display: none !important; }
        }
    </style>
</head>
<body>

    @unless($station)
        <div class="pos-toolbar">
            <div class="btns">
                <button type="button" id="btnPrintHere">🖨 Print here</button>
                <button type="button" id="btnSendPos" class="send">📲 Send to Mini POS</button>
            </div>
            <label>
                <input type="checkbox" id="rememberTarget">
                Always send to Mini POS from this device
            </label>
            <div class="status" id="posStatus"></div>
        </div>
    @endunless

    <div class="receipt">
        <p class="title">
        <img src="{{ asset('images/logo-2888.png') }}" style="max-width: 100px; height: auto;" >
        </p>
       <!-- Receipt Details with Left & Right Alignment -->
       <div class="details">
            <span>Receipt No: <strong>{{ $receipt_no }}</strong></span>
            <span>Receipt By: <strong>{{ $receipt_by }}</strong></span>
        </div>
        <div class="details">
            <span>Receipt Date: <strong>{{ $receipt_date }}</strong></span>
        </div>


        <table>
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Company</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bets as $bet)
                <tr>
                    <td style="letter-spacing: 1px; font-size:15px;">{{ $bet['number'] }}</td>
                    <td style="letter-spacing: 1px; font-size:15px;">{{ $bet['company'] }}</td>
                    <td style="letter-spacing: 1px; font-size:15px;">{{ $bet['amount'] }}</td>
                </tr>
                @endforeach
                <!-- Total Amount row -->
                <tr class="total-row">
                    <td colspan="2">Total Amount</td>
                    <td>{{ number_format($total_amount, 2) }} ({{ $currency }})</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2">Due Amount</td>
                    <td>{{ number_format($due_amount, 2) }} ({{ $currency }})</td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="footer_bold">NOTE: VALIDITY FOR {{ config('pos.validity_days') }} DAYS</p>
    <p class="footer_bold">{{ $expire_date }}</p>
    <p class="footer1">Quý khách vui lòng kiểm tra lại số ghi trên phiếu xin cảm ơn !</p>
    <p class="footer">Thank you for betting with us!</p>

<script>
    (function () {
        var isReprint = @json((bool) $reprint);
        var isStation = @json((bool) $station);
        var TARGET_KEY = 'pos_print_target';

        function getTarget() {
            try { return localStorage.getItem(TARGET_KEY); } catch (e) { return null; }
        }

        function setTarget(value) {
            try {
                value ? localStorage.setItem(TARGET_KEY, value) : localStorage.removeItem(TARGET_KEY);
            } catch (e) {}
        }

        function showStatus(text, cls) {
            var el = document.getElementById('posStatus');
            if (el) {
                el.textContent = text;
                el.className = 'status ' + (cls || '');
            }
        }

        function done() {
            if (isStation) {
                // Mini POS: go back and wait for the next receipt
                window.location.href = @json(route('pos.station'));
            } else if (isReprint) {
                // Old receipt opened from the receipt list in a new tab/window
                window.close();
                if (!window.closed) {
                    history.length > 1 ? history.back() : (window.location.href = @json($back_url));
                }
            } else {
                window.location.href = @json($back_url);
            }
        }

        function handlePrint() {
            // Set before print(): on Android POS Chrome, print() may return immediately
            window.onafterprint = done;
            window.print();
        }

        function sendToPos() {
            var btn = document.getElementById('btnSendPos');
            if (btn) btn.disabled = true;
            showStatus('Sending to Mini POS...');

            fetch(@json(route('pos.print-jobs.store')), {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                },
                body: JSON.stringify({
                    receipt_no: @json($receipt_no),
                    currency: @json($currency),
                    reprint: isReprint,
                }),
            })
                .then(function (res) {
                    return res.json().catch(function () { return {}; }).then(function (data) {
                        if (!res.ok || !data.success) throw new Error(data.message || 'Send failed');
                    });
                })
                .then(function () {
                    showStatus('✓ Sent to Mini POS', 'ok');
                    setTimeout(done, 1200);
                })
                .catch(function (err) {
                    showStatus('✗ ' + err.message + ' — try again or Print here', 'err');
                    if (btn) btn.disabled = false;
                });
        }

        function start() {
            if (isStation) {
                // Mini POS always prints on its own built-in printer
                setTimeout(handlePrint, 300);
                return;
            }

            var remember = document.getElementById('rememberTarget');
            remember.checked = getTarget() === 'station';
            remember.addEventListener('change', function () {
                setTarget(remember.checked ? 'station' : null);
            });

            document.getElementById('btnPrintHere').addEventListener('click', handlePrint);
            document.getElementById('btnSendPos').addEventListener('click', sendToPos);

            if (getTarget() === 'station') {
                sendToPos();
            } else if (@json((bool) config('pos.auto_print'))) {
                setTimeout(handlePrint, 300);
            }
        }

        // Wait until the logo has loaded so it is not missing on the paper
        if (document.readyState === 'complete') {
            start();
        } else {
            window.addEventListener('load', start);
        }
    })();
</script>
</body>
</html>
