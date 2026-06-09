<?php

namespace App\Models;

use App\Concerns\HasUuid;

use App\Enums\ConsultationStatus;
use Database\Factories\ConsultationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'astrologer_id', 'consultation_type', 'status',
    'started_at', 'ended_at', 'duration_seconds',
    'price_per_minute', 'gross_amount', 'platform_commission',
    'astrologer_earning', 'commission_rate',
    'ended_by', 'end_reason', 'rated_at', 'rating', 'review',
])]
class Consultation extends Model
{
    /** @use HasFactory<ConsultationFactory> */
    use HasFactory, HasUuid;

    protected function casts(): array
    {
        return [
            'status' => ConsultationStatus::class,
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'rated_at' => 'datetime',
            'duration_seconds' => 'integer',
            'price_per_minute' => 'integer',
            'gross_amount' => 'integer',
            'platform_commission' => 'integer',
            'astrologer_earning' => 'integer',
            'commission_rate' => 'integer',
            'rating' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function astrologer(): BelongsTo
    {
        return $this->belongsTo(Astrologer::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class, 'reference_id')
            ->where('reference_type', 'consultation');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ConsultationStatus::Active);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', ConsultationStatus::Pending);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForAstrologer(Builder $query, int $astrologerId): Builder
    {
        return $query->where('astrologer_id', $astrologerId);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isEnded(): bool
    {
        return $this->status->isEnded();
    }
}
