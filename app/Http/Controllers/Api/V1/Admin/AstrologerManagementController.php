<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Enums\AstrologerStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAstrologerStatusRequest;
use App\Http\Resources\V1\AstrologerResource;
use App\Models\Astrologer;
use App\Services\AstrologerService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AstrologerManagementController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Astrologer::with(['user', 'expertises', 'languages']);

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        return AstrologerResource::collection(
            $query->latest()->paginate($request->integer('per_page', 15)),
        );
    }

    public function show(Astrologer $astrologer): AstrologerResource
    {
        return new AstrologerResource(
            $astrologer->load(['user', 'expertises', 'languages', 'documents']),
        );
    }

    public function updateStatus(UpdateAstrologerStatusRequest $request, Astrologer $astrologer): AstrologerResource
    {
        $astrologer = $this->astrologerService->updateStatus(
            $astrologer,
            AstrologerStatus::from($request->validated('status')),
            $request->validated('notes'),
        );

        return new AstrologerResource($astrologer);
    }
}
