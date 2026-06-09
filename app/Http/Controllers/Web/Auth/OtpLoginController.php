<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RequestOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OtpLoginController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function showForm(): View
    {
        return view('auth.otp-login');
    }

    public function requestOtp(RequestOtpRequest $request): RedirectResponse
    {
        $this->authService->requestOtp($request->validated('mobile'));

        return back()
            ->with('otp_sent', true)
            ->withInput();
    }

    public function verifyOtp(VerifyOtpRequest $request): RedirectResponse
    {
        $user = $this->authService->verifyOtpAndGetUser(
            $request->validated('mobile'),
            $request->validated('otp'),
        );

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        if ($this->isProfileIncomplete($user)) {
            return redirect()->route('onboarding');
        }

        if ($user->isAstrologer()) {
            return redirect()->intended(route('astrologer.dashboard'));
        }

        return redirect()->intended(route('home'));
    }

    private function isProfileIncomplete($user): bool
    {
        return ! $user->date_of_birth || ! $user->place_of_birth || $user->name === 'User';
    }
}
