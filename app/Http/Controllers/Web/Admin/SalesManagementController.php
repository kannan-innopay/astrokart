<?php

namespace App\Http\Controllers\Web\Admin;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SalesManagementController extends Controller
{
    public function index(): View
    {
        return view('admin.sales.index', [
            'salesUsers' => User::where('role', UserRole::Sales)
                ->withCount('referredAstrologers')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.sales.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'mobile' => ['nullable', 'string', 'regex:/^[6-9]\d{9}$/', 'unique:users,mobile'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'mobile' => $validated['mobile'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => UserRole::Sales,
            'account_status' => AccountStatus::Active,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.sales.index')->with('success', 'Sales user created.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->isSales(), 403);

        $user->delete();

        return redirect()->route('admin.sales.index')->with('success', 'Sales user removed.');
    }
}
