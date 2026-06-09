<?php

namespace App\Services;

use App\Enums\SubscriptionPlan;
use App\Enums\TransactionType;
use App\Models\MuhurthamRequest;
use App\Models\User;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MuhurthamService
{
    private const PRICE_PAISE = 500; // ₹5

    private const MONTHLY_FREE_QUOTA = 3;

    private const YEARLY_FREE_QUOTA = 50;

    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.horoscope.url');
    }

    public function search(User $user, string $purpose, Carbon $start, Carbon $end): MuhurthamRequest
    {
        $amountToCharge = $this->calculateCharge($user);

        if ($amountToCharge > 0) {
            $wallet = $user->wallet;
            if (! $wallet || ! $wallet->hasSufficientBalance($amountToCharge)) {
                throw new \RuntimeException('Insufficient wallet balance. Please recharge your wallet.');
            }
        }

        return DB::transaction(function () use ($user, $purpose, $start, $end, $amountToCharge) {
            $walletTransactionId = null;

            if ($amountToCharge > 0) {
                $wallet = $user->wallet;
                $balanceBefore = $wallet->balance;
                $wallet->debit($amountToCharge);

                $wt = WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => TransactionType::MuhurthamRequestDebit,
                    'amount' => $amountToCharge,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'reference_type' => 'muhurtham_request',
                    'status' => 'completed',
                    'remarks' => "Muhurtham search: {$purpose}",
                ]);
                $walletTransactionId = $wt->id;
            }

            $request = MuhurthamRequest::create([
                'user_id' => $user->id,
                'purpose' => $purpose,
                'date_range_start' => $start,
                'date_range_end' => $end,
                'status' => 'processing',
                'amount_charged' => $amountToCharge,
                'wallet_transaction_id' => $walletTransactionId,
            ]);

            // Call Python service
            try {
                $response = Http::timeout(120)
                    ->connectTimeout(5)
                    ->retry(2, 1000)
                    ->post("{$this->baseUrl}/api/muhurtham/search", [
                        'date_start' => $start->format('Y-m-d'),
                        'date_end' => $end->format('Y-m-d'),
                        'purpose' => $purpose,
                        'user_chart' => $user->birth_chart,
                    ]);

                if ($response->successful()) {
                    $request->update([
                        'status' => 'completed',
                        'result_json' => $response->json(),
                        'generated_at' => now(),
                    ]);
                } else {
                    $this->handleFailure($request, $user, $amountToCharge, 'Service returned error: ' . $response->status());
                }
            } catch (\Throwable $e) {
                Log::error('Muhurtham service error', ['error' => $e->getMessage()]);
                $this->handleFailure($request, $user, $amountToCharge, $e->getMessage());
            }

            return $request->refresh();
        });
    }

    public function getUserRequests(User $user): Collection
    {
        return MuhurthamRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();
    }

    private function calculateCharge(User $user): int
    {
        $subscription = $user->activeSubscription;

        if (! $subscription) {
            return self::PRICE_PAISE;
        }

        $quota = match ($subscription->plan) {
            SubscriptionPlan::Monthly => self::MONTHLY_FREE_QUOTA,
            SubscriptionPlan::Yearly => self::YEARLY_FREE_QUOTA,
            default => 0,
        };

        if ($quota <= 0) {
            return self::PRICE_PAISE;
        }

        $usedThisPeriod = MuhurthamRequest::where('user_id', $user->id)
            ->where('amount_charged', 0)
            ->where('created_at', '>=', $subscription->starts_at)
            ->count();

        return $usedThisPeriod >= $quota ? self::PRICE_PAISE : 0;
    }

    private function handleFailure(MuhurthamRequest $request, User $user, int $amountCharged, string $reason): void
    {
        $request->update([
            'status' => 'failed',
            'failure_reason' => $reason,
        ]);

        // Auto-refund on failure
        if ($amountCharged > 0 && $user->wallet) {
            $wallet = $user->wallet;
            $balanceBefore = $wallet->balance;
            $wallet->credit($amountCharged);

            WalletTransaction::create([
                'user_id' => $user->id,
                'type' => TransactionType::Refund,
                'amount' => $amountCharged,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => 'muhurtham_request',
                'reference_id' => $request->id,
                'status' => 'completed',
                'remarks' => 'Muhurtham search refund (service error)',
            ]);

            $request->update(['status' => 'refunded']);
        }
    }
}
