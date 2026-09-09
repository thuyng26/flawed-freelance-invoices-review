<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — Flawed Freelance Invoices</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 420px; margin: 4rem auto; padding: 0 1rem; color: #1a2332; }
        h1 { font-size: 1.4rem; }
        label { display: block; margin: 0.75rem 0 0.25rem; font-size: 0.9rem; color: #5a6472; }
        input { width: 100%; padding: 0.5rem; border: 1px solid #cbd2dd; border-radius: 4px; box-sizing: border-box; }
        button { margin-top: 1rem; padding: 0.5rem 1rem; border: none; border-radius: 4px; background: #2c4a77; color: #fff; cursor: pointer; }
        .error { color: #b8542c; font-size: 0.85rem; margin-top: 0.5rem; }
        .hint { color: #8a93a2; font-size: 0.8rem; margin-top: 1.5rem; }
    </style>
</head>
<body>
    <h1>Freelance Invoices</h1>
    <p>Sign in to see your clients and invoices.</p>

    @if ($errors->any())
        <p class="error">{{ $errors->first() }}</p>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <label for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>

        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>

        <button type="submit">Sign in</button>
    </form>

    <p class="hint">Seed logins: alice@example.com / bob@example.com — password is <code>password</code>.</p>
</body>
</html>
