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
            font-size: 12px;
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
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            font-size: 12px;
            padding: 5px;
            text-align: center;
            border: 1px solid black; /* Added border */
        }

        .total-row {
            font-weight: bold;
        }

        .footer {
            font-size: 10px;
            margin-top: 10px;
        }
        .footer_bold{
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
        }
    </style>
</head>
<body onload="handlePrint()">

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
                    <td style="letter-spacing: 1px; font-weight:600">{{ $bet['number'] }}</td>
                    <td style="letter-spacing: 1px; font-weight:600">{{ $bet['company'] }}</td>
                    <td style="letter-spacing: 1px; font-weight:600">{{ $bet['amount'] }}</td>
                </tr>
                @endforeach
                <!-- Total Amount row -->
                <tr class="total-row">
                    <td colspan="2">Total Amount</td>
                    <td>{{ number_format($total_amount, 2) }} (VND)</td>
                </tr>
                <tr class="total-row">
                    <td colspan="2">Due Amount</td>
                    <td>{{ number_format($due_amount, 2) }} (VND)</td>
                </tr>
            </tbody>
        </table>
    </div>

    <p class="footer_bold">NOTE: VALIDITY FOR 3 DAYS</p>
    <p class="footer_bold">{{ $expire_date }}</p>
    <p class="footer">Thank you for betting with us!</p>

</body>
<script>
    function handlePrint() {
        window.print();
        window.onafterprint = function () {
            window.location.href = '{{ route('bet.input') }}';
        };
    }
</script>
</html>
