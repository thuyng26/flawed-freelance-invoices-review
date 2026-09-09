<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

it('records a payment against the freelancer own invoice', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()->for($client)->create();

    $this->actingAs($user)
        ->post(route('payments.store', $invoice->id), [
            'amount_cents' => 5000,
        ])
        ->assertRedirect(route('invoices.show', $invoice->id));

    $this->assertDatabaseHas('payments', [
        'invoice_id' => $invoice->id,
        'amount_cents' => 5000,
    ]);
});

it('returns 404 when paying another freelancer their invoice', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $bobClient = Client::factory()->for($bob)->create();
    $bobInvoice = Invoice::factory()->for($bobClient)->create();

    $this->actingAs($alice)
        ->post(route('payments.store', $bobInvoice->id), [
            'amount_cents' => 5000,
        ])
        ->assertNotFound();

    $this->assertDatabaseMissing('payments', ['invoice_id' => $bobInvoice->id]);
});

it('requires a positive payment amount', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()->for($client)->create();

    $this->actingAs($user)
        ->post(route('payments.store', $invoice->id), [
            'amount_cents' => 0,
        ])
        ->assertSessionHasErrors('amount_cents');
});
