<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use App\Models\Expertise;
use App\Models\Language;
use App\Services\AstrologerService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AstrologerBrowseController extends Controller
{
    public function __construct(
        private AstrologerService $astrologerService,
    ) {}

    public function index(Request $request): View
    {
        if (! config('app.features.astrologers')) {
            return view('customer.astrologers.coming-soon');
        }

        $astrologers = $this->astrologerService->listApproved(
            $request->only([
                'is_online', 'expertise_id', 'language_id',
                'min_price', 'max_price', 'min_rating',
                'min_experience', 'sort_by', 'sort_direction',
            ]),
            12,
        );

        return view('customer.astrologers.index', [
            'astrologers' => $astrologers,
            'expertises' => Expertise::where('is_active', true)->orderBy('name')->get(),
            'languages' => Language::orderBy('name')->get(),
        ]);
    }

    public function show(Astrologer $astrologer): View
    {
        if (! config('app.features.astrologers')) {
            return view('customer.astrologers.coming-soon');
        }

        return view('customer.astrologers.show', [
            'astrologer' => $astrologer->load(['user', 'expertises', 'languages', 'availabilities']),
        ]);
    }
}
