<?php

namespace App\Http\Controllers\Web\Astrologer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Astrologer\UpdateAstrologerProfileRequest;
use App\Models\Expertise;
use App\Models\Language;
use App\Services\AstrologerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
    ) {}

    public function edit(Request $request): View
    {
        return view('astrologer.profile.edit', [
            'astrologer' => $request->user()->astrologerProfile->load(['expertises', 'languages']),
            'allExpertises' => Expertise::where('is_active', true)->orderBy('name')->get(),
            'allLanguages' => Language::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateAstrologerProfileRequest $request): RedirectResponse
    {
        $this->astrologerService->updateProfile(
            $request->user()->astrologerProfile,
            $request->validated(),
        );

        return back()->with('success', 'Profile updated successfully.');
    }
}
