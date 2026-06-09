<?php

namespace App\Http\Controllers\Web\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Services\ConsultationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function __construct(
        private ConsultationService $consultationService,
    ) {}

    public function accept(Request $request, Consultation $consultation): RedirectResponse
    {
        $this->consultationService->acceptConsultation($consultation);

        return redirect()->route('consultation.chat', $consultation)
            ->with('success', 'Consultation started!');
    }

    public function reject(Request $request, Consultation $consultation): RedirectResponse
    {
        $this->consultationService->rejectConsultation($consultation);

        return back()->with('success', 'Consultation declined.');
    }
}
