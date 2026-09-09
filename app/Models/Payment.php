<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A payment recorded against an invoice.
 *
 * @property int $invoice_id
 * @property int $amount_cents
 * @property \Illuminate\Support\Carbon $paid_at
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'amount_cents', 'paid_at'];

    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
