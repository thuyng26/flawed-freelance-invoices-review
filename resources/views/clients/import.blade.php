<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Import clients — Flawed Freelance Invoices</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 640px; margin: 2rem auto; padding: 0 1rem; color: #1a2332; }
        h1 { font-size: 1.4rem; }
        textarea { width: 100%; height: 10rem; padding: 0.5rem; border: 1px solid #cbd2dd; border-radius: 4px; box-sizing: border-box; font-family: ui-monospace, monospace; }
        button { margin-top: 1rem; padding: 0.5rem 1rem; border: none; border-radius: 4px; background: #2c4a77; color: #fff; cursor: pointer; }
        .meta { color: #8a93a2; font-size: 0.85rem; }
    </style>
</head>
<body>
    <p><a href="{{ route('dashboard') }}">&larr; Dashboard</a></p>
    <h1>Import clients</h1>
    <p class="meta">Paste one <code>name,email</code> per line.</p>

    <form method="POST" action="{{ route('clients.import.store') }}">
        @csrf
        <textarea name="csv" placeholder="Acme Co,billing@acme.test"></textarea>
        <button type="submit">Import</button>
    </form>
</body>
</html>
