<?php

use App\Models\Astrologer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('a sales user sees only their own referred astrologers', function () {
    $sales = User::factory()->sales()->create();
    $other = User::factory()->sales()->create();

    $mine = Astrologer::factory()->create(['sales_user_id' => $sales->id]);
    $mine->user->update(['name' => 'My Astro']);

    $theirs = Astrologer::factory()->create(['sales_user_id' => $other->id]);
    $theirs->user->update(['name' => 'Their Astro']);

    $this->actingAs($sales)
        ->get(route('sales.dashboard'))
        ->assertOk()
        ->assertSee('My Astro')
        ->assertDontSee('Their Astro');
});

test('the sales dashboard requires the sales role', function () {
    $customer = User::factory()->customer()->create();

    $this->actingAs($customer)
        ->get(route('sales.dashboard'))
        ->assertForbidden();
});

test('a sales user logging in is sent to the sales dashboard', function () {
    $sales = User::factory()->sales()->create([
        'email' => 'rep@example.com',
        'password' => Hash::make('secret-password'),
    ]);

    $this->post(route('admin.login.submit'), [
        'email' => 'rep@example.com',
        'password' => 'secret-password',
    ])->assertRedirect(route('sales.dashboard'));

    $this->assertAuthenticatedAs($sales);
});
