<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Consultation;
use App\Services\ConsultationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function __construct(
        private ConsultationService $consultationService,
    ) {}

    public function request(Request $request): JsonResponse
    {
        $request->validate(['astrologer_id' => ['required', 'exists:astrologers,id']]);

        $astrologer = Astrologer::findOrFail($request->input('astrologer_id'));
        $consultation = $this->consultationService->requestConsultation($request->user(), $astrologer);

        return response()->json([
            'message' => 'Consultation requested.',
            'consultation_id' => $consultation->id,
        ], 201);
    }

    public function accept(Request $request, Consultation $consultation): JsonResponse
    {
        $this->consultationService->acceptConsultation($consultation);

        return response()->json(['message' => 'Consultation accepted.']);
    }

    public function reject(Request $request, Consultation $consultation): JsonResponse
    {
        $this->consultationService->rejectConsultation($consultation);

        return response()->json(['message' => 'Consultation rejected.']);
    }

    public function end(Request $request, Consultation $consultation): JsonResponse
    {
        $endedBy = $request->user()->id === $consultation->user_id ? 'user' : 'astrologer';

        $this->consultationService->endConsultation($consultation, $endedBy);

        return response()->json(['message' => 'Consultation ended.']);
    }

    public function sendMessage(Request $request, Consultation $consultation): JsonResponse
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
            'created_at' => $chatMessage->created_at->toIso8601String(),
        ]);
    }

    public function messages(Consultation $consultation): JsonResponse
    {
        $messages = $consultation->messages()
            ->with('sender:id,name')
            ->oldest()
            ->get();

        return response()->json(['data' => $messages]);
    }

    public function active(Request $request): JsonResponse
    {
        $consultation = Consultation::where('user_id', $request->user()->id)
            ->whereIn('status', ['pending', 'active'])
            ->with(['astrologer.user'])
            ->first();

        return response()->json(['data' => $consultation]);
    }

    public function history(Request $request): JsonResponse
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

        return response()->json($consultations);
    }

    public function rate(Request $request, Consultation $consultation): JsonResponse
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'review' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->consultationService->rateConsultation(
            $consultation,
            $request->input('rating'),
            $request->input('review'),
        );

        return response()->json(['message' => 'Thank you for your review.']);
    }
}
