<?php

namespace App\Services;

use App\Enums\AstrologerStatus;
use App\Enums\UserRole;
use App\Models\Astrologer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class AstrologerService
{
    public function apply(User $user, array $data): Astrologer
    {
        if (Astrologer::where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'user' => ['You have already applied as an astrologer.'],
            ]);
        }

        $user->update(['role' => UserRole::Astrologer]);

        $astrologer = Astrologer::create([
            'user_id' => $user->id,
            'bio' => $data['bio'] ?? null,
            'years_of_experience' => $data['years_of_experience'],
            'price_per_minute' => $data['price_per_minute'],
            'consultation_modes' => $data['consultation_modes'] ?? ['chat'],
            'status' => AstrologerStatus::Applied,
        ]);

        if (! empty($data['expertise_ids'])) {
            $astrologer->expertises()->attach($data['expertise_ids']);
        }

        if (! empty($data['language_ids'])) {
            $astrologer->languages()->attach($data['language_ids']);
        }

        return $astrologer->load(['expertises', 'languages', 'user']);
    }

    public function updateProfile(Astrologer $astrologer, array $data): Astrologer
    {
        $astrologer->update(collect($data)->only([
            'bio',
            'years_of_experience',
            'price_per_minute',
            'consultation_modes',
            'bank_account_name',
            'bank_account_number',
            'bank_ifsc_code',
            'upi_id',
        ])->toArray());

        if (isset($data['expertise_ids'])) {
            $astrologer->expertises()->sync($data['expertise_ids']);
        }

        if (isset($data['language_ids'])) {
            $astrologer->languages()->sync($data['language_ids']);
        }

        return $astrologer->load(['expertises', 'languages', 'user']);
    }

    public function updateAvailability(Astrologer $astrologer, array $slots): void
    {
        $astrologer->availabilities()->delete();

        foreach ($slots as $slot) {
            $astrologer->availabilities()->create($slot);
        }
    }

    public function goOnline(Astrologer $astrologer): Astrologer
    {
        if (! $astrologer->canGoOnline()) {
            throw ValidationException::withMessages([
                'status' => ['Only approved astrologers can go online.'],
            ]);
        }

        $astrologer->update(['is_online' => true]);

        return $astrologer;
    }

    public function goOffline(Astrologer $astrologer): Astrologer
    {
        $astrologer->update(['is_online' => false]);

        return $astrologer;
    }

    public function listApproved(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Astrologer::with(['user', 'expertises', 'languages'])
            ->where('status', AstrologerStatus::Approved);

        if (! empty($filters['is_online'])) {
            $query->where('is_online', true);
        }

        if (! empty($filters['expertise_id'])) {
            $query->whereHas('expertises', fn ($q) => $q->where('expertises.id', $filters['expertise_id']));
        }

        if (! empty($filters['language_id'])) {
            $query->whereHas('languages', fn ($q) => $q->where('languages.id', $filters['language_id']));
        }

        if (! empty($filters['min_price'])) {
            $query->where('price_per_minute', '>=', $filters['min_price']);
        }

        if (! empty($filters['max_price'])) {
            $query->where('price_per_minute', '<=', $filters['max_price']);
        }

        if (! empty($filters['min_rating'])) {
            $query->where('rating', '>=', $filters['min_rating']);
        }

        if (! empty($filters['min_experience'])) {
            $query->where('years_of_experience', '>=', $filters['min_experience']);
        }

        $sortField = $filters['sort_by'] ?? 'rating';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $allowedSorts = ['rating', 'price_per_minute', 'years_of_experience', 'total_reviews'];

        if (in_array($sortField, $allowedSorts)) {
            $query->orderBy($sortField, $sortDirection);
        }

        return $query->paginate($perPage);
    }

    public function updateStatus(Astrologer $astrologer, AstrologerStatus $status, ?string $notes = null): Astrologer
    {
        $data = ['status' => $status];

        if ($notes !== null) {
            $data['verification_notes'] = $notes;
        }

        if ($status === AstrologerStatus::Approved) {
            $data['verified_at'] = now();
        }

        if ($status !== AstrologerStatus::Approved) {
            $data['is_online'] = false;
        }

        $astrologer->update($data);

        return $astrologer->fresh(['user', 'expertises', 'languages']);
    }
}
