<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClientImportController extends Controller
{
    public function create(): View
    {
        return view('clients.import');
    }

    /**
     * Import clients from a pasted CSV (one "name,email" per line).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'csv' => ['required', 'string'],
        ]);

        $lines = preg_split('/\r\n|\r|\n/', trim($validated['csv']));

        $imported = 0;
        foreach ($lines as $line) {
            if ($line === '') {
                continue;
            }

            $cols = str_getcsv($line);

            $row = validator([
                'name' => $cols[0] ?? null,
                'email' => $cols[1] ?? null,
            ], [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255'],
            ])->validate();

            Client::create([
                'user_id' => Auth::id(),
                'name'    => $row['name'],
                'email'   => $row['email'],
            ]);

            $imported = $imported + 1;
        }

        return redirect()
            ->route('dashboard')
            ->with('status', $imported.' clients imported');
    }
}
