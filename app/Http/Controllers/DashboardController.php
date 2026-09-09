<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * The logged-in freelancer's own clients and invoices. Everything is
     * scoped to the authenticated user — no cross-tenant data leaks here.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $clients = $user->clients()
            ->orderBy('name')
            ->get();

        $invoices = Invoice::query()
            ->whereHas('client', fn ($query) => $query->where('user_id', $user->id))
            ->with('client')
            ->orderByDesc('issued_at')
            ->get();

        return view('dashboard', [
            'user' => $user,
            'clients' => $clients,
            'invoices' => $invoices,
        ]);
    }
}
