<?php

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

require_once __DIR__.'/../../GradingHelpers.php';

uses(Tests\TestCase::class, RefreshDatabase::class);

test('4.1 client import exists and mass assignment is closed', function () {
    expect(Route::has('clients.import.store'))->toBeTrue('No clients.import.store route — the import feature was not applied.');

    $user = User::factory()->create();

    $this->actingAs($user)->post('/clients/import', [
        'csv' => "Grade Alice,alice@grading.test\nGrade Bob,bob@grading.test",
        'credit_limit' => 99999,
    ]);

    $created = Client::query()->whereIn('email', ['alice@grading.test', 'bob@grading.test'])->get();
    expect($created)->toHaveCount(2, 'CSV rows were not imported.');
    expect($created->every(fn (Client $c) => $c->user_id === $user->id))->toBeTrue('Imported clients not owned by the importing user.');

    expect(Client::query()->where('credit_limit', 99999)->count())->toBe(0, 'A smuggled credit_limit request field landed in the database (mass assignment).');
    expect($created->every(fn (Client $c) => $c->credit_limit === null))->toBeTrue('Imported rows picked up a credit_limit they should not have.');
});

test('4.2 invoice share exists and idor is closed', function () {
    $owner = User::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => Client::factory()->create(['user_id' => $owner->id])->id,
        'number' => 'INV-GRADE-4242',
    ]);
    $stranger = User::factory()->create();

    $blocked = $this->actingAs($stranger)->get("/invoices/{$invoice->id}/share");
    expect(in_array($blocked->getStatusCode(), [403, 404], true))->toBeTrue('A stranger can view another user\'s shared invoice (got '.$blocked->getStatusCode().').');

    $this->actingAs($owner)->get("/invoices/{$invoice->id}/share")
        ->assertOk()
        ->assertSee('INV-GRADE-4242');
});

test('4.3 revenue report exists and sql injection is closed', function () {
    $user = User::factory()->create();
    Invoice::factory()->paid()->create([
        'client_id' => Client::factory()->create(['user_id' => $user->id, 'name' => 'Grade Own Client'])->id,
        'issued_at' => '2024-06-01',
    ]);
    $other = User::factory()->create();
    Invoice::factory()->paid()->create([
        'client_id' => Client::factory()->create(['user_id' => $other->id, 'name' => 'GRADE-OTHER-MARKER'])->id,
        'issued_at' => '2024-06-01',
    ]);

    $this->actingAs($user)->get('/reports/revenue')
        ->assertOk()
        ->assertSee('Grade Own Client')
        ->assertDontSee('GRADE-OTHER-MARKER');

    $hostile = $this->actingAs($user)->get('/reports/revenue?'.http_build_query([
        'client_id' => '0 OR 1=1',
        'from' => "2020-01-01' OR '1'='1",
        'to' => "2999-12-31' OR '1'='1",
    ]));
    expect(in_array($hostile->getStatusCode(), [200, 302, 422], true))
        ->toBeTrue('Hostile report params blew up (got '.$hostile->getStatusCode().') — interpolated SQL.');
    expect(str_contains($hostile->getContent(), 'GRADE-OTHER-MARKER'))
        ->toBeFalse('Hostile report params leaked another user\'s data.');
});

test('4.4 learner regression tests added and suite green', function () {
    [$exit, $diff] = grading_process(['git', 'diff', grading_base_sha(), 'HEAD', '--', 'tests/']);
    expect($exit)->toBe(0);
    $added = preg_match_all('/^\+\s*(it|test)\(/m', $diff);
    expect($added)->toBeGreaterThanOrEqual(3, 'Fewer than 3 new test blocks under tests/ since the starter (found '.$added.').');

    [$suiteExit, $out] = grading_process([PHP_BINARY, 'artisan', 'test', '--colors=never']);
    expect($suiteExit)->toBe(0, "Learner test suite is not green:\n".$out);
});

test('4.6 review material intact', function () {
    foreach ([1, 2, 3] as $n) {
        expect(glob(base_path("review/diff-{$n}-*.patch")))->toHaveCount(1, "review/diff-{$n}-*.patch is missing.");
        expect(is_file(base_path("review/prompt-{$n}.md")))->toBeTrue("review/prompt-{$n}.md is missing.");
    }
});
