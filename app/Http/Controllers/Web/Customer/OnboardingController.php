<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\Language;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function show(): View
    {
        return view('customer.onboarding', [
            'languages' => Language::orderBy('name')->get(),
        ]);
    }

    public function store(UpdateProfileRequest $request): RedirectResponse
    {
        $this->userService->updateProfile(
            $request->user(),
            $request->validated(),
        );

        return redirect()->route('home')
            ->with('success', 'Welcome to Astrokart! Your profile is set up.');
    }
}
