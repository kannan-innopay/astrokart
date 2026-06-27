<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\AstrologerStatus;
use App\Enums\ConsultationMode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAstrologerStatusRequest;
use App\Models\Astrologer;
use App\Models\AstrologerDocument;
use App\Models\Expertise;
use App\Models\Language;
use App\Services\AstrologerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AstrologerManagementController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
    ) {}

    public function index(Request $request): View
    {
        $query = Astrologer::with(['user', 'expertises']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        return view('admin.astrologers.index', [
            'astrologers' => $query->latest()->paginate(15),
        ]);
    }

    public function show(Astrologer $astrologer): View
    {
        return view('admin.astrologers.show', [
            'astrologer' => $astrologer->load(['user', 'salesUser', 'expertises', 'languages', 'documents', 'photos', 'availabilities']),
        ]);
    }

    public function edit(Astrologer $astrologer): View
    {
        return view('admin.astrologers.edit', [
            'astrologer' => $astrologer->load(['user', 'expertises', 'languages']),
            'allExpertises' => Expertise::where('is_active', true)->orderBy('name')->get(),
            'allLanguages' => Language::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Astrologer $astrologer): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'years_of_experience' => ['required', 'integer', 'min:0', 'max:100'],
            'price_per_minute' => ['required', 'integer', 'min:5'], // captured in rupees
            'consultation_modes' => ['required', 'array', 'min:1'],
            'consultation_modes.*' => [Rule::enum(ConsultationMode::class)],
            'expertise_ids' => ['required', 'array', 'min:1'],
            'expertise_ids.*' => ['integer', 'exists:expertises,id'],
            'language_ids' => ['required', 'array', 'min:1'],
            'language_ids.*' => ['integer', 'exists:languages,id'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:255'],
            'bank_ifsc_code' => ['nullable', 'string', 'max:11'],
            'upi_id' => ['nullable', 'string', 'max:255'],
        ]);

        $astrologer->user->update(['name' => $validated['name']]);

        $this->astrologerService->updateProfile($astrologer, [
            ...$validated,
            // UI captures rupees; the money system stores paise.
            'price_per_minute' => (int) $validated['price_per_minute'] * 100,
        ]);

        return redirect()->route('admin.astrologers.show', $astrologer)->with('success', 'Astrologer updated.');
    }

    public function destroy(Astrologer $astrologer): RedirectResponse
    {
        $this->astrologerService->purgeFiles($astrologer);

        // Removing the user cascades the astrologer profile, photos and documents.
        $astrologer->user->delete();

        return redirect()->route('admin.astrologers.index')->with('success', 'Astrologer deleted.');
    }

    public function updateStatus(UpdateAstrologerStatusRequest $request, Astrologer $astrologer): RedirectResponse
    {
        $this->astrologerService->updateStatus(
            $astrologer,
            AstrologerStatus::from($request->validated('status')),
            $request->validated('notes'),
        );

        return back()->with('success', 'Astrologer status updated.');
    }

    public function downloadDocument(AstrologerDocument $document): StreamedResponse
    {
        abort_unless(Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->download($document->file_path);
    }
}
