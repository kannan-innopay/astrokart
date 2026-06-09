<?php

use App\Models\User;

test('compatibility index page is accessible', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('compatibility.index'))
        ->assertOk()
        ->assertSeeText('Compatibility');
});

test('compatibility match requires moon nakshatra and rashi', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('compatibility.match'), [])
        ->assertSessionHasErrors(['moon_nakshatra', 'moon_rashi']);
});

test('compatibility match requires birth chart', function () {
    $user = User::factory()->create(['birth_chart' => null]);

    $this->actingAs($user)
        ->post(route('compatibility.match'), [
            'moon_nakshatra' => 3,
            'moon_rashi' => 1,
        ])
        ->assertRedirect()
        ->assertSessionHas('error');
});
