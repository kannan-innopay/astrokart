<?php

use App\Models\Astrologer;
use App\Models\User;

test('an admin can create a sales user', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post(route('admin.sales.store'), [
            'name' => 'Sales Rep',
            'email' => 'rep@example.com',
            'mobile' => '9876543210',
            'password' => 'secret-password',
        ])
        ->assertRedirect(route('admin.sales.index'));

    $sales = User::where('email', 'rep@example.com')->first();
    expect($sales)->not->toBeNull();
    expect($sales->role->value)->toBe('sales');
});

test('the sales list shows referred astrologer counts', function () {
    $admin = User::factory()->admin()->create();
    $sales = User::factory()->sales()->create(['name' => 'Counter Rep']);
    Astrologer::factory()->count(2)->create(['sales_user_id' => $sales->id]);

    $this->actingAs($admin)
        ->get(route('admin.sales.index'))
        ->assertOk()
        ->assertSee('Counter Rep');
});

test('a non-admin cannot access sales management', function () {
    $customer = User::factory()->customer()->create();

    $this->actingAs($customer)
        ->get(route('admin.sales.index'))
        ->assertForbidden();
});

test('removing a sales user is restricted to sales accounts', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->customer()->create();

    $this->actingAs($admin)
        ->delete(route('admin.sales.destroy', $customer))
        ->assertForbidden();
});
