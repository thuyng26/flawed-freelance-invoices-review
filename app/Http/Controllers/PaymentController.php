<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Record a payment against one of the logged-in freelancer's invoices.
     * The invoice must belong to the authenticated user, or this 404s.
     */
    public function store(Request $request, int $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'amount_cents' => ['required', 'integer', 'min:1'],
            'paid_at' => ['nullable', 'date'],
        ]);

        $model = Invoice::query()
            ->whereHas('client', fn ($query) => $query->where('user_id', $request->user()->id))
            ->findOrFail($invoice);

        $model->payments()->create([
            'amount_cents' => $validated['amount_cents'],
            'paid_at' => $validated['paid_at'] ?? now(),
        ]);

        return redirect()->route('invoices.show', $model->id);
    }
}
