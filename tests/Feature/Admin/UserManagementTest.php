<?php

use App\Models\Astrologer;
use App\Models\User;

test('the admin users list shows customers but not onboarded astrologers', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->customer()->create(['name' => 'Cust Person']);
    Astrologer::factory()->approved()->create()->user->update(['name' => 'Astro Person']);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Cust Person')
        ->assertDontSee('Astro Person');
});

test('an incomplete astrologer signup (no profile) stays visible in users', function () {
    $admin = User::factory()->admin()->create();
    $incomplete = User::factory()->astrologer()->create(['name' => 'Incomplete Astro']);

    $this->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Incomplete Astro');

    // and its detail page renders (not redirected away)
    $this->actingAs($admin)
        ->get(route('admin.users.show', $incomplete))
        ->assertOk();
});

test('viewing an astrologer in the users section redirects to their astrologer page', function () {
    $admin = User::factory()->admin()->create();
    $astrologer = Astrologer::factory()->approved()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.show', $astrologer->user))
        ->assertRedirect(route('admin.astrologers.show', $astrologer));
});

test('viewing a customer in the users section works', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.show', $customer))
        ->assertOk();
});

test('an incomplete astrologer signup can be deleted', function () {
    $admin = User::factory()->admin()->create();
    $incomplete = User::factory()->astrologer()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $incomplete))
        ->assertRedirect(route('admin.users.index'));

    expect(User::find($incomplete->id))->toBeNull();
});

test('a customer cannot be deleted via the cleanup action', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $customer))
        ->assertForbidden();

    expect(User::find($customer->id))->not->toBeNull();
});

test('an onboarded astrologer cannot be deleted via the users cleanup action', function () {
    $admin = User::factory()->admin()->create();
    $astrologer = Astrologer::factory()->approved()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $astrologer->user))
        ->assertForbidden();

    expect(User::find($astrologer->user->id))->not->toBeNull();
});
