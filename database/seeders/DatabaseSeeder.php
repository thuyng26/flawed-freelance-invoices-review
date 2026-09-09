<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Two freelancers, each with 3 clients and 6 invoices (12 total).
     * Every invoice marked "paid" gets a matching payment record.
     *
     * Seeded logins (password is "password" for both):
     *   alice@example.com
     *   bob@example.com
     */
    public function run(): void
    {
        $freelancers = [
            ['name' => 'Alice Ferraro', 'email' => 'alice@example.com'],
            ['name' => 'Bob Nguyen', 'email' => 'bob@example.com'],
        ];

        foreach ($freelancers as $attributes) {
            $user = User::factory()->create([
                'name' => $attributes['name'],
                'email' => $attributes['email'],
                'password' => Hash::make('password'),
            ]);

            $clients = Client::factory()
                ->count(3)
                ->for($user)
                ->create();

            foreach ($clients as $index => $client) {
                // 2 invoices per client => 6 per freelancer, 12 total.
                // The first client's invoices are forced paid so there is
                // always payment data to look at.
                $invoices = Invoice::factory()
                    ->count(2)
                    ->for($client)
                    ->state(fn () => $index === 0 ? ['status' => 'paid'] : [])
                    ->create();

                foreach ($invoices as $invoice) {
                    if ($invoice->status === 'paid') {
                        Payment::factory()->for($invoice)->create([
                            'amount_cents' => $invoice->amount_cents,
                        ]);
                    }
                }
            }
        }
    }
}
