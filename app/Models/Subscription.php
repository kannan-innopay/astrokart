<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasUuid;

    protected $fillable = [
        'user_id',
        'plan',
        'amount',
        'status',
        'starts_at',
        'expires_at',
        'billing_interval',
        'next_billing_at',
        'last_billed_at',
        'auto_renew',
        'cancelled_at',
        'past_due_at',
        'grace_ends_at',
        'payment_id',
    ];

    protected function casts(): array
    {
        return [
            'plan' => SubscriptionPlan::class,
            'status' => SubscriptionStatus::class,
            'amount' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'last_billed_at' => 'datetime',
            'auto_renew' => 'boolean',
            'cancelled_at' => 'datetime',
            'past_due_at' => 'datetime',
            'grace_ends_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function charges(): HasMany
    {
        return $this->hasMany(SubscriptionCharge::class);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     */
    public function scopeActive($query): void
    {
        $query->where('status', SubscriptionStatus::Active)
            ->where('expires_at', '>', now());
    }

    public function isActive(): bool
    {
        return $this->status === SubscriptionStatus::Active
            && $this->expires_at->isFuture();
    }

    public function daysRemaining(): int
    {
        return max(0, (int) now()->diffInDays($this->expires_at, false));
    }

    public function cancel(): void
    {
        $this->update([
            'status' => SubscriptionStatus::Cancelled,
            'cancelled_at' => now(),
            'auto_renew' => false,
        ]);
    }
}
