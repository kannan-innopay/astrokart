<?php

namespace App\Services;

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Enums\TransactionType;
use App\Models\Subscription;
use App\Models\SubscriptionCharge;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Subscribe a user to a plan by debiting their wallet.
     */
    public function subscribe(User $user, SubscriptionPlan $plan): Subscription
    {
        $price = $plan->price();
        $wallet = $user->wallet;

        if (! $wallet || ! $wallet->hasSufficientBalance($price)) {
            throw new \RuntimeException('Insufficient wallet balance. Please recharge your wallet first.');
        }

        // Check for existing active subscription
        if ($user->isPremium()) {
            throw new \RuntimeException('You already have an active subscription.');
        }

        return DB::transaction(function () use ($user, $plan, $price, $wallet) {
            $now = now();
            $expiresAt = $now->copy()->addDays($plan->durationDays());

            // Debit wallet
            $balanceBefore = $wallet->balance;
            $wallet->debit($price);

            // Create wallet transaction
            $walletTransaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::SubscriptionDebit,
                'amount' => $price,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => 'subscription',
                'status' => 'completed',
                'remarks' => $plan->label() . ' subscription',
            ]);

            // Create subscription
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan' => $plan,
                'amount' => $price,
                'status' => SubscriptionStatus::Active,
                'starts_at' => $now,
                'expires_at' => $expiresAt,
                'billing_interval' => $plan === SubscriptionPlan::Daily ? 'daily' : null,
                'next_billing_at' => $plan === SubscriptionPlan::Daily ? $expiresAt : null,
                'last_billed_at' => $now,
                'auto_renew' => $plan === SubscriptionPlan::Daily,
            ]);

            // Record charge
            SubscriptionCharge::create([
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'amount' => $price,
                'wallet_transaction_id' => $walletTransaction->id,
                'status' => 'completed',
                'charged_for_date' => $now->toDateString(),
            ]);

            return $subscription;
        });
    }

    /**
     * Renew a daily subscription by debiting ₹3 from wallet.
     */
    public function renewDaily(Subscription $subscription): bool
    {
        if ($subscription->plan !== SubscriptionPlan::Daily || ! $subscription->auto_renew) {
            return false;
        }

        $user = $subscription->user;
        $wallet = $user->wallet;
        $price = SubscriptionPlan::Daily->price();

        if (! $wallet || ! $wallet->hasSufficientBalance($price)) {
            $subscription->update([
                'status' => SubscriptionStatus::PastDue,
                'past_due_at' => now(),
            ]);

            return false;
        }

        return DB::transaction(function () use ($subscription, $user, $wallet, $price) {
            $balanceBefore = $wallet->balance;
            $wallet->debit($price);

            $walletTransaction = WalletTransaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::SubscriptionDebit,
                'amount' => $price,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => 'subscription',
                'reference_id' => $subscription->id,
                'status' => 'completed',
                'remarks' => 'Daily Premium Pass renewal',
            ]);

            $now = now();
            $subscription->update([
                'expires_at' => $now->copy()->addDay(),
                'next_billing_at' => $now->copy()->addDay(),
                'last_billed_at' => $now,
                'status' => SubscriptionStatus::Active,
                'past_due_at' => null,
            ]);

            SubscriptionCharge::create([
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
                'amount' => $price,
                'wallet_transaction_id' => $walletTransaction->id,
                'status' => 'completed',
                'charged_for_date' => $now->toDateString(),
            ]);

            return true;
        });
    }

    /**
     * Cancel a subscription. Access continues until expires_at.
     */
    public function cancel(Subscription $subscription): void
    {
        $subscription->cancel();
    }

    /**
     * Get active subscription for a user.
     */
    public function getActive(User $user): ?Subscription
    {
        return $user->activeSubscription;
    }
}
