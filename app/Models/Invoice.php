<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * An invoice issued to a client.
 *
 * Amounts are stored in integer cents to avoid float rounding. Status is one of
 * draft / sent / paid.
 *
 * @property int $client_id
 * @property string $number
 * @property int $amount_cents
 * @property string $status
 * @property \Illuminate\Support\Carbon $issued_at
 */
class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'number', 'amount_cents', 'status', 'issued_at'];

    protected function casts(): array
    {
        return [
            'amount_cents' => 'integer',
            'issued_at' => 'date',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
