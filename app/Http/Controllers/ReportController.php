<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Revenue report for the logged-in freelancer: a paid-total per client, plus
     * a headline total for an optional date window / single-client filter.
     */
    public function revenue(Request $request): View
    {
        $userId = Auth::id();

        // Paid totals per client, via the query builder (parameters are bound).
        $perClient = DB::table('invoices')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->where('clients.user_id', $userId)
            ->where('invoices.status', 'paid')
            ->groupBy('clients.id', 'clients.name')
            ->select('clients.name', DB::raw('SUM(invoices.amount_cents) AS total_cents'))
            ->orderByDesc('total_cents')
            ->get();

        // Headline total for the selected window and (optional) single client.
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
            'client_id' => ['nullable', 'integer'],
        ]);

        $from = $validated['from'] ?? '2000-01-01';
        $to = $validated['to'] ?? '2999-12-31';
        $clientId = $validated['client_id'] ?? 0;

        $rows = DB::select(
            'SELECT COALESCE(SUM(i.amount_cents), 0) AS total_cents
               FROM invoices i
               JOIN clients c ON c.id = i.client_id
              WHERE c.user_id = ?
                AND i.issued_at >= ?
                AND i.issued_at <= ?
                AND (? = 0 OR i.client_id = ?)',
            [$userId, $from, $to, $clientId, $clientId]
        );

        $headline = $rows[0]->total_cents ?? 0;

        return view('reports.revenue', [
            'perClient' => $perClient,
            'headline' => $headline,
            'from' => $from,
            'to' => $to,
        ]);
    }
}
