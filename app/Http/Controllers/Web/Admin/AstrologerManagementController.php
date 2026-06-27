<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\AstrologerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAstrologerStatusRequest;
use App\Models\Astrologer;
use App\Models\AstrologerDocument;
use App\Services\AstrologerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
