<?php

namespace App\Models;

use App\Concerns\HasUuid;

use App\Enums\AstrologerStatus;
use Database\Factories\AstrologerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'photo',
    'bio',
    'years_of_experience',
    'price_per_minute',
    'consultation_modes',
    'is_online',
    'rating',
    'total_reviews',
    'status',
    'verification_notes',
    'verified_at',
    'bank_account_name',
    'bank_account_number',
    'bank_ifsc_code',
    'upi_id',
])]
class Astrologer extends Model
{
    /** @use HasFactory<AstrologerFactory> */
    use HasFactory, HasUuid;

    protected function casts(): array
    {
        return [
            'status' => AstrologerStatus::class,
            'consultation_modes' => 'array',
            'is_online' => 'boolean',
            'rating' => 'decimal:2',
            'price_per_minute' => 'integer',
            'years_of_experience' => 'integer',
            'total_reviews' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function expertises(): BelongsToMany
    {
        return $this->belongsToMany(Expertise::class, 'astrologer_expertise')->withTimestamps();
    }

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'astrologer_language')->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(AstrologerDocument::class);
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(AstrologerAvailability::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function isApproved(): bool
    {
        return $this->status === AstrologerStatus::Approved;
    }

    public function canGoOnline(): bool
    {
        return $this->status->canGoOnline();
    }
}
