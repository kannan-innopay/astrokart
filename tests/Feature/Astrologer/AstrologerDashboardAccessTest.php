<?php

use App\Models\Astrologer;
use App\Models\User;

test('an astrologer without a completed profile is redirected to finish registration', function () {
    $user = User::factory()->astrologer()->create();

    $this->actingAs($user)
        ->get(route('astrologer.dashboard'))
        ->assertRedirect(route('astrologer.register.profile'));
});

test('an astrologer with a profile can access the dashboard', function () {
    $astrologer = Astrologer::factory()->approved()->create();

    $this->actingAs($astrologer->user)
        ->get(route('astrologer.dashboard'))
        ->assertOk();
});
