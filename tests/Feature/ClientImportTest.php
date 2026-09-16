<?php

use App\Models\Client;
use App\Models\User;

it('imports clients from csv for the logged-in freelancer', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('clients.import.store'), [
            'csv' => "Acme Co,billing@acme.test\nBeta Inc,ap@beta.test",
        ])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('clients', ['email' => 'billing@acme.test', 'user_id' => $user->id]);
    $this->assertDatabaseHas('clients', ['email' => 'ap@beta.test', 'user_id' => $user->id]);
});

it('never mass-assigns a smuggled credit_limit through the import shared-defaults field', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('clients.import.store'), [
        'csv' => 'Sneaky Co,sneaky@client.test',
        'credit_limit' => 999999999,
    ]);

    $client = Client::query()->where('email', 'sneaky@client.test')->firstOrFail();

    expect($client->credit_limit)->toBeNull();
});
