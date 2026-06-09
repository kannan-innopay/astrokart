<?php

use App\Models\Astrologer;
use App\Models\Expertise;
use App\Models\Language;
use App\Models\User;

test('it allows astrologer to view own profile', function () {
    $astrologer = Astrologer::factory()->approved()->create();

    $response = $this->actingAs($astrologer->user)
        ->getJson('/api/v1/astrologer/profile');

    $response->assertOk()
        ->assertJsonPath('data.id', $astrologer->id);
});

test('it allows astrologer to update own profile', function () {
    $astrologer = Astrologer::factory()->approved()->create();

    $response = $this->actingAs($astrologer->user)
        ->putJson('/api/v1/astrologer/profile', [
            'bio' => 'Updated bio text',
            'price_per_minute' => 3000,
        ]);

    $response->assertOk()
        ->assertJsonPath('data.bio', 'Updated bio text')
        ->assertJsonPath('data.price_per_minute', 3000);
});

test('it allows astrologer to update availability', function () {
    $astrologer = Astrologer::factory()->approved()->create();

    $response = $this->actingAs($astrologer->user)
        ->putJson('/api/v1/astrologer/availability', [
            'slots' => [
                ['day_of_week' => 1, 'start_time' => '09:00', 'end_time' => '17:00'],
                ['day_of_week' => 2, 'start_time' => '10:00', 'end_time' => '18:00'],
            ],
        ]);

    $response->assertOk();
    expect($astrologer->fresh()->availabilities)->toHaveCount(2);
});

test('it allows approved astrologer to go online', function () {
    $astrologer = Astrologer::factory()->approved()->create();

    $response = $this->actingAs($astrologer->user)
        ->postJson('/api/v1/astrologer/go-online');

    $response->assertOk()
        ->assertJsonPath('data.is_online', true);
});

test('it prevents non-approved astrologer from going online', function () {
    $astrologer = Astrologer::factory()->create(); // status: applied

    $response = $this->actingAs($astrologer->user)
        ->postJson('/api/v1/astrologer/go-online');

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('it allows astrologer to go offline', function () {
    $astrologer = Astrologer::factory()->online()->create();

    $response = $this->actingAs($astrologer->user)
        ->postJson('/api/v1/astrologer/go-offline');

    $response->assertOk()
        ->assertJsonPath('data.is_online', false);
});

test('it rejects non-astrologer access to astrologer routes', function () {
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($customer)
        ->getJson('/api/v1/astrologer/profile');

    $response->assertForbidden();
});

test('it allows astrologer to sync expertises and languages', function () {
    $astrologer = Astrologer::factory()->approved()->create();
    $newExpertises = Expertise::factory(2)->create();
    $newLanguages = Language::factory(2)->create();

    $response = $this->actingAs($astrologer->user)
        ->putJson('/api/v1/astrologer/profile', [
            'expertise_ids' => $newExpertises->pluck('id')->toArray(),
            'language_ids' => $newLanguages->pluck('id')->toArray(),
        ]);

    $response->assertOk();
    expect($astrologer->fresh()->expertises)->toHaveCount(2);
    expect($astrologer->fresh()->languages)->toHaveCount(2);
});
