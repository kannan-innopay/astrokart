<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Astrologer\ApplyAsAstrologerRequest;
use App\Http\Requests\Astrologer\UpdateAstrologerProfileRequest;
use App\Http\Requests\Astrologer\UpdateAvailabilityRequest;
use App\Http\Resources\V1\AstrologerListResource;
use App\Http\Resources\V1\AstrologerResource;
use App\Models\Astrologer;
use App\Services\AstrologerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AstrologerController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $astrologers = $this->astrologerService->listApproved(
            $request->only([
                'is_online', 'expertise_id', 'language_id',
                'min_price', 'max_price', 'min_rating',
                'min_experience', 'sort_by', 'sort_direction',
            ]),
            $request->integer('per_page', 15),
        );

        return AstrologerListResource::collection($astrologers);
    }

    public function show(Astrologer $astrologer): AstrologerResource
    {
        return new AstrologerResource(
            $astrologer->load(['user', 'expertises', 'languages']),
        );
    }

    public function apply(ApplyAsAstrologerRequest $request): JsonResponse
    {
        $astrologer = $this->astrologerService->apply(
            $request->user(),
            $request->validated(),
        );

        return (new AstrologerResource($astrologer))
            ->response()
            ->setStatusCode(201);
    }

    public function profile(Request $request): AstrologerResource
    {
        return new AstrologerResource(
            $request->user()->astrologerProfile->load(['user', 'expertises', 'languages']),
        );
    }

    public function updateProfile(UpdateAstrologerProfileRequest $request): AstrologerResource
    {
        $astrologer = $this->astrologerService->updateProfile(
            $request->user()->astrologerProfile,
            $request->validated(),
        );

        return new AstrologerResource($astrologer);
    }

    public function updateAvailability(UpdateAvailabilityRequest $request): JsonResponse
    {
        $this->astrologerService->updateAvailability(
            $request->user()->astrologerProfile,
            $request->validated('slots'),
        );

        return response()->json(['message' => 'Availability updated successfully.']);
    }

    public function goOnline(Request $request): AstrologerResource
    {
        $astrologer = $this->astrologerService->goOnline(
            $request->user()->astrologerProfile,
        );

        return new AstrologerResource($astrologer->load(['user', 'expertises', 'languages']));
    }

    public function goOffline(Request $request): AstrologerResource
    {
        $astrologer = $this->astrologerService->goOffline(
            $request->user()->astrologerProfile,
        );

        return new AstrologerResource($astrologer->load(['user', 'expertises', 'languages']));
    }
}
