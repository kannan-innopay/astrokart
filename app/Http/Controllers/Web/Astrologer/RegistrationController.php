<?php

namespace App\Http\Controllers\Web\Astrologer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Astrologer\RegisterAstrologerRequest;
use App\Http\Requests\Auth\RequestOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Models\Expertise;
use App\Models\Language;
use App\Services\AstrologerService;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function __construct(
        private AuthService $authService,
        private AstrologerService $astrologerService,
    ) {}

    /**
     * Step 1 — mobile verification. Logged-in users skip straight to the
     * appropriate step.
     */
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()) {
            return $this->redirectForUser($request);
        }

        return view('astrologer.register.mobile');
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

        return $this->redirectForUser($request);
    }

    /**
     * Step 2 — professional details, photos, KYC and bank details.
     */
    public function profile(Request $request): View|RedirectResponse
    {
        if ($request->user()->astrologerProfile) {
            return redirect()->route('astrologer.register.status');
        }

        return view('astrologer.register.profile', [
            'expertises' => Expertise::where('is_active', true)->orderBy('name')->get(),
            'languages' => Language::orderBy('name')->get(),
        ]);
    }

    public function store(RegisterAstrologerRequest $request): RedirectResponse
    {
        if ($request->user()->astrologerProfile) {
            return redirect()->route('astrologer.register.status');
        }

        $this->astrologerService->register($request->user(), $request->validated());

        return redirect()->route('astrologer.register.status')
            ->with('success', 'Your astrologer application has been submitted for verification.');
    }

    /**
     * Final step — application status (pending / approved / rejected).
     */
    public function status(Request $request): View|RedirectResponse
    {
        $astrologer = $request->user()->astrologerProfile;

        if (! $astrologer) {
            return redirect()->route('astrologer.register.profile');
        }

        return view('astrologer.register.status', [
            'astrologer' => $astrologer,
        ]);
    }

    private function redirectForUser(Request $request): RedirectResponse
    {
        if ($request->user()->astrologerProfile) {
            return redirect()->route('astrologer.register.status');
        }

        return redirect()->route('astrologer.register.profile');
    }
}
