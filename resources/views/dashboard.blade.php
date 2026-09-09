<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard — Flawed Freelance Invoices</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 760px; margin: 2rem auto; padding: 0 1rem; color: #1a2332; }
        h1 { font-size: 1.5rem; }
        h2 { font-size: 1.1rem; margin-top: 2rem; border-bottom: 1px solid #e5e9f0; padding-bottom: 0.25rem; }
        .top { display: flex; justify-content: space-between; align-items: baseline; }
        ul { list-style: none; padding: 0; }
        li { padding: 0.5rem 0; border-bottom: 1px solid #eef1f6; display: flex; gap: 0.75rem; align-items: baseline; }
        a { color: #2c4a77; text-decoration: none; }
        .meta { margin-left: auto; font-size: 0.8rem; color: #8a93a2; white-space: nowrap; }
        .status { font-size: 0.7rem; text-transform: uppercase; color: #b8542c; }
        form.logout { display: inline; }
        button { border: none; background: none; cursor: pointer; color: #8a93a2; }
    </style>
</head>
<body>
    <div class="top">
        <h1>{{ $user->name }}</h1>
        <form class="logout" method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Sign out</button>
        </form>
    </div>

    <h2>Clients ({{ $clients->count() }})</h2>
    <ul>
        @foreach ($clients as $client)
            <li>
                <span>{{ $client->name }}</span>
                <span class="meta">{{ $client->email }}</span>
            </li>
        @endforeach
    </ul>

    <h2>Invoices ({{ $invoices->count() }})</h2>
    <ul>
        @foreach ($invoices as $invoice)
            <li>
                <a href="{{ route('invoices.show', $invoice->id) }}">{{ $invoice->number }}</a>
                <span class="status">{{ $invoice->status }}</span>
                <span class="meta">
                    {{ $invoice->client->name }} ·
                    ${{ number_format($invoice->amount_cents / 100, 2) }} ·
                    {{ $invoice->issued_at->toFormattedDateString() }}
                </span>
            </li>
        @endforeach
    </ul>
</body>
</html>
