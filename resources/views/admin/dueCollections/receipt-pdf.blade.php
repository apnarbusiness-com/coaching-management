<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Receipt - {{ $earningTransaction->receipt_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; padding: 30px; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #059669; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #059669; margin-bottom: 3px; }
        .header p { font-size: 11px; color: #64748b; }
        .receipt-title { text-align: center; margin-bottom: 20px; }
        .receipt-title h2 { font-size: 16px; border: 1px solid #059669; display: inline-block; padding: 5px 25px; color: #059669; letter-spacing: 1px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .info-box { width: 48%; }
        .info-box label { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-box p { font-size: 13px; font-weight: 600; color: #1e293b; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th { background: #059669; color: white; padding: 8px 10px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px; }
        table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
        table tr:last-child td { border-bottom: none; }
        .amount-in-words { margin-bottom: 15px; padding: 10px; background: #f0fdf4; border-radius: 5px; }
        .amount-in-words label { font-size: 10px; color: #059669; text-transform: uppercase; letter-spacing: 0.5px; }
        .amount-in-words p { font-size: 13px; font-weight: 600; color: #1e293b; margin-top: 2px; }
        .footer { margin-top: 30px; display: flex; justify-content: space-between; }
        .footer .signature { text-align: center; }
        .footer .signature p { margin-top: 35px; padding-top: 5px; border-top: 1px solid #cbd5e1; width: 180px; font-size: 11px; color: #64748b; }
        .total-row td { font-weight: 700; font-size: 14px; border-top: 2px solid #059669; padding-top: 10px; }
        .print-btn { text-align: center; margin: 20px 0; }
        .print-btn button { padding: 10px 30px; background: #059669; color: white; border: none; border-radius: 5px; font-size: 14px; cursor: pointer; }
        @media print {
            .print-btn { display: none; }
            body { padding: 15px; }
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .fw-bold { font-weight: 700; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ setting('institute_name') ?? config('app.name') }}</h1>
        <p>{{ setting('institute_address') ?? '' }} | {{ setting('institute_phone') ?? '' }}</p>
    </div>

    <div class="receipt-title">
        <h2>MONEY RECEIPT</h2>
    </div>

    <div class="info-row">
        <div class="info-box">
            <label>Receipt No</label>
            <p>{{ $earningTransaction->receipt_no }}</p>
        </div>
        <div class="info-box text-right">
            <label>Date</label>
            <p>{{ \Carbon\Carbon::parse($earningTransaction->payment_date)->format('d M, Y') }}</p>
        </div>
    </div>

    <div class="info-row">
        <div class="info-box">
            <label>Student Name</label>
            <p>{{ $earningTransaction->student->first_name ?? '' }} {{ $earningTransaction->student->last_name ?? '' }}</p>
        </div>
        <div class="info-box text-right">
            <label>ID No</label>
            <p>{{ $earningTransaction->student->id_no ?? 'N/A' }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:40px;">#</th>
                <th>Month</th>
                <th>Batch</th>
                <th class="text-right">Amount (BDT)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($earningTransaction->earnings as $index => $earning)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::createFromDate(null, $earning->earning_month, 1)->format('F') }} {{ $earning->earning_year }}</td>
                <td>{{ $earning->batch->batch_name ?? 'N/A' }}</td>
                <td class="text-right">{{ number_format($earning->amount, 2) }}</td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3" class="text-right fw-bold">Total</td>
                <td class="text-right fw-bold">{{ number_format($earningTransaction->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="amount-in-words">
        <label>Amount in Words</label>
        <p>Taka {{ number_to_words($earningTransaction->total_amount) }} only</p>
    </div>

    <div class="info-row">
        <div class="info-box">
            <label>Payment Method</label>
            <p>{{ $earningTransaction->payment_method }}</p>
        </div>
        <div class="info-box text-right">
            <label>Received By</label>
            <p>{{ $earningTransaction->createdBy->name ?? auth()->user()->name ?? '' }}</p>
        </div>
    </div>

    <div class="footer">
        <div class="signature">
            <p>Receiver's Signature</p>
        </div>
        <div class="signature">
            <p>Authorized Signature</p>
        </div>
    </div>

    <div class="print-btn">
        <button onclick="window.print()">Print Receipt</button>
    </div>

</body>
</html>
