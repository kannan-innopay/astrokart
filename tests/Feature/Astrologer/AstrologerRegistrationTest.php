<?php

use App\Models\Expertise;
use App\Models\Language;
use App\Models\OtpVerification;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

function validRegistrationPayload(array $overrides = []): array
{
    $expertise = Expertise::factory()->create();
    $language = Language::factory()->create();

    return array_merge([
        'name' => 'Guru Astro',
        'bio' => 'Vedic astrologer with deep experience.',
        'years_of_experience' => 12,
        'price_per_minute' => 2500,
        'consultation_modes' => ['chat', 'call'],
        'expertise_ids' => [$expertise->id],
        'language_ids' => [$language->id],
        'photos' => [UploadedFile::fake()->image('me1.jpg'), UploadedFile::fake()->image('me2.jpg')],
    ], $overrides);
}

test('the astrologer signup page is publicly reachable', function () {
    $this->get(route('astrologer.register.show'))
        ->assertOk()
        ->assertSee('Join as an Astrologer');
});

test('mobile verification logs the user in and sends them to the profile step', function () {
    OtpVerification::create([
        'mobile' => '9876543210',
        'otp_hash' => Hash::make('123456'),
        'purpose' => 'login',
        'expires_at' => now()->addMinutes(10),
    ]);

    $this->post(route('astrologer.register.otp.verify'), [
        'mobile' => '9876543210',
        'otp' => '123456',
    ])->assertRedirect(route('astrologer.register.profile'));

    $this->assertAuthenticated();
});

test('an astrologer can complete registration with photos, kyc and bank details', function () {
    Storage::fake('public');
    Storage::fake('local');

    $user = User::factory()->customer()->create();

    $response = $this->actingAs($user)->post(route('astrologer.register.store'), validRegistrationPayload([
        'aadhaar_number' => '123412341234',
        'aadhaar_file' => UploadedFile::fake()->create('aadhaar.pdf', 200, 'application/pdf'),
        'pan_number' => 'ABCDE1234F',
        'pan_file' => UploadedFile::fake()->image('pan.jpg'),
        'certificate_file' => UploadedFile::fake()->create('cert.pdf', 200, 'application/pdf'),
        'bank_account_name' => 'Guru Astro',
        'bank_account_number' => '000111222333',
        'bank_ifsc_code' => 'HDFC0001234',
        'upi_id' => 'guru@upi',
    ]));

    $response->assertRedirect(route('astrologer.register.status'));

    $user->refresh();
    expect($user->role->value)->toBe('astrologer');

    $astrologer = $user->astrologerProfile;
    expect($astrologer->status->value)->toBe('applied');
    expect($astrologer->bank_ifsc_code)->toBe('HDFC0001234');
    expect($astrologer->photos)->toHaveCount(2);
    expect($astrologer->photos->firstWhere('is_primary', true))->not->toBeNull();
    expect($astrologer->documents)->toHaveCount(3);

    Storage::disk('public')->assertExists($astrologer->photos->first()->file_path);
    Storage::disk('local')->assertExists($astrologer->documents->first()->file_path);
});

test('registration works without any kyc (kyc is optional)', function () {
    Storage::fake('public');

    $user = User::factory()->customer()->create();

    $this->actingAs($user)
        ->post(route('astrologer.register.store'), validRegistrationPayload())
        ->assertRedirect(route('astrologer.register.status'));

    expect($user->fresh()->astrologerProfile->documents)->toHaveCount(0);
});

test('price entered in rupees is stored in paise', function () {
    Storage::fake('public');

    $user = User::factory()->customer()->create();

    $this->actingAs($user)
        ->post(route('astrologer.register.store'), validRegistrationPayload(['price_per_minute' => 5]));

    expect($user->fresh()->astrologerProfile->price_per_minute)->toBe(500);
});

test('the signup pages can be localized via the lang query parameter', function () {
    // Guest mobile/OTP step in Hindi
    $this->get(route('astrologer.register.show', ['lang' => 'hi']))
        ->assertOk()
        ->assertSee('ज्योतिषी के रूप में जुड़ें');

    // Authenticated profile wizard in Tamil
    $user = User::factory()->customer()->create();
    $this->actingAs($user)
        ->get(route('astrologer.register.profile', ['lang' => 'ta']))
        ->assertOk()
        ->assertSee('ஜோதிடர் விண்ணப்பம்');
});

test('at least one profile photo is required', function () {
    $user = User::factory()->customer()->create();

    $this->actingAs($user)
        ->post(route('astrologer.register.store'), validRegistrationPayload(['photos' => []]))
        ->assertSessionHasErrors('photos');
});

test('it validates required professional details', function () {
    $user = User::factory()->customer()->create();

    $this->actingAs($user)
        ->post(route('astrologer.register.store'), [])
        ->assertSessionHasErrors(['name', 'years_of_experience', 'price_per_minute', 'expertise_ids', 'language_ids', 'photos']);
});

test('a user who already has an astrologer profile is sent to status', function () {
    Storage::fake('public');

    $user = User::factory()->customer()->create();
    $this->actingAs($user)->post(route('astrologer.register.store'), validRegistrationPayload());

    $this->actingAs($user->fresh())
        ->get(route('astrologer.register.profile'))
        ->assertRedirect(route('astrologer.register.status'));
});
