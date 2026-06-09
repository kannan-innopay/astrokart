<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\Language;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private UserService $userService,
    ) {}

    public function edit(Request $request): View
    {
        return view('customer.profile.edit', [
            'user' => $request->user(),
            'languages' => Language::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->userService->updateProfile(
            $request->user(),
            $request->validated(),
        );

        return back()->with('success', 'Profile updated successfully.');
    }
}
