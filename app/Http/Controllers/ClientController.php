<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * Create a client for the logged-in freelancer.
     *
     * Only name and email are accepted from the request; user_id comes from the
     * authenticated user, and credit_limit is not settable from form input.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $request->user()->clients()->create($validated);

        return redirect()->route('dashboard');
    }
}
