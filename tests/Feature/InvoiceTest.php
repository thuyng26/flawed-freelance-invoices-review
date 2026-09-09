<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

it('lets a freelancer view their own invoice', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    $invoice = Invoice::factory()->for($client)->create(['number' => 'INV-VIEW-1']);

    $this->actingAs($user)
        ->get(route('invoices.show', $invoice->id))
        ->assertOk()
        ->assertSee('INV-VIEW-1');
});

it('returns 404 when viewing another freelancer their invoice', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $bobClient = Client::factory()->for($bob)->create();
    $bobInvoice = Invoice::factory()->for($bobClient)->create();

    $this->actingAs($alice)
        ->get(route('invoices.show', $bobInvoice->id))
        ->assertNotFound();
});

it('validates invoice creation input', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('invoices.store'), [])
        ->assertSessionHasErrors(['client_id', 'number', 'amount_cents', 'status', 'issued_at']);
});

it('creates an invoice for the freelancer own client', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('invoices.store'), [
            'client_id' => $client->id,
            'number' => 'INV-NEW-42',
            'amount_cents' => 25000,
            'status' => 'sent',
            'issued_at' => '2026-02-01',
        ])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('invoices', [
        'number' => 'INV-NEW-42',
        'client_id' => $client->id,
    ]);
});

it('refuses to create an invoice for another freelancer their client', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $bobClient = Client::factory()->for($bob)->create();

    $this->actingAs($alice)
        ->post(route('invoices.store'), [
            'client_id' => $bobClient->id,
            'number' => 'INV-HIJACK-1',
            'amount_cents' => 25000,
            'status' => 'sent',
            'issued_at' => '2026-02-01',
        ])
        ->assertNotFound();

    $this->assertDatabaseMissing('invoices', ['number' => 'INV-HIJACK-1']);
});
