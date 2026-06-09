<?php

use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('it can request an OTP for a valid mobile number', function () {
    $response = $this->postJson('/api/v1/auth/otp/request', [
        'mobile' => '9876543210',
    ]);

    $response->assertOk()
        ->assertJson(['message' => 'OTP sent successfully.']);

    $this->assertDatabaseHas('otp_verifications', [
        'mobile' => '9876543210',
        'purpose' => 'login',
    ]);
});

test('it rejects invalid mobile numbers', function () {
    $response = $this->postJson('/api/v1/auth/otp/request', [
        'mobile' => '1234',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['mobile']);
});

test('it can verify OTP and receive a token', function () {
    $otp = '123456';

    OtpVerification::create([
        'mobile' => '9876543210',
        'otp_hash' => Hash::make($otp),
        'purpose' => 'login',
        'expires_at' => now()->addMinutes(10),
    ]);

    $response = $this->postJson('/api/v1/auth/otp/verify', [
        'mobile' => '9876543210',
        'otp' => $otp,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'user' => ['id', 'name', 'mobile', 'role'],
                'token',
                'token_type',
            ],
        ]);
});

test('it creates a new user on first OTP login', function () {
    $otp = '123456';

    OtpVerification::create([
        'mobile' => '9876543210',
        'otp_hash' => Hash::make($otp),
        'purpose' => 'login',
        'expires_at' => now()->addMinutes(10),
    ]);

    $this->postJson('/api/v1/auth/otp/verify', [
        'mobile' => '9876543210',
        'otp' => $otp,
    ]);

    $this->assertDatabaseHas('users', [
        'mobile' => '9876543210',
        'role' => 'customer',
    ]);
});

test('it returns existing user on subsequent OTP login', function () {
    $user = User::factory()->create(['mobile' => '9876543210']);

    $otp = '123456';
    OtpVerification::create([
        'mobile' => '9876543210',
        'otp_hash' => Hash::make($otp),
        'purpose' => 'login',
        'expires_at' => now()->addMinutes(10),
    ]);

    $response = $this->postJson('/api/v1/auth/otp/verify', [
        'mobile' => '9876543210',
        'otp' => $otp,
    ]);

    $response->assertOk();
    expect(User::where('mobile', '9876543210')->count())->toBe(1);
});

test('it rejects invalid OTP', function () {
    OtpVerification::create([
        'mobile' => '9876543210',
        'otp_hash' => Hash::make('123456'),
        'purpose' => 'login',
        'expires_at' => now()->addMinutes(10),
    ]);

    $response = $this->postJson('/api/v1/auth/otp/verify', [
        'mobile' => '9876543210',
        'otp' => '999999',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['otp']);
});

test('it rejects expired OTP', function () {
    OtpVerification::create([
        'mobile' => '9876543210',
        'otp_hash' => Hash::make('123456'),
        'purpose' => 'login',
        'expires_at' => now()->subMinute(),
    ]);

    $response = $this->postJson('/api/v1/auth/otp/verify', [
        'mobile' => '9876543210',
        'otp' => '123456',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['otp']);
});

test('it rejects OTP after max attempts', function () {
    OtpVerification::create([
        'mobile' => '9876543210',
        'otp_hash' => Hash::make('123456'),
        'purpose' => 'login',
        'expires_at' => now()->addMinutes(10),
        'attempts' => 3,
    ]);

    $response = $this->postJson('/api/v1/auth/otp/verify', [
        'mobile' => '9876543210',
        'otp' => '123456',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['otp']);
});
