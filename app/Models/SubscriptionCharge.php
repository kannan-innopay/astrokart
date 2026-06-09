<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionCharge extends Model
{
    protected $fillable = [
        'subscription_id',
        'user_id',
        'amount',
        'wallet_transaction_id',
        'status',
        'charged_for_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'charged_for_date' => 'date',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
