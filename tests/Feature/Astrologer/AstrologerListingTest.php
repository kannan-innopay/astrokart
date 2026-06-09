<?php

use App\Models\Astrologer;
use App\Models\Expertise;

test('it lists approved astrologers publicly', function () {
    Astrologer::factory(3)->approved()->create();
    Astrologer::factory(2)->create(); // unapproved

    $response = $this->getJson('/api/v1/astrologers');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
});

test('it does not show unapproved astrologers in public listing', function () {
    Astrologer::factory()->rejected()->create();
    Astrologer::factory()->pendingVerification()->create();

    $response = $this->getJson('/api/v1/astrologers');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(0);
});

test('it can filter by expertise', function () {
    $expertise = Expertise::factory()->create();
    $matchingAstrologer = Astrologer::factory()->approved()->create();
    $matchingAstrologer->expertises()->attach($expertise);

    Astrologer::factory()->approved()->create();

    $response = $this->getJson("/api/v1/astrologers?expertise_id={$expertise->id}");

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
    expect($response->json('data.0.id'))->toBe($matchingAstrologer->id);
});

test('it can filter by online status', function () {
    Astrologer::factory()->online()->create();
    Astrologer::factory()->approved()->create();

    $response = $this->getJson('/api/v1/astrologers?is_online=1');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(1);
});

test('it can view single astrologer profile', function () {
    $astrologer = Astrologer::factory()->approved()->create();

    $response = $this->getJson("/api/v1/astrologers/{$astrologer->uuid}");

    $response->assertOk()
        ->assertJsonPath('data.id', $astrologer->id)
        ->assertJsonStructure([
            'data' => ['id', 'name', 'bio', 'rating', 'price_per_minute', 'expertises', 'languages'],
        ]);
});
