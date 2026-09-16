<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

it('lets the owning freelancer view their shared invoice', function () {
    $owner = User::factory()->create();
    $client = Client::factory()->for($owner)->create();
    $invoice = Invoice::factory()->for($client)->create(['number' => 'INV-SHARE-1']);

    $this->actingAs($owner)
        ->get(route('invoices.share', $invoice->id))
        ->assertOk()
        ->assertSee('INV-SHARE-1');
});

it('blocks a stranger from viewing another freelancer shared invoice', function () {
    $owner = User::factory()->create();
    $client = Client::factory()->for($owner)->create();
    $invoice = Invoice::factory()->for($client)->create(['number' => 'INV-SHARE-2']);

    $stranger = User::factory()->create();

    $response = $this->actingAs($stranger)->get(route('invoices.share', $invoice->id));

    expect(in_array($response->getStatusCode(), [403, 404], true))->toBeTrue();
    $response->assertDontSee('INV-SHARE-2');
});
