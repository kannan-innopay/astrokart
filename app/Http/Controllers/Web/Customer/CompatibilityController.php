<?php

namespace App\Http\Controllers\Web\Customer;

use App\Http\Controllers\Controller;
use App\Models\CompatibilityReport;
use App\Services\CompatibilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompatibilityController extends Controller
{
    public function __construct(
        private CompatibilityService $compatibilityService,
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        $history = CompatibilityReport::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('customer.compatibility.index', [
            'walletBalance' => $user->wallet?->balance ?? 0,
            'isPremium' => $user->isPremium(),
            'hasChart' => (bool) $user->birth_chart,
            'history' => $history,
        ]);
    }

    public function match(Request $request): RedirectResponse
    {
        $request->validate([
            'partner_name' => ['nullable', 'string', 'max:100'],
            'moon_nakshatra' => ['required', 'integer', 'min:0', 'max:26'],
            'moon_rashi' => ['required', 'integer', 'min:0', 'max:11'],
        ]);

        try {
            $report = $this->compatibilityService->match(
                $request->user(),
                [
                    'name' => $request->input('partner_name'),
                    'moon_nakshatra' => $request->input('moon_nakshatra'),
                    'moon_rashi' => $request->input('moon_rashi'),
                ],
            );

            return redirect()->route('compatibility.show', $report)
                ->with('success', 'Compatibility report generated.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function show(Request $request, CompatibilityReport $compatibilityReport): View
    {
        abort_unless($compatibilityReport->user_id === $request->user()->id, 403);

        return view('customer.compatibility.show', [
            'report' => $compatibilityReport,
        ]);
    }
}
