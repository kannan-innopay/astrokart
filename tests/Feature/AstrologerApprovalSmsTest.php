<?php

use App\Enums\AstrologerStatus;
use App\Jobs\SendAstrologerApprovedSms;
use App\Models\Astrologer;
use App\Services\AstrologerService;
use App\Services\Contracts\SmsSenderInterface;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->service = app(AstrologerService::class);
});

test('approving an astrologer dispatches the approval sms job', function () {
    Queue::fake();

    $astrologer = Astrologer::factory()->pendingVerification()->create();

    $this->service->updateStatus($astrologer, AstrologerStatus::Approved);

    Queue::assertPushed(SendAstrologerApprovedSms::class, fn ($job) => $job->astrologer->is($astrologer));
});

test('it does not send sms for non-approval status changes', function () {
    Queue::fake();

    $astrologer = Astrologer::factory()->pendingVerification()->create();

    $this->service->updateStatus($astrologer, AstrologerStatus::Rejected, 'Incomplete documents.');

    Queue::assertNotPushed(SendAstrologerApprovedSms::class);
});

test('it does not resend sms when an already-approved astrologer is updated', function () {
    Queue::fake();

    $astrologer = Astrologer::factory()->approved()->create();

    $this->service->updateStatus($astrologer, AstrologerStatus::Approved, 'Re-checked.');

    Queue::assertNotPushed(SendAstrologerApprovedSms::class);
});

test('the job sends an sms with the astrologer first name', function () {
    $astrologer = Astrologer::factory()->approved()->create();
    $astrologer->user->update(['name' => 'Ravi Shankar', 'mobile' => '9876543210']);

    $sms = Mockery::mock(SmsSenderInterface::class);
    $sms->shouldReceive('send')
        ->once()
        ->withArgs(fn (string $mobile, string $template, array $vars) => $mobile === '9876543210' && $vars['var1'] === 'Ravi')
        ->andReturnTrue();

    (new SendAstrologerApprovedSms($astrologer->fresh('user')))->handle($sms);
});
