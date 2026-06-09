<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Payment;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    private string $baseUrl;

    private string $token;

    private string $userUuid;

    private string $paymentModes;

    public function __construct()
    {
        $this->baseUrl = config('services.switchpay.base_url', 'https://www.switchpay.in');
        $this->token = config('services.switchpay.token', '');
        $this->userUuid = config('services.switchpay.user_uuid', '');
        $this->paymentModes = config('services.switchpay.payment_modes', 'cc|dc|upi|nb');
    }

    /**
     * @return array{payment: Payment, redirect_url: string}
     */
    public function initiateRecharge(User $user, int $amountPaise, string $callbackUrl): array
    {
        $amountRupees = $amountPaise / 100;

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amountPaise,
            'status' => PaymentStatus::Initiated,
            'remarks' => 'Wallet recharge ₹' . number_format($amountRupees, 2),
        ]);

        try {
            $payload = [
                'amount' => $amountRupees,
                'description' => 'Astrokart Wallet Recharge',
                'name' => $user->name,
                'email' => $user->email ?? ($user->mobile . '@astrokart.app'),
                'mobile' => $user->mobile ?? '',
                'user_uuid' => $this->userUuid,
                'enabledModesOfPayment' => $this->paymentModes,
                'payment_method' => 'REGULAR',
                'callback_url' => $callbackUrl,
                'source' => 'api',
            ];

            Log::info('SwitchPay request', [
                'url' => "{$this->baseUrl}/api/createTransaction",
                'payload' => $payload,
            ]);

            $response = Http::timeout(15)
                ->connectTimeout(5)
                ->withToken($this->token)
                ->withOptions(['allow_redirects' => false])
                ->asForm()
                ->post("{$this->baseUrl}/api/createTransaction", $payload);

            Log::info('SwitchPay response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => substr($response->body(), 0, 500),
            ]);

            $redirectUrl = $response->header('Location');

            // SwitchPay returns HTML with an auto-submit form — extract the action URL
            if (! $redirectUrl) {
                $body = $response->body();

                // Try JSON response first
                $json = $response->json();
                if ($json) {
                    $redirectUrl = $json['payment_url'] ?? $json['url'] ?? $json['redirect_url'] ?? null;
                }

                // Extract form action URL from HTML: action='https://...'
                if (! $redirectUrl && preg_match("/action=['\"]([^'\"]+)['\"]/", $body, $matches)) {
                    $redirectUrl = $matches[1];
                }
            }

            if (! $redirectUrl) {
                Log::error('SwitchPay: no redirect URL in response', ['body' => substr($response->body(), 0, 500)]);
                throw new \RuntimeException('No payment URL received from SwitchPay.');
            }

            $payment->update(['status' => PaymentStatus::Pending]);

            return [
                'payment' => $payment,
                'redirect_url' => $redirectUrl,
            ];
        } catch (\Throwable $e) {
            Log::error('SwitchPay initiation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            $payment->update([
                'status' => PaymentStatus::Failed,
                'remarks' => 'Payment initiation failed: ' . $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function handleCallback(string $transactionDataJson): Payment
    {
        $data = json_decode($transactionDataJson, true);

        $orderId = $data['order_id'] ?? null;
        $status = $data['status'] ?? 'failed';
        $pgPayload = $data['pg_payload'] ?? null;

        Log::info('SwitchPay callback received', [
            'order_id' => $orderId,
            'status' => $status,
        ]);

        // Find payment — try by order_id first, fall back to most recent pending
        $payment = null;
        if ($orderId) {
            $payment = Payment::where('order_id', $orderId)->first();
        }

        if (! $payment) {
            // SwitchPay may not have set order_id on our side yet — find the latest pending payment
            $payment = Payment::where('status', PaymentStatus::Pending)
                ->orWhere('status', PaymentStatus::Initiated)
                ->latest()
                ->first();
        }

        if (! $payment) {
            Log::error('SwitchPay callback: no matching payment found', ['order_id' => $orderId]);

            throw new \RuntimeException('Payment not found for callback.');
        }

        // Update payment record
        $payment->update([
            'order_id' => $orderId,
            'status' => in_array(strtolower($status), ['success', 'captured']) ? PaymentStatus::Success : PaymentStatus::Failed,
            'gateway_response' => $data,
        ]);

        // Credit wallet on success
        if ($payment->status === PaymentStatus::Success) {
            $this->creditWallet($payment);
        }

        return $payment;
    }

    private function creditWallet(Payment $payment): void
    {
        $user = $payment->user;
        $wallet = $user->wallet;

        if (! $wallet) {
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
            ]);
        }

        $balanceBefore = $wallet->balance;
        $wallet->credit($payment->amount);

        WalletTransaction::create([
            'user_id' => $user->id,
            'type' => TransactionType::Credit,
            'amount' => $payment->amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $wallet->fresh()->balance,
            'reference_type' => 'payment',
            'reference_id' => $payment->id,
            'status' => TransactionStatus::Completed,
            'remarks' => 'Wallet recharge via SwitchPay',
        ]);
    }
}
