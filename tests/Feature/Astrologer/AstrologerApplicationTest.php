<?php

use App\Models\Expertise;
use App\Models\Language;
use App\Models\User;

test('it allows a customer to apply as astrologer', function () {
    $user = User::factory()->customer()->create();
    $expertises = Expertise::factory(2)->create();
    $languages = Language::factory(2)->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/astrologer/apply', [
            'bio' => 'Experienced astrologer with 10 years of practice.',
            'years_of_experience' => 10,
            'price_per_minute' => 2500,
            'consultation_modes' => ['chat'],
            'expertise_ids' => $expertises->pluck('id')->toArray(),
            'language_ids' => $languages->pluck('id')->toArray(),
        ]);

    $response->assertCreated()
        ->assertJsonPath('data.years_of_experience', 10)
        ->assertJsonPath('data.price_per_minute', 2500)
        ->assertJsonPath('data.status', 'applied');

    $user->refresh();
    expect($user->role->value)->toBe('astrologer');
});

test('it creates astrologer profile with expertises and languages', function () {
    $user = User::factory()->customer()->create();
    $expertises = Expertise::factory(3)->create();
    $languages = Language::factory(2)->create();

    $this->actingAs($user)
        ->postJson('/api/v1/astrologer/apply', [
            'years_of_experience' => 5,
            'price_per_minute' => 1500,
            'expertise_ids' => $expertises->pluck('id')->toArray(),
            'language_ids' => $languages->pluck('id')->toArray(),
        ]);

    $astrologer = $user->fresh()->astrologerProfile;
    expect($astrologer->expertises)->toHaveCount(3);
    expect($astrologer->languages)->toHaveCount(2);
});

test('it rejects duplicate application', function () {
    $user = User::factory()->customer()->create();
    $expertise = Expertise::factory()->create();
    $language = Language::factory()->create();

    $payload = [
        'years_of_experience' => 5,
        'price_per_minute' => 1500,
        'expertise_ids' => [$expertise->id],
        'language_ids' => [$language->id],
    ];

    $this->actingAs($user)->postJson('/api/v1/astrologer/apply', $payload);

    $response = $this->actingAs($user)->postJson('/api/v1/astrologer/apply', $payload);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['user']);
});

test('it validates application fields', function () {
    $user = User::factory()->customer()->create();

    $response = $this->actingAs($user)
        ->postJson('/api/v1/astrologer/apply', []);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['years_of_experience', 'price_per_minute', 'expertise_ids', 'language_ids']);
});
