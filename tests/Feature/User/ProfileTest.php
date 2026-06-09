<?php

use App\Models\User;
use App\Models\Wallet;

test('it can view own profile', function () {
    $user = User::factory()->create();
    Wallet::factory()->for($user)->create(['balance' => 5000]);

    $response = $this->actingAs($user)
        ->getJson('/api/v1/user/profile');

    $response->assertOk()
        ->assertJsonPath('data.id', $user->id)
        ->assertJsonPath('data.wallet_balance', 5000);
});

test('it can update own profile', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->putJson('/api/v1/user/profile', [
            'name' => 'Updated Name',
            'gender' => 'male',
            'date_of_birth' => '1990-05-15',
            'time_of_birth' => '14:30',
            'place_of_birth' => 'Mumbai',
            'preferred_language' => 'hi',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.gender', 'male')
        ->assertJsonPath('data.place_of_birth', 'Mumbai');
});

test('it validates profile update fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->putJson('/api/v1/user/profile', [
            'gender' => 'invalid_gender',
            'date_of_birth' => 'not-a-date',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['gender', 'date_of_birth']);
});

test('it rejects unauthenticated access', function () {
    $response = $this->getJson('/api/v1/user/profile');

    $response->assertUnauthorized();
});
