<?php

namespace App\Services;

use App\Enums\TransactionType;
use App\Models\CompatibilityReport;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CompatibilityService
{
    private const PRICE_PAISE = 900; // ₹9

    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.horoscope.url');
    }

    public function match(User $user, array $partnerData): CompatibilityReport
    {
        // Get user's Moon data from birth chart
        $userMoonNak = null;
        $userMoonRashi = null;

        if ($user->birth_chart) {
            foreach ($user->birth_chart['grahas'] ?? [] as $g) {
                if ($g['name'] === 'Moon') {
                    $userMoonNak = $g['nakshatra']['index'] ?? null;
                    $userMoonRashi = $g['rashi']['index'] ?? null;
                    break;
                }
            }
        }

        if ($userMoonNak === null || $userMoonRashi === null) {
            throw new \RuntimeException('Your birth chart must have Moon position data. Please regenerate your chart.');
        }

        $isPremium = $user->isPremium();
        $amountToCharge = $isPremium ? 0 : self::PRICE_PAISE;

        if ($amountToCharge > 0) {
            $wallet = $user->wallet;
            if (! $wallet || ! $wallet->hasSufficientBalance($amountToCharge)) {
                throw new \RuntimeException('Insufficient wallet balance. Please recharge your wallet.');
            }
        }

        return DB::transaction(function () use ($user, $partnerData, $userMoonNak, $userMoonRashi, $amountToCharge) {
            $walletTransactionId = null;

            if ($amountToCharge > 0) {
                $wallet = $user->wallet;
                $balanceBefore = $wallet->balance;
                $wallet->debit($amountToCharge);

                $wt = WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => TransactionType::CompatibilityRequestDebit,
                    'amount' => $amountToCharge,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'reference_type' => 'compatibility_report',
                    'status' => 'completed',
                    'remarks' => 'Compatibility matching',
                ]);
                $walletTransactionId = $wt->id;
            }

            // Call Python service
            $response = Http::timeout(30)
                ->connectTimeout(5)
                ->retry(2, 1000)
                ->post("{$this->baseUrl}/api/compatibility/match", [
                    'person1_moon_nakshatra' => $userMoonNak,
                    'person1_moon_rashi' => $userMoonRashi,
                    'person2_moon_nakshatra' => (int) $partnerData['moon_nakshatra'],
                    'person2_moon_rashi' => (int) $partnerData['moon_rashi'],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException('Compatibility service error. Please try again.');
            }

            $result = $response->json();

            return CompatibilityReport::create([
                'user_id' => $user->id,
                'partner_name' => $partnerData['name'] ?? null,
                'partner_dob' => $partnerData['dob'] ?? null,
                'partner_moon_nakshatra' => (int) $partnerData['moon_nakshatra'],
                'partner_moon_rashi' => (int) $partnerData['moon_rashi'],
                'score' => $result['total_score'] ?? 0,
                'result_json' => $result,
                'amount_charged' => $amountToCharge,
                'wallet_transaction_id' => $walletTransactionId,
            ]);
        });
    }
}
