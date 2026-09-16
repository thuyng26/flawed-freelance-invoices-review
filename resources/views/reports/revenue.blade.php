<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Revenue report — Flawed Freelance Invoices</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; color: #1a2332; }
        h1 { font-size: 1.4rem; }
        .headline { font-size: 1.8rem; font-weight: 600; }
        .meta { color: #8a93a2; font-size: 0.85rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        td, th { text-align: left; padding: 0.4rem 0; border-bottom: 1px solid #eef1f6; font-size: 0.9rem; }
    </style>
</head>
<body>
    <p><a href="{{ route('dashboard') }}">&larr; Dashboard</a></p>
    <h1>Revenue report</h1>

    <p class="meta">Window: {{ $from }} to {{ $to }}</p>
    <p class="headline">${{ number_format($headline / 100, 2) }}</p>

    <h2>Paid totals per client</h2>
    <table>
        <thead>
            <tr><th>Client</th><th>Total</th></tr>
        </thead>
        <tbody>
            @forelse ($perClient as $row)
                <tr>
                    <td>{{ $row->name }}</td>
                    <td>${{ number_format($row->total_cents / 100, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="meta">No paid invoices yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
