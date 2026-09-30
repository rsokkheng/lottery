<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Station</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 16px;
        }

        .dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #22c55e;
            margin: 0 auto 16px;
            animation: pulse 1.6s infinite;
        }

        .dot.off { background: #ef4444; animation: none; }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, .7); }
            70% { box-shadow: 0 0 0 18px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        h1 { font-size: 22px; margin: 0 0 8px; }
        p { margin: 4px 0; color: #cbd5e1; }
        .user { font-weight: bold; color: #fff; }
        #status { margin-top: 16px; font-size: 16px; font-weight: bold; }

        .back {
            margin-top: 28px;
            color: #93c5fd;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="dot" id="dot"></div>
    <h1>🖨 Mini POS Station</h1>
    <p>Logged in as <span class="user">{{ auth()->user()->name }}</span></p>
    <p>Receipts sent from phones on this account will print here.</p>
    <p>Keep this page open and the screen on.</p>
    <div id="status">Waiting for receipts...</div>

    <a class="back" href="{{ url('/') }}">← Back to system</a>

<script>
    (function () {
        var POLL_MS = {{ max(1, config('pos.station_poll_seconds')) * 1000 }};
        var statusEl = document.getElementById('status');
        var dot = document.getElementById('dot');
        var busy = false;

        // Keep the POS screen awake so polling does not stop
        function keepAwake() {
            if ('wakeLock' in navigator) {
                navigator.wakeLock.request('screen').catch(function () {});
            }
        }
        keepAwake();
        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') keepAwake();
        });

        function poll() {
            if (busy) return;
            busy = true;

            fetch(@json(route('pos.station.next')), {
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json' },
                cache: 'no-store',
            })
                .then(function (res) {
                    if (res.status === 401 || res.status === 419) {
                        window.location.reload(); // session expired -> login page
                        throw new Error('Session expired');
                    }
                    if (!res.ok) throw new Error('Server error ' + res.status);
                    return res.json();
                })
                .then(function (data) {
                    dot.classList.remove('off');
                    if (data.job) {
                        statusEl.textContent = 'Printing receipt ' + data.job.receipt_no + '...';
                        // The receipt page prints, then returns to this station
                        window.location.href = data.job.url;
                        return;
                    }
                    statusEl.textContent = 'Waiting for receipts...';
                    busy = false;
                })
                .catch(function (err) {
                    dot.classList.add('off');
                    statusEl.textContent = 'Offline: ' + err.message + ' (retrying)';
                    busy = false;
                });
        }

        poll();
        setInterval(poll, POLL_MS);
    })();
</script>
</body>
</html>
