<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Models\WalletTransaction;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()
            ->take(20)
            ->get();

        return view('customer.wallet.index', [
            'wallet' => $user->wallet,
            'transactions' => $transactions,
        ]);
    }

    public function recharge(Request $request): RedirectResponse
    {
        $request->validate([
            'amount' => ['required', 'integer', 'min:1000'],
        ]);

        $amountPaise = $request->integer('amount');
        $callbackUrl = route('wallet.callback');

        try {
            $result = $this->paymentService->initiateRecharge(
                $request->user(),
                $amountPaise,
                $callbackUrl,
            );

            return redirect()->away($result['redirect_url']);
        } catch (\Throwable) {
            return back()->with('error', 'Payment initiation failed. Please try again.');
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        $transactionData = $request->input('transaction_data');

        if (! $transactionData) {
            return redirect()->route('wallet.index')
                ->with('error', 'Invalid payment response.');
        }

        try {
            $payment = $this->paymentService->handleCallback($transactionData);

            if ($payment->status->value === 'success') {
                return redirect()->route('wallet.index')
                    ->with('success', 'Wallet recharged with ₹' . number_format($payment->amount / 100, 2) . '!');
            }

            return redirect()->route('wallet.index')
                ->with('error', 'Payment failed. Please try again.');
        } catch (\Throwable) {
            return redirect()->route('wallet.index')
                ->with('error', 'Payment processing error. Contact support if amount was deducted.');
        }
    }
}
