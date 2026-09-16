<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;

it('scopes the revenue report to the logged-in freelancer own clients', function () {
    $user = User::factory()->create();
    Invoice::factory()->paid()->create([
        'client_id' => Client::factory()->create(['user_id' => $user->id, 'name' => 'Own Client'])->id,
        'issued_at' => '2024-06-01',
    ]);

    $other = User::factory()->create();
    Invoice::factory()->paid()->create([
        'client_id' => Client::factory()->create(['user_id' => $other->id, 'name' => 'OTHER-MARKER'])->id,
        'issued_at' => '2024-06-01',
    ]);

    $this->actingAs($user)->get(route('reports.revenue'))
        ->assertOk()
        ->assertSee('Own Client')
        ->assertDontSee('OTHER-MARKER');
});

it('does not let sql injected filter params leak another freelancer revenue', function () {
    $user = User::factory()->create();
    Invoice::factory()->paid()->create([
        'client_id' => Client::factory()->create(['user_id' => $user->id])->id,
        'issued_at' => '2024-06-01',
    ]);

    $other = User::factory()->create();
    Invoice::factory()->paid()->create([
        'client_id' => Client::factory()->create(['user_id' => $other->id, 'name' => 'OTHER-MARKER'])->id,
        'issued_at' => '2024-06-01',
    ]);

    $response = $this->actingAs($user)->get('/reports/revenue?'.http_build_query([
        'client_id' => '0 OR 1=1',
        'from' => "2020-01-01' OR '1'='1",
        'to' => "2999-12-31' OR '1'='1",
    ]));

    expect(in_array($response->getStatusCode(), [200, 302, 422], true))->toBeTrue();
    $response->assertDontSee('OTHER-MARKER');
});
