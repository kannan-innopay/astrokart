<?php

use App\Enums\AstrologerDocumentType;
use App\Models\Astrologer;
use App\Models\Expertise;
use App\Models\Language;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

test('an admin can edit astrologer details with price in rupees', function () {
    $admin = User::factory()->admin()->create();
    $astrologer = Astrologer::factory()->approved()->create();
    $expertise = Expertise::factory()->create();
    $language = Language::factory()->create();

    $this->actingAs($admin)
        ->put(route('admin.astrologers.update', $astrologer), [
            'name' => 'Updated Astro',
            'bio' => 'Updated bio',
            'years_of_experience' => 7,
            'price_per_minute' => 50, // rupees
            'consultation_modes' => ['chat'],
            'expertise_ids' => [$expertise->id],
            'language_ids' => [$language->id],
        ])
        ->assertRedirect(route('admin.astrologers.show', $astrologer));

    $astrologer->refresh();
    expect($astrologer->user->name)->toBe('Updated Astro');
    expect($astrologer->price_per_minute)->toBe(5000); // stored as paise
    expect($astrologer->years_of_experience)->toBe(7);
    expect($astrologer->expertises)->toHaveCount(1);
});

test('an admin can delete an astrologer and their account', function () {
    $admin = User::factory()->admin()->create();
    $astrologer = Astrologer::factory()->approved()->create();
    $userId = $astrologer->user_id;

    $this->actingAs($admin)
        ->delete(route('admin.astrologers.destroy', $astrologer))
        ->assertRedirect(route('admin.astrologers.index'));

    expect(Astrologer::find($astrologer->id))->toBeNull();
    expect(User::find($userId))->toBeNull();
});

test('deleting an astrologer removes their uploaded files', function () {
    Storage::fake('public');
    Storage::fake('local');

    $admin = User::factory()->admin()->create();
    $astrologer = Astrologer::factory()->approved()->create();

    Storage::disk('public')->put('astrologer-photos/p1.jpg', 'x');
    Storage::disk('local')->put('astrologer-documents/d1.pdf', 'x');

    $astrologer->update(['photo' => 'astrologer-photos/p1.jpg']);
    $astrologer->photos()->create(['file_path' => 'astrologer-photos/p1.jpg', 'is_primary' => true, 'sort_order' => 0]);
    $astrologer->documents()->create(['document_type' => AstrologerDocumentType::Aadhaar, 'file_path' => 'astrologer-documents/d1.pdf']);

    $this->actingAs($admin)->delete(route('admin.astrologers.destroy', $astrologer));

    Storage::disk('public')->assertMissing('astrologer-photos/p1.jpg');
    Storage::disk('local')->assertMissing('astrologer-documents/d1.pdf');
});
