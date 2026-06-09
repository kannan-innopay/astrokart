<?php

namespace App\Services;

use App\Enums\ConsultationStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Events\BirthChartRequested;
use App\Events\BirthChartShared;
use App\Events\ChatMessageSent;
use App\Events\ConsultationAccepted;
use App\Events\ConsultationEnded;
use App\Events\ConsultationRejected;
use App\Events\ConsultationRequested;
use App\Events\WalletBalanceUpdated;
use App\Jobs\ConsultationTimeout;
use App\Jobs\ProcessConsultationBilling;
use App\Models\Astrologer;
use App\Models\ChatMessage;
use App\Models\Consultation;
use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Validation\ValidationException;

class ConsultationService
{
    private const MINIMUM_MINUTES = 5;

    public function requestConsultation(User $user, Astrologer $astrologer): Consultation
    {
        if (! $astrologer->isApproved() || ! $astrologer->is_online) {
            throw ValidationException::withMessages([
                'astrologer' => ['This astrologer is not available right now.'],
            ]);
        }

        if ($user->id === $astrologer->user_id) {
            throw ValidationException::withMessages([
                'astrologer' => ['You cannot consult yourself.'],
            ]);
        }

        // Check for existing active/pending consultation
        $existing = Consultation::where('user_id', $user->id)
            ->whereIn('status', [ConsultationStatus::Pending, ConsultationStatus::Active])
            ->exists();

        if ($existing) {
            throw ValidationException::withMessages([
                'consultation' => ['You already have an active or pending consultation.'],
            ]);
        }

        // Check minimum wallet balance
        $minimumBalance = $astrologer->price_per_minute * self::MINIMUM_MINUTES;
        $wallet = $user->wallet;

        if (! $wallet || ! $wallet->hasSufficientBalance($minimumBalance)) {
            throw ValidationException::withMessages([
                'wallet' => ['Insufficient wallet balance. Minimum ₹' . number_format($minimumBalance / 100, 2) . ' required.'],
            ]);
        }

        $consultation = Consultation::create([
            'user_id' => $user->id,
            'astrologer_id' => $astrologer->id,
            'consultation_type' => 'chat',
            'status' => ConsultationStatus::Pending,
            'price_per_minute' => $astrologer->price_per_minute,
            'commission_rate' => 30,
        ]);

        ConsultationRequested::dispatch($consultation->load(['user', 'astrologer.user']));

        // Auto-reject after 60 seconds if not accepted
        ConsultationTimeout::dispatch($consultation)->delay(now()->addSeconds(60));

        return $consultation;
    }

    public function acceptConsultation(Consultation $consultation): Consultation
    {
        if ($consultation->status !== ConsultationStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => ['This consultation cannot be accepted.'],
            ]);
        }

        $consultation->update([
            'status' => ConsultationStatus::Active,
            'started_at' => now(),
        ]);

        ConsultationAccepted::dispatch($consultation->load(['astrologer.user']));

        // Start billing — first deduction after 1 minute
        ProcessConsultationBilling::dispatch($consultation)->delay(now()->addMinutes(1));

        return $consultation;
    }

    public function rejectConsultation(Consultation $consultation): Consultation
    {
        if ($consultation->status !== ConsultationStatus::Pending) {
            throw ValidationException::withMessages([
                'status' => ['This consultation cannot be rejected.'],
            ]);
        }

        $consultation->update([
            'status' => ConsultationStatus::Rejected,
            'ended_by' => 'astrologer',
            'end_reason' => 'Astrologer declined the request.',
        ]);

        ConsultationRejected::dispatch($consultation);

        return $consultation;
    }

    public function endConsultation(Consultation $consultation, string $endedBy, string $reason = 'Session ended'): Consultation
    {
        if (! $consultation->isActive()) {
            throw ValidationException::withMessages([
                'status' => ['This consultation is not active.'],
            ]);
        }

        $endedAt = now();
        $durationSeconds = (int) $consultation->started_at->diffInSeconds($endedAt);
        $durationMinutes = (int) ceil($durationSeconds / 60);
        $grossAmount = $durationMinutes * $consultation->price_per_minute;
        $platformCommission = (int) ($grossAmount * $consultation->commission_rate / 100);
        $astrologerEarning = $grossAmount - $platformCommission;

        $consultation->update([
            'status' => ConsultationStatus::Completed,
            'ended_at' => $endedAt,
            'duration_seconds' => $durationSeconds,
            'gross_amount' => $grossAmount,
            'platform_commission' => $platformCommission,
            'astrologer_earning' => $astrologerEarning,
            'ended_by' => $endedBy,
            'end_reason' => $reason,
        ]);

        ConsultationEnded::dispatch($consultation->fresh());

        return $consultation;
    }

    public function sendMessage(Consultation $consultation, User $sender, string $message): ChatMessage
    {
        if (! $consultation->isActive()) {
            throw ValidationException::withMessages([
                'consultation' => ['This consultation is not active.'],
            ]);
        }

        $senderType = $sender->id === $consultation->user_id ? 'user' : 'astrologer';

        $chatMessage = ChatMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id' => $sender->id,
            'sender_type' => $senderType,
            'message' => $message,
        ]);

        ChatMessageSent::dispatch($chatMessage->load('sender'));

        return $chatMessage;
    }

    public function requestBirthChart(Consultation $consultation, User $astrologer): ChatMessage
    {
        if (! $consultation->isActive()) {
            throw ValidationException::withMessages([
                'consultation' => ['This consultation is not active.'],
            ]);
        }

        $chatMessage = ChatMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id' => $astrologer->id,
            'sender_type' => 'astrologer',
            'type' => 'chart_request',
            'message' => $astrologer->name.' has requested access to your birth chart.',
        ]);

        BirthChartRequested::dispatch($chatMessage->load('sender'));

        return $chatMessage;
    }

    public function shareBirthChart(Consultation $consultation, User $user): ChatMessage
    {
        if (! $consultation->isActive()) {
            throw ValidationException::withMessages([
                'consultation' => ['This consultation is not active.'],
            ]);
        }

        $birthChart = $user->birth_chart;
        if (! $birthChart) {
            throw ValidationException::withMessages([
                'chart' => ['No birth chart available to share.'],
            ]);
        }

        // Build chart summary for display
        $chartSummary = [
            'lagna' => $birthChart['lagna'] ?? null,
            'grahas' => $birthChart['grahas'] ?? [],
            'panchanga' => $birthChart['panchanga'] ?? null,
            'ayanamsa' => $birthChart['ayanamsa'] ?? null,
            'user_name' => $user->name,
            'date_of_birth' => $user->date_of_birth?->format('d M Y'),
            'time_of_birth' => $user->time_of_birth,
            'place_of_birth' => $user->place_of_birth,
        ];

        $chatMessage = ChatMessage::create([
            'consultation_id' => $consultation->id,
            'sender_id' => $user->id,
            'sender_type' => 'user',
            'type' => 'chart_shared',
            'message' => $user->name.' shared their birth chart.',
            'metadata' => $chartSummary,
        ]);

        BirthChartShared::dispatch($chatMessage->load('sender'));

        return $chatMessage;
    }

    public function rateConsultation(Consultation $consultation, int $rating, ?string $review = null): Consultation
    {
        if ($consultation->status !== ConsultationStatus::Completed) {
            throw ValidationException::withMessages([
                'status' => ['Only completed consultations can be rated.'],
            ]);
        }

        if ($consultation->rated_at) {
            throw ValidationException::withMessages([
                'rating' => ['This consultation has already been rated.'],
            ]);
        }

        $consultation->update([
            'rating' => $rating,
            'review' => $review,
            'rated_at' => now(),
        ]);

        // Update astrologer's average rating
        $astrologer = $consultation->astrologer;
        $avgRating = Consultation::where('astrologer_id', $astrologer->id)
            ->whereNotNull('rating')
            ->avg('rating');
        $totalReviews = Consultation::where('astrologer_id', $astrologer->id)
            ->whereNotNull('rating')
            ->count();

        $astrologer->update([
            'rating' => round($avgRating, 2),
            'total_reviews' => $totalReviews,
        ]);

        return $consultation;
    }

    public function debitForMinute(Consultation $consultation): bool
    {
        $user = $consultation->user;
        $wallet = $user->wallet;

        if (! $wallet || ! $wallet->hasSufficientBalance($consultation->price_per_minute)) {
            $this->endConsultation($consultation, 'system', 'Insufficient wallet balance.');

            return false;
        }

        $balanceBefore = $wallet->balance;
        $wallet->debit($consultation->price_per_minute);

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => TransactionType::Debit,
            'amount' => $consultation->price_per_minute,
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->fresh()->balance,
            'reference_type' => 'consultation',
            'reference_id' => $consultation->id,
            'status' => TransactionStatus::Completed,
            'remarks' => 'Per-minute consultation charge',
        ]);

        WalletBalanceUpdated::dispatch($user, $wallet->fresh()->balance);

        return true;
    }
}
