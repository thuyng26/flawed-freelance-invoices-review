<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * A user may view an invoice only if it belongs to one of their own clients.
     */
    public function view(User $user, Invoice $invoice): bool
    {
        return $invoice->client->user_id === $user->id;
    }
}
