<?php

namespace App\Models;

use App\Concerns\HasUuid;
use App\Enums\AstrologerStatus;
use Database\Factories\AstrologerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Public URL for the primary profile photo (the `photo` column stores a
     * path relative to the public disk).
     *
     * @return Attribute<?string, never>
     */
    protected function photoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->photo ? Storage::disk('public')->url($this->photo) : null);
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

    public function photos(): HasMany
    {
        return $this->hasMany(AstrologerPhoto::class)->orderBy('sort_order');
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(AstrologerPhoto::class)->where('is_primary', true);
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
