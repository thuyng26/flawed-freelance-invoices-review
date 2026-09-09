<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

it('shows the freelancer their own clients', function () {
    $user = User::factory()->create();
    Client::factory()->for($user)->create(['name' => 'Acme Owned Co']);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('Acme Owned Co');
});

it('does not show another freelancer their clients', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    Client::factory()->for($bob)->create(['name' => 'Zenith Private Ltd']);

    $this->actingAs($alice)
        ->get('/')
        ->assertOk()
        ->assertDontSee('Zenith Private Ltd');
});

it('shows the freelancer their own invoices', function () {
    $user = User::factory()->create();
    $client = Client::factory()->for($user)->create();
    Invoice::factory()->for($client)->create(['number' => 'INV-OWNED-1']);

    $this->actingAs($user)
        ->get('/')
        ->assertOk()
        ->assertSee('INV-OWNED-1');
});

it('does not leak another freelancer their invoices', function () {
    $alice = User::factory()->create();
    $bob = User::factory()->create();
    $bobClient = Client::factory()->for($bob)->create();
    Invoice::factory()->for($bobClient)->create(['number' => 'INV-SECRET-9']);

    $this->actingAs($alice)
        ->get('/')
        ->assertOk()
        ->assertDontSee('INV-SECRET-9');
});
