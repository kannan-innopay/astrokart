<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\ChatMessage;
use App\Models\Consultation;
use App\Services\ConsultationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConsultationController extends Controller
{
    public function __construct(
        private ConsultationService $consultationService,
    ) {}

    public function start(Request $request, Astrologer $astrologer): RedirectResponse
    {
        $consultation = $this->consultationService->requestConsultation($request->user(), $astrologer);

        return redirect()->route('consultation.chat', $consultation)
            ->with('success', 'Consultation request sent. Waiting for astrologer to accept...');
    }

    public function chat(Request $request, Consultation $consultation): View
    {
        $user = $request->user();

        // Ensure user is a participant
        if ($user->id !== $consultation->user_id && $user->id !== $consultation->astrologer->user_id) {
            abort(403);
        }

        return view('customer.consultation.chat', [
            'consultation' => $consultation->load(['user', 'astrologer.user']),
            'messages' => $consultation->messages()->with('sender:id,name')->oldest()->get(),
            'currentUser' => $user,
        ]);
    }

    public function send(Request $request, Consultation $consultation): \Illuminate\Http\JsonResponse
    {
        $request->validate(['message' => ['required', 'string', 'max:2000']]);

        $chatMessage = $this->consultationService->sendMessage(
            $consultation,
            $request->user(),
            $request->input('message'),
        );

        return response()->json([
            'id' => $chatMessage->id,
            'message' => $chatMessage->message,
            'sender_id' => $chatMessage->sender_id,
            'sender_name' => $request->user()->name,
            'created_at' => $chatMessage->created_at->toIso8601String(),
        ]);
    }

    public function chartView(Request $request, Consultation $consultation, ChatMessage $message): View|RedirectResponse
    {
        $user = $request->user();

        // Must be a participant
        if ($user->id !== $consultation->user_id && $user->id !== $consultation->astrologer->user_id) {
            abort(403);
        }

        // Link expires when consultation ends
        if ($consultation->isEnded()) {
            return redirect()->route('consultations.history')
                ->with('error', 'This chart link has expired because the consultation has ended.');
        }

        // Must be a chart_shared message for this consultation
        if ($message->consultation_id !== $consultation->id || $message->type !== 'chart_shared') {
            abort(404);
        }

        return view('customer.consultation.chart-fullview', [
            'consultation' => $consultation,
            'chartData' => $message->metadata,
            'message' => $message,
        ]);
    }

    public function requestChart(Request $request, Consultation $consultation): \Illuminate\Http\JsonResponse
    {
        $this->consultationService->requestBirthChart($consultation, $request->user());

        return response()->json(['message' => 'Birth chart request sent.']);
    }

    public function shareChart(Request $request, Consultation $consultation): \Illuminate\Http\JsonResponse
    {
        $this->consultationService->shareBirthChart($consultation, $request->user());

        return response()->json(['message' => 'Birth chart shared.']);
    }

    public function end(Request $request, Consultation $consultation): RedirectResponse
    {
        $endedBy = $request->user()->id === $consultation->user_id ? 'user' : 'astrologer';
        $this->consultationService->endConsultation($consultation, $endedBy);

        return redirect()->route('consultations.history')
            ->with('success', 'Consultation ended.');
    }

    public function history(Request $request): View
    {
        $user = $request->user();

        $consultations = Consultation::where(function ($q) use ($user) {
            $q->where('user_id', $user->id);
            if ($user->astrologerProfile) {
                $q->orWhere('astrologer_id', $user->astrologerProfile->id);
            }
        })
            ->with(['user:id,name', 'astrologer.user:id,name'])
            ->latest()
            ->paginate(15);

        return view('customer.consultation.history', [
            'consultations' => $consultations,
        ]);
    }

    public function rate(Request $request, Consultation $consultation): RedirectResponse
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->consultationService->rateConsultation(
            $consultation,
            $request->integer('rating'),
            $request->input('review'),
        );

        return back()->with('success', 'Thank you for your review!');
    }
}
