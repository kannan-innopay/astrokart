<?php

use App\Enums\SubscriptionPlan;
use App\Enums\SubscriptionStatus;
use App\Models\PlanEntitlement;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Wallet;
use App\Services\SubscriptionService;

beforeEach(function () {
    // Seed entitlements
    foreach (['daily', 'monthly', 'yearly'] as $plan) {
        foreach (['full_chart_analysis', 'daily_predictions'] as $entitlement) {
            PlanEntitlement::firstOrCreate(['plan' => $plan, 'entitlement' => $entitlement]);
        }
    }
});

test('user can subscribe to monthly plan via wallet', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 50000]);

    $this->actingAs($user)
        ->post(route('subscription.subscribe'), ['plan' => 'monthly'])
        ->assertRedirect(route('subscription.index'));

    $freshUser = User::find($user->id);
    expect($freshUser->isPremium())->toBeTrue();
    expect(Subscription::where('user_id', $user->id)->first())
        ->plan->toBe(SubscriptionPlan::Monthly)
        ->status->toBe(SubscriptionStatus::Active)
        ->amount->toBe(9900);

    // Wallet should be debited
    expect($user->wallet->refresh()->balance)->toBe(50000 - 9900);
});

test('user cannot subscribe with insufficient balance', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100]);

    $this->actingAs($user)
        ->post(route('subscription.subscribe'), ['plan' => 'monthly'])
        ->assertRedirect();

    expect($user->refresh()->isPremium())->toBeFalse();
});

test('user cannot subscribe twice', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100000]);

    // First subscription
    $this->actingAs($user)
        ->post(route('subscription.subscribe'), ['plan' => 'monthly'])
        ->assertRedirect(route('subscription.index'));

    // Second attempt should fail
    $this->actingAs(User::find($user->id))
        ->post(route('subscription.subscribe'), ['plan' => 'daily'])
        ->assertRedirect();

    expect(Subscription::where('user_id', $user->id)->count())->toBe(1);
});

test('user can cancel subscription', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100000]);

    $service = app(SubscriptionService::class);
    $subscription = $service->subscribe($user, SubscriptionPlan::Monthly);

    $this->actingAs($user)
        ->post(route('subscription.cancel'))
        ->assertRedirect(route('subscription.index'));

    expect($subscription->refresh()->status)->toBe(SubscriptionStatus::Cancelled);
    expect($subscription->cancelled_at)->not->toBeNull();
});

test('premium user has entitlements', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 100000]);

    expect($user->hasEntitlement('full_chart_analysis'))->toBeFalse();

    $service = app(SubscriptionService::class);
    $service->subscribe($user, SubscriptionPlan::Daily);

    // Need to refresh the user to clear memoized values
    $user = $user->fresh();
    expect($user->hasEntitlement('full_chart_analysis'))->toBeTrue();
    expect($user->hasEntitlement('daily_predictions'))->toBeTrue();
    expect($user->hasEntitlement('nonexistent_feature'))->toBeFalse();
});

test('daily subscription can be renewed', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 10000]);

    $service = app(SubscriptionService::class);
    $subscription = $service->subscribe($user, SubscriptionPlan::Daily);

    // Simulate time passing - set next_billing_at to past
    $subscription->update(['next_billing_at' => now()->subHour()]);

    $renewed = $service->renewDaily($subscription->refresh());

    expect($renewed)->toBeTrue();
    expect($subscription->refresh()->status)->toBe(SubscriptionStatus::Active);
    expect($user->wallet->refresh()->balance)->toBe(10000 - 300 - 300); // initial + renewal
});

test('daily renewal fails with insufficient balance', function () {
    $user = User::factory()->create();
    Wallet::factory()->create(['user_id' => $user->id, 'balance' => 300]); // exactly enough for first

    $service = app(SubscriptionService::class);
    $subscription = $service->subscribe($user, SubscriptionPlan::Daily);

    $subscription->update(['next_billing_at' => now()->subHour()]);

    $renewed = $service->renewDaily($subscription->refresh());

    expect($renewed)->toBeFalse();
    expect($subscription->refresh()->status)->toBe(SubscriptionStatus::PastDue);
});

test('expire command marks expired subscriptions', function () {
    $user = User::factory()->create();
    Subscription::create([
        'user_id' => $user->id,
        'plan' => SubscriptionPlan::Monthly,
        'amount' => 9900,
        'status' => SubscriptionStatus::Active,
        'starts_at' => now()->subDays(31),
        'expires_at' => now()->subDay(),
    ]);

    $this->artisan('subscription:expire')
        ->assertSuccessful();

    expect(Subscription::where('user_id', $user->id)->first()->status)
        ->toBe(SubscriptionStatus::Expired);
});

test('subscription pricing page is accessible', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('subscription.index'))
        ->assertOk()
        ->assertSeeText('Daily Pass')
        ->assertSeeText('Monthly')
        ->assertSeeText('Yearly');
});
