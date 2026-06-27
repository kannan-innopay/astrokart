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

test('an admin can delete a customer', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $customer))
        ->assertRedirect(route('admin.users.index'));

    expect(User::find($customer->id))->toBeNull();
});

test('an admin cannot delete themselves', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertForbidden();

    expect(User::find($admin->id))->not->toBeNull();
});

test('an admin cannot delete another admin without super admin rights', function () {
    $admin = User::factory()->admin()->create();
    $other = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $other))
        ->assertForbidden();
});

test('a super admin can delete an admin', function () {
    $super = User::factory()->superAdmin()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($super)
        ->delete(route('admin.users.destroy', $admin))
        ->assertRedirect(route('admin.users.index'));

    expect(User::find($admin->id))->toBeNull();
});

test('an admin can change a customer role to astrologer', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $customer), [
            'name' => $customer->name,
            'role' => 'astrologer',
            'account_status' => 'active',
        ])
        ->assertRedirect(route('admin.users.index'));

    expect($customer->fresh()->role->value)->toBe('astrologer');
});

test('an admin can edit customer details', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $customer), [
            'name' => 'Renamed Customer',
            'role' => 'customer',
            'account_status' => 'suspended',
        ]);

    $customer->refresh();
    expect($customer->name)->toBe('Renamed Customer');
    expect($customer->account_status->value)->toBe('suspended');
});

test('an admin cannot edit a super admin', function () {
    $admin = User::factory()->admin()->create();
    $super = User::factory()->superAdmin()->create();

    $this->actingAs($admin)
        ->get(route('admin.users.edit', $super))
        ->assertForbidden();
});

test('an admin cannot grant admin role', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->put(route('admin.users.update', $customer), [
            'name' => $customer->name,
            'role' => 'admin',
            'account_status' => 'active',
        ])
        ->assertSessionHasErrors('role');
});
