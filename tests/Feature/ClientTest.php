<?php

use App\Models\User;

it('creates a client for the logged-in freelancer', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('clients.store'), [
            'name' => 'New Client Inc',
            'email' => 'billing@newclient.test',
        ])
        ->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('clients', [
        'name' => 'New Client Inc',
        'user_id' => $user->id,
    ]);
});

it('requires a name and email to create a client', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('clients.store'), [])
        ->assertSessionHasErrors(['name', 'email']);
});

it('never mass-assigns credit_limit from client input (baseline is secure)', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('clients.store'), [
        'name' => 'Sneaky Client',
        'email' => 'sneaky@client.test',
        'credit_limit' => 999999999,
    ])->assertRedirect(route('dashboard'));

    $this->assertDatabaseHas('clients', [
        'name' => 'Sneaky Client',
        'credit_limit' => null,
    ]);
});
