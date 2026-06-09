<?php

namespace App\Http\Controllers\Web\Astrologer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Astrologer\UpdateAvailabilityRequest;
use App\Services\AstrologerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
    ) {}

    public function edit(Request $request): View
    {
        return view('astrologer.availability.edit', [
            'astrologer' => $request->user()->astrologerProfile,
            'slots' => $request->user()->astrologerProfile->availabilities->toArray(),
        ]);
    }

    public function update(UpdateAvailabilityRequest $request): RedirectResponse
    {
        $this->astrologerService->updateAvailability(
            $request->user()->astrologerProfile,
            $request->validated('slots'),
        );

        return back()->with('success', 'Availability updated successfully.');
    }
}
