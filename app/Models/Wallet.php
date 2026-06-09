<?php

namespace App\Models;

use App\Concerns\HasUuid;

use Database\Factories\WalletFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'balance', 'currency'])]
class Wallet extends Model
{
    /** @use HasFactory<WalletFactory> */
    use HasFactory, HasUuid;

    protected function casts(): array
    {
        return [
            'balance' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasSufficientBalance(int $amount): bool
    {
        return $this->balance >= $amount;
    }

    public function credit(int $amount): void
    {
        $this->increment('balance', $amount);
    }

    public function debit(int $amount): void
    {
        if (! $this->hasSufficientBalance($amount)) {
            throw new \RuntimeException('Insufficient wallet balance.');
        }

        $this->decrement('balance', $amount);
    }
}
