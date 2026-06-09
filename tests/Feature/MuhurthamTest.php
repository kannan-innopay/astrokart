<?php

use App\Models\User;
use App\Models\Wallet;

test('muhurtham index page is accessible', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('muhurtham.index'))
        ->assertOk()
        ->assertSeeText('Muhurtham');
});

test('muhurtham search requires valid purpose and dates', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 10000]);

    $this->actingAs($user)
        ->post(route('muhurtham.search'), [
            'purpose' => 'invalid_purpose',
            'date_start' => now()->format('Y-m-d'),
            'date_end' => now()->addDays(30)->format('Y-m-d'),
        ])
        ->assertSessionHasErrors('purpose');
});

test('muhurtham search requires sufficient wallet balance for non-premium users', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100]); // Less than ₹5

    $this->actingAs($user)
        ->post(route('muhurtham.search'), [
            'purpose' => 'marriage',
            'date_start' => now()->addDay()->format('Y-m-d'),
            'date_end' => now()->addDays(30)->format('Y-m-d'),
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});
