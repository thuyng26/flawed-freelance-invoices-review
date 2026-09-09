<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'amount_cents' => $this->faker->numberBetween(5000, 500000),
            'paid_at' => $this->faker->dateTimeBetween('-60 days', 'now')->format('Y-m-d H:i:s'),
        ];
    }
}
