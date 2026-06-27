<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\AccountStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        // Only fully-onboarded astrologers (those with a profile) live in the
        // Astrologers section. Incomplete astrologer signups stay visible here.
        $query = User::whereDoesntHave('astrologerProfile');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        return view('admin.users.index', [
            'users' => $query->latest()->paginate(15),
        ]);
    }

    public function show(User $user): View|RedirectResponse
    {
        // A fully-onboarded astrologer is managed in the Astrologers section.
        if ($astrologer = $user->astrologerProfile) {
            return redirect()->route('admin.astrologers.show', $astrologer);
        }

        return view('admin.users.show', [
            'user' => $user->load(['wallet', 'astrologerProfile']),
        ]);
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'account_status' => ['required', 'in:active,suspended,deactivated'],
        ]);

        $user->update(['account_status' => AccountStatus::from($request->input('account_status'))]);

        return back()->with('success', 'User status updated.');
    }

    /**
     * Clean up an abandoned astrologer signup — a user who took the astrologer
     * role at the OTP step but never completed a profile.
     */
    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->isAstrologer() && ! $user->astrologerProfile, 403);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Incomplete astrologer signup deleted.');
    }
}
