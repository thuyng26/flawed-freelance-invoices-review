<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A client belonging to one freelancer (user).
 *
 * The clients table also carries a `credit_limit` column, but it is deliberately
 * NOT mass-assignable here: credit limits are set through an internal admin flow,
 * never from end-user form input.
 *
 * @property string $name
 * @property string $email
 * @property int $user_id
 * @property int|null $credit_limit
 */
class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
