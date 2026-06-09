<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\AccountStatus;
use App\Enums\Gender;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable([
    'name',
    'email',
    'password',
    'mobile',
    'role',
    'gender',
    'date_of_birth',
    'time_of_birth',
    'place_of_birth',
    'preferred_language',
    'account_status',
    'birth_chart',
    'birth_latitude',
    'birth_longitude',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuid, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'mobile_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'gender' => Gender::class,
            'account_status' => AccountStatus::class,
            'date_of_birth' => 'date',
            'birth_chart' => 'array',
            'birth_latitude' => 'decimal:7',
            'birth_longitude' => 'decimal:7',
        ];
    }

    public function astrologerProfile(): HasOne
    {
        return $this->hasOne(Astrologer::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class)
            ->where('status', \App\Enums\SubscriptionStatus::Active)
            ->where('expires_at', '>', now())
            ->latest('starts_at');
    }

    public function isPremium(): bool
    {
        return once(fn () => $this->activeSubscription()->exists());
    }

    public function hasEntitlement(string $entitlement): bool
    {
        $subscription = once(fn () => $this->activeSubscription);

        if (! $subscription) {
            return false;
        }

        return PlanEntitlement::where('plan', $subscription->plan->value)
            ->where('entitlement', $entitlement)
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    public function isAstrologer(): bool
    {
        return $this->role === UserRole::Astrologer;
    }

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === UserRole::SuperAdmin;
    }

    public function isActive(): bool
    {
        return $this->account_status === AccountStatus::Active;
    }
}
