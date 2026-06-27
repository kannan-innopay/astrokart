<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\EmailLoginRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    public function __construct(
        private AuthService $authService,
    ) {}

    public function showForm(): View
    {
        return view('auth.admin-login');
    }

    public function login(EmailLoginRequest $request): RedirectResponse
    {
        $user = $this->authService->authenticateWithEmail(
            $request->validated('email'),
            $request->validated('password'),
        );

        $destination = match (true) {
            $user->isAdmin() => 'admin.dashboard',
            $user->isSales() => 'sales.dashboard',
            default => null,
        };

        if ($destination === null) {
            throw ValidationException::withMessages([
                'email' => ['This account does not have staff access.'],
            ]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->route($destination);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
