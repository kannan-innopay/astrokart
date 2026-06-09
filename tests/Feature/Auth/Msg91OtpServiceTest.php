<?php

use App\Services\Msg91OtpService;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    config([
        'services.msg91.auth_key' => 'test-auth-key',
        'services.msg91.sender_id' => 'TESTER',
        'services.msg91.templates' => [
            'login' => 'login-template-id',
            'registration' => 'registration-template-id',
        ],
        'services.msg91.otp_length' => 6,
        'services.msg91.otp_expiry' => 10,
        'services.msg91.country_code' => '91',
        'services.msg91.test_phone' => '7777777777',
        'services.msg91.test_otp' => '123456',
    ]);

    Http::preventStrayRequests();
});

test('it sends OTP successfully via MSG91', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp' => Http::response([
            'type' => 'success',
            'request_id' => 'abc123',
        ]),
    ]);

    $service = new Msg91OtpService;
    $result = $service->sendOtp('9876543210');

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://control.msg91.com/api/v5/otp'
            && $request->method() === 'POST'
            && $request->header('authkey')[0] === 'test-auth-key'
            && $request['template_id'] === 'login-template-id'
            && $request['mobile'] === '919876543210'
            && $request['otp_length'] === 6
            && $request['otp_expiry'] === 10;
    });
});

test('it uses registration template for registration purpose', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp' => Http::response(['type' => 'success']),
    ]);

    $service = new Msg91OtpService;
    $service->sendOtp('9876543210', 'registration');

    Http::assertSent(fn ($request) => $request['template_id'] === 'registration-template-id');
});

test('it prepends country code to mobile number', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp' => Http::response(['type' => 'success']),
    ]);

    $service = new Msg91OtpService;
    $service->sendOtp('9876543210');

    Http::assertSent(fn ($request) => $request['mobile'] === '919876543210');
});

test('it does not double-prepend country code', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp' => Http::response(['type' => 'success']),
    ]);

    $service = new Msg91OtpService;
    $service->sendOtp('919876543210');

    Http::assertSent(fn ($request) => $request['mobile'] === '919876543210');
});

test('it returns false when MSG91 send returns error', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp' => Http::response([
            'type' => 'error',
            'message' => 'Invalid template ID',
        ]),
    ]);

    $service = new Msg91OtpService;
    $result = $service->sendOtp('9876543210');

    expect($result)->toBeFalse();
});

test('it returns false when MSG91 send request fails', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp' => Http::response([], 500),
    ]);

    $service = new Msg91OtpService;
    $result = $service->sendOtp('9876543210');

    expect($result)->toBeFalse();
});

test('it verifies OTP successfully via MSG91', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp/verify*' => Http::response([
            'type' => 'success',
            'message' => 'OTP verified success',
        ]),
    ]);

    $service = new Msg91OtpService;
    $result = $service->verifyOtp('9876543210', '123456');

    expect($result)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'control.msg91.com/api/v5/otp/verify')
            && $request->method() === 'GET'
            && $request->header('authkey')[0] === 'test-auth-key';
    });
});

test('it returns false when OTP verification fails', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp/verify*' => Http::response([
            'type' => 'error',
            'message' => 'OTP not match',
        ]),
    ]);

    $service = new Msg91OtpService;
    $result = $service->verifyOtp('9876543210', '999999');

    expect($result)->toBeFalse();
});

test('it returns false when OTP has expired', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp/verify*' => Http::response([
            'type' => 'error',
            'message' => 'OTP expired',
        ]),
    ]);

    $service = new Msg91OtpService;
    $result = $service->verifyOtp('9876543210', '123456');

    expect($result)->toBeFalse();
});

test('it returns false when verify request fails', function () {
    Http::fake([
        'control.msg91.com/api/v5/otp/verify*' => Http::response([], 500),
    ]);

    $service = new Msg91OtpService;
    $result = $service->verifyOtp('9876543210', '123456');

    expect($result)->toBeFalse();
});

// Test phone bypass tests

test('it skips API call for test phone on send', function () {
    Http::fake();

    $service = new Msg91OtpService;
    $result = $service->sendOtp('7777777777');

    expect($result)->toBeTrue();
    Http::assertNothingSent();
});

test('it verifies test phone with correct test OTP', function () {
    Http::fake();

    $service = new Msg91OtpService;
    $result = $service->verifyOtp('7777777777', '123456');

    expect($result)->toBeTrue();
    Http::assertNothingSent();
});

test('it rejects test phone with wrong OTP', function () {
    Http::fake();

    $service = new Msg91OtpService;
    $result = $service->verifyOtp('7777777777', '999999');

    expect($result)->toBeFalse();
    Http::assertNothingSent();
});
