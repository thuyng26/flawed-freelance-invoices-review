<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'number' => 'INV-'.$this->faker->unique()->numberBetween(1000, 9999),
            'amount_cents' => $this->faker->numberBetween(5000, 500000),
            'status' => $this->faker->randomElement(['draft', 'sent', 'paid']),
            'issued_at' => $this->faker->dateTimeBetween('-90 days', 'now')->format('Y-m-d'),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => ['status' => 'paid']);
    }
}
