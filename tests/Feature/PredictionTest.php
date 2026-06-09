<?php

use App\Enums\SubscriptionPlan;
use App\Models\PlanEntitlement;
use App\Models\User;
use App\Models\Wallet;
use App\Services\SubscriptionService;

test('daily prediction page is accessible', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('predictions.daily'))
        ->assertOk();
});

test('daily prediction shows paywall for non-premium users', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('predictions.daily'))
        ->assertOk()
        ->assertSee('Start from');
});

test('monthly forecast page is accessible', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('predictions.monthly'))
        ->assertOk();
});

test('daily prediction available for premium users', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100000]);

    PlanEntitlement::firstOrCreate(['plan' => 'monthly', 'entitlement' => 'daily_predictions']);

    app(SubscriptionService::class)->subscribe($user, SubscriptionPlan::Monthly);

    $this->actingAs(User::find($user->id))
        ->get(route('predictions.daily'))
        ->assertOk();
});

test('generate daily predictions command runs', function () {
    $this->artisan('predictions:generate-daily')
        ->assertSuccessful();
});

test('dasha alerts command runs', function () {
    $this->artisan('dasha:send-alerts')
        ->assertSuccessful();
});
