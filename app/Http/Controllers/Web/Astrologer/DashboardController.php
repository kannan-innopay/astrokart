<?php

namespace App\Http\Controllers\Web\Astrologer;

use App\Enums\ConsultationStatus;
use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $astrologer = $request->user()->astrologerProfile->load(['expertises', 'languages']);

        // Fetch any pending consultation requests for this astrologer
        $pendingRequests = Consultation::where('astrologer_id', $astrologer->id)
            ->where('status', ConsultationStatus::Pending)
            ->with('user:id,name,mobile')
            ->latest()
            ->get();

        // Fetch active consultation if any
        $activeConsultation = Consultation::where('astrologer_id', $astrologer->id)
            ->where('status', ConsultationStatus::Active)
            ->with('user:id,name')
            ->first();

        return view('astrologer.dashboard', [
            'astrologer' => $astrologer,
            'pendingRequests' => $pendingRequests,
            'activeConsultation' => $activeConsultation,
        ]);
    }
}
