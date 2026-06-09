<?php

use App\Models\Astrologer;
use App\Models\User;

test('it allows admin to list all astrologers', function () {
    $admin = User::factory()->admin()->create();
    Astrologer::factory(3)->create();

    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/astrologers');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(3);
});

test('it allows admin to filter astrologers by status', function () {
    $admin = User::factory()->admin()->create();
    Astrologer::factory(2)->approved()->create();
    Astrologer::factory(1)->rejected()->create();

    $response = $this->actingAs($admin)
        ->getJson('/api/v1/admin/astrologers?status=approved');

    $response->assertOk();
    expect($response->json('data'))->toHaveCount(2);
});

test('it allows admin to view astrologer details', function () {
    $admin = User::factory()->admin()->create();
    $astrologer = Astrologer::factory()->create();

    $response = $this->actingAs($admin)
        ->getJson("/api/v1/admin/astrologers/{$astrologer->uuid}");

    $response->assertOk()
        ->assertJsonPath('data.id', $astrologer->id);
});

test('it allows admin to approve an astrologer', function () {
    $admin = User::factory()->admin()->create();
    $astrologer = Astrologer::factory()->create();

    $response = $this->actingAs($admin)
        ->patchJson("/api/v1/admin/astrologers/{$astrologer->uuid}/status", [
            'status' => 'approved',
            'notes' => 'Documents verified successfully.',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'approved');

    $astrologer->refresh();
    expect($astrologer->verified_at)->not->toBeNull();
    expect($astrologer->verification_notes)->toBe('Documents verified successfully.');
});

test('it allows admin to reject an astrologer', function () {
    $admin = User::factory()->admin()->create();
    $astrologer = Astrologer::factory()->create();

    $response = $this->actingAs($admin)
        ->patchJson("/api/v1/admin/astrologers/{$astrologer->uuid}/status", [
            'status' => 'rejected',
            'notes' => 'Invalid documentation.',
        ]);

    $response->assertOk()
        ->assertJsonPath('data.status', 'rejected');
});

test('it rejects non-admin access to admin routes', function () {
    $customer = User::factory()->customer()->create();

    $response = $this->actingAs($customer)
        ->getJson('/api/v1/admin/astrologers');

    $response->assertForbidden();
});

test('it allows super admin access to admin routes', function () {
    $superAdmin = User::factory()->superAdmin()->create();
    Astrologer::factory()->create();

    $response = $this->actingAs($superAdmin)
        ->getJson('/api/v1/admin/astrologers');

    $response->assertOk();
});
