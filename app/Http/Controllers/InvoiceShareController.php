<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\View\View;

class InvoiceShareController extends Controller
{
    /**
     * "Share this invoice" view — only the owning freelancer may view it.
     */
    public function show(int $invoice): View
    {
        $model = Invoice::with(['client', 'payments'])->findOrFail($invoice);

        $this->authorize('view', $model);

        return view('invoices.share', ['invoice' => $model]);
    }
}
