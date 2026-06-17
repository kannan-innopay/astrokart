<?php

namespace App\Services;

use App\Enums\AstrologerDocumentType;
use App\Enums\AstrologerStatus;
use App\Enums\ConsultationMode;
use App\Enums\UserRole;
use App\Jobs\SendAstrologerApprovedSms;
use App\Models\Astrologer;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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

        // A number registered as a customer cannot be reused to become an astrologer.
        if ($user->isCustomer()) {
            throw ValidationException::withMessages([
                'mobile' => [__('astrologer.number_is_customer')],
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

    /**
     * Complete the public astrologer signup wizard: create the profile, attach
     * expertise/languages, store uploaded photos and KYC documents, and capture
     * bank settlement details. The profile starts as Applied, pending admin review.
     *
     * @param  array<string, mixed>  $data
     */
    public function register(User $user, array $data): Astrologer
    {
        if (Astrologer::where('user_id', $user->id)->exists()) {
            throw ValidationException::withMessages([
                'user' => ['You have already registered as an astrologer.'],
            ]);
        }

        $photoPaths = collect($data['photos'] ?? [])
            ->map(fn (UploadedFile $photo): string => $photo->store('astrologer-photos', 'public'));

        return DB::transaction(function () use ($user, $data, $photoPaths) {
            $user->update([
                'name' => $data['name'],
                'role' => UserRole::Astrologer,
            ]);

            $astrologer = Astrologer::create([
                'user_id' => $user->id,
                'photo' => $photoPaths->first(),
                'bio' => $data['bio'] ?? null,
                'years_of_experience' => $data['years_of_experience'],
                // Signup form captures rupees; the rest of the money system is in paise.
                'price_per_minute' => (int) $data['price_per_minute'] * 100,
                'consultation_modes' => $data['consultation_modes'] ?? [ConsultationMode::Chat->value],
                'status' => AstrologerStatus::Applied,
                'bank_account_name' => $data['bank_account_name'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'bank_ifsc_code' => $data['bank_ifsc_code'] ?? null,
                'upi_id' => $data['upi_id'] ?? null,
            ]);

            $astrologer->expertises()->attach($data['expertise_ids']);
            $astrologer->languages()->attach($data['language_ids']);

            $photoPaths->each(fn (string $path, int $index) => $astrologer->photos()->create([
                'file_path' => $path,
                'is_primary' => $index === 0,
                'sort_order' => $index,
            ]));

            $this->storeKycDocuments($astrologer, $data);

            return $astrologer->load(['expertises', 'languages', 'photos', 'documents', 'user']);
        });
    }

    /**
     * Persist any uploaded KYC documents (Aadhaar, PAN, certificate) to the
     * private disk. All KYC is optional, so missing files are skipped.
     *
     * @param  array<string, mixed>  $data
     */
    private function storeKycDocuments(Astrologer $astrologer, array $data): void
    {
        $documents = [
            [AstrologerDocumentType::Aadhaar, 'aadhaar_number', 'aadhaar_file'],
            [AstrologerDocumentType::Pan, 'pan_number', 'pan_file'],
            [AstrologerDocumentType::Certificate, null, 'certificate_file'],
        ];

        foreach ($documents as [$type, $numberKey, $fileKey]) {
            $file = $data[$fileKey] ?? null;

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $astrologer->documents()->create([
                'document_type' => $type,
                'document_number' => $numberKey ? ($data[$numberKey] ?? null) : null,
                'file_path' => $file->store('astrologer-documents', 'local'),
            ]);
        }
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
        $wasApproved = $astrologer->status === AstrologerStatus::Approved;

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

        if ($status === AstrologerStatus::Approved && ! $wasApproved) {
            SendAstrologerApprovedSms::dispatch($astrologer);
        }

        return $astrologer->fresh(['user', 'expertises', 'languages']);
    }
}
