<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Show one invoice — scoped to the logged-in freelancer. Invoices that
     * belong to another user's clients are simply not found (404).
     */
    public function show(Request $request, int $invoice): View
    {
        $model = $this->ownedInvoices($request)
            ->with(['client', 'payments'])
            ->findOrFail($invoice);

        return view('invoices.show', ['invoice' => $model]);
    }

    /**
     * Create an invoice for one of the logged-in freelancer's own clients.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'client_id' => ['required', 'integer'],
            'number' => ['required', 'string', 'max:64'],
            'amount_cents' => ['required', 'integer', 'min:1'],
            'status' => ['required', 'in:draft,sent,paid'],
            'issued_at' => ['required', 'date'],
        ]);

        // The client must belong to the authenticated user, or this 404s.
        $client = $request->user()->clients()->findOrFail($validated['client_id']);

        $client->invoices()->create($validated);

        return redirect()->route('dashboard');
    }

    /**
     * Query builder limited to invoices owned by the authenticated user.
     */
    private function ownedInvoices(Request $request)
    {
        return Invoice::query()
            ->whereHas('client', fn ($query) => $query->where('user_id', $request->user()->id));
    }
}
