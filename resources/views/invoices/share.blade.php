<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $invoice->number }} — shared invoice</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; color: #1a2332; }
        h1 { font-size: 1.4rem; }
        .status { font-size: 0.75rem; text-transform: uppercase; color: #b8542c; }
        .meta { color: #8a93a2; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        td, th { text-align: left; padding: 0.4rem 0; border-bottom: 1px solid #eef1f6; font-size: 0.9rem; }
    </style>
</head>
<body>
    <h1>{{ $invoice->number }} <span class="status">{{ $invoice->status }}</span></h1>
    <p class="meta">
        Billed to {{ $invoice->client->name }} ·
        issued {{ $invoice->issued_at->toFormattedDateString() }}
    </p>

    <p><strong>Amount:</strong> ${{ number_format($invoice->amount_cents / 100, 2) }}</p>

    <h2>Payments</h2>
    <table>
        <thead>
            <tr><th>Paid at</th><th>Amount</th></tr>
        </thead>
        <tbody>
            @forelse ($invoice->payments as $payment)
                <tr>
                    <td>{{ $payment->paid_at?->toFormattedDateString() ?? '—' }}</td>
                    <td>${{ number_format($payment->amount_cents / 100, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="meta">No payments recorded.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
