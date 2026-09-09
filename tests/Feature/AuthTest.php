<?php

use App\Models\User;

it('redirects guests to the login screen', function () {
    $this->get('/')->assertRedirect('/login');
});

it('renders the login screen', function () {
    $this->get('/login')
        ->assertOk()
        ->assertSee('Sign in');
});

it('logs in with valid credentials', function () {
    $user = User::factory()->create(['email' => 'user@example.com']);

    $response = $this->post('/login', [
        'email' => 'user@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

it('rejects an invalid password', function () {
    User::factory()->create(['email' => 'user@example.com']);

    $this->post('/login', [
        'email' => 'user@example.com',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects an unknown email', function () {
    $this->post('/login', [
        'email' => 'nobody@example.com',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('logs the user out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
